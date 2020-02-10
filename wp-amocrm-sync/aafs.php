<?php
/**
 * Plugin Name: WpAmoCRMSync
 * Plugin URI: https://wordpress.org/plugins/wpamocrmsync
 * Description: Интеграция форм на сайте с AmoCRM
 * Version: 1.1
 * Author: Aleksandr Ivanov
 * License: MIT
 * Author URI: https://ai2c.ru
 */

namespace AI2C;
use AmoCRM\Client;

if (!defined('ABSPATH'))
    exit;
if (!defined('AAFS_PLUGIN_PATH'))
    define('AAFS_PLUGIN_PATH', dirname(__FILE__));
require_once AAFS_PLUGIN_PATH . '/vendor/autoload.php';
if (is_admin())
    require_once(dirname(__FILE__) . '/options.php');

class WpAmoCRMSync
{
    public static function createAmoLead($name, $email, $phone, $message = '')
    {
        try{
            $options = get_option('aafs_settings_name');
            $amo = new Client($options['subdomain'], $options['login'], $options['api_key']);
            $searchQuery = self::generateQuery($email,$phone);
            $contactArr = $amo->contact->apiList([
                'query' => $searchQuery,
                'limit_rows' => 1,
            ]);
            $contactArr = $contactArr[0];
            $contactId = $contactArr['id'];
            $contactLeads = $contactArr['linked_leads_id'];
            $namePattern = $options['name_pattern'];

            $lead = $amo->lead;
            $lead['name'] = self::parseName($namePattern,$name,$email,$phone);
            if (intval($options['pipeline_id']) != 0) {
                $lead['pipeline_id'] = intval($options['pipeline_id']);
            }
            $amoid = $lead->apiAdd();

            if($message != ''){
                $note = $amo->note;
                $note['element_id'] = $amoid;
                $note['element_type'] = 2;
                $note['note_type'] = 4;
                $noteStr = 'Сообщение клиента: '.$message.PHP_EOL;
                $note['text'] = $noteStr;
                $noteid = $note->apiAdd();
            }
            $contact = $amo->contact;
            $contact['name'] = $name != '' ? $name : 'Имя не указано';

            if (intval($options['phone_field_id']) != 0) {
                $contact->addCustomField(intval($options['phone_field_id']), [
                    [($phone != '' ? $phone : 'Не указан'), 'WORK']
                ]);
            }

            if (intval($options['email_field_id']) != 0) {
                $contact->addCustomField(intval($options['email_field_id']), [
                    [($email != '' ? $email : 'Не указан'), 'WORK']
                ]);
            }

            $contactLeads[] = (int)$amoid;
            $contact['linked_leads_id'] = $contactLeads;

            if (intval($contactId) == 0) {
                $contactId = $contact->apiAdd();
            } else {
                $contact->apiUpdate((int)$contactId, 'now');
            }
        }catch (\AmoCRM\Exception $e){
            //
        }
    }
    private static function parseName($namePattern,$name,$email,$phone){
        $namePattern = str_replace('#date#',date('d.m.Y'),$namePattern);
        $namePattern = str_replace('#name#',$name,$namePattern);
        $namePattern = str_replace('#email#',$email,$namePattern);
        $namePattern = str_replace('#phone#',$phone,$namePattern);
        return $namePattern;
    }
    private static function generateQuery($email,$phone){
        if ($email != '')
            return $email;
        if ($phone != '')
            return $phone;
        return '';
    }
}