<?php
namespace AI2C;
use AmoCRM\Client;

/**
 * Class WpAmoCRMSyncSettingsPage
 * @package AI2C
 */
class WpAmoCRMSyncSettingsPage
{
    /**
     * @var array|void
     */
    private $options;
    /**
     * @var object|bool
     */
    private $amo;
    /**
     * @var object|bool
     */
    private $amoInfo;

    /**
     * AmoCRMFormSyncSettingsPage constructor.
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        $this->options = get_option( 'aafs_settings_name' );
        try {
            $this->amo = new Client($this->options['subdomain'], $this->options['login'], $this->options['api_key']);
            $account = $this->amo->account;
            $this->amoInfo = $account->apiCurrent();
        }catch(\AmoCRM\Exception $e){
            $this->amo = false;
            $this->amoInfo = false;
        }
    }

    /**
     *
     */
    public function add_plugin_page()
    {
        add_options_page(
            'Настройки плагина WpAmoCRMSync',
            'WpAmoCRMSync',
            'manage_options',
            'aafs',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     *
     */
    public function create_admin_page()
    {
        $this->options = get_option( 'aafs_settings_name' );
        ?>
        <div class="wrap">
            <h1>Настройки плагина</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'aafs_settings_group' );
                do_settings_sections( 'aafs' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     *
     */
    public function page_init()
    {
        register_setting('aafs_settings_group','aafs_settings_name', array( $this, 'sanitize' ));
        add_settings_section('aafs_setting_section_id', 'Данные для подключения к API и настройки плагина', array( $this, 'print_section_info' ), 'aafs');
        add_settings_field('subdomain', 'Поддомен', array( $this, 'subdomain_callback' ), 'aafs', 'aafs_setting_section_id');
        add_settings_field('login', 'Логин', array( $this, 'login_callback' ), 'aafs', 'aafs_setting_section_id');
        add_settings_field('api_key', 'API Key', array( $this, 'api_key_callback' ), 'aafs', 'aafs_setting_section_id');
        add_settings_field('pipeline_id', 'Воронка', array( $this, 'pipeline_id_callback' ), 'aafs', 'aafs_setting_section_id');
        add_settings_field('phone_field_id', 'Поле контакта "Телефон"', array( $this, 'phone_field_id_callback' ), 'aafs', 'aafs_setting_section_id');
        add_settings_field('email_field_id', 'Поле контакта "Email"', array( $this, 'email_field_id_callback' ), 'aafs', 'aafs_setting_section_id');
        add_settings_field('name_pattern', 'Название сделки', array( $this, 'name_pattern_callback' ), 'aafs', 'aafs_setting_section_id');
    }

    /**
     * @param $input
     * @return array
     */
    public function sanitize($input )
    {
        $new_input = array();
        if( isset( $input['subdomain'] ) )
            $new_input['subdomain'] = $input['subdomain'];
        if( isset( $input['login'] ) )
            $new_input['login'] = $input['login'];
        if( isset( $input['api_key'] ) )
            $new_input['api_key'] = $input['api_key'];
        if( isset( $input['pipeline_id'] ) )
            $new_input['pipeline_id'] = $input['pipeline_id'];
        if( isset( $input['phone_field_id'] ) )
            $new_input['phone_field_id'] = $input['phone_field_id'];
        if( isset( $input['email_field_id'] ) )
            $new_input['email_field_id'] = $input['email_field_id'];
        if( isset( $input['name_pattern'] ) )
            $new_input['name_pattern'] = $input['name_pattern'];
        return $new_input;
    }

    /**
     *
     */
    public function print_section_info()
    {
        echo 'Пример использования плагина в теме:';
        echo '<pre><b>';
        ?>
        if (is_plugin_active('wp-amocrm-sync/aafs.php')) {
            try {
                \AI2C\WpAmoCRMSync::createAmoLead('Имя','Email','Телефон','Сообщение');
            }catch (Exception $e){
                // echo 'Exception: ', $e->getMessage(), "\n";
            }
        }
        <?php
        echo '</b></pre>';
        echo 'Данные настройки Вы можете посмотреть в своем профиле AmoCRM, нажав на фото пользователя в левом верхнем углу экрана:';
    }

    /**
     * @param $dom
     * @param $optionName
     */
    private function print_field($dom, $optionName){
        printf($dom, isset( $this->options[$optionName] ) ? esc_attr( $this->options[$optionName]) : '');
    }

    /**
     *
     */
    public function subdomain_callback()
    {
        $dom = '<input type="text" id="subdomain" name="aafs_settings_name[subdomain]" value="%s" style="min-width: 300px;"/><br><small>Например: youname.amocrm.ru</small>';
        $this->print_field($dom,'subdomain');
    }

    /**
     *
     */
    public function login_callback()
    {
        $dom = '<input type="text" id="login" name="aafs_settings_name[login]" value="%s" style="min-width: 300px;"/><br><small>Например: contact@yourname.com</small>';
        $this->print_field($dom,'login');
    }

    /**
     */
    public function api_key_callback()
    {
        $dom = '<input type="text" id="api_key" name="aafs_settings_name[api_key]" value="%s" style="min-width: 300px;"/>';
        if($this->amoInfo != false)
            $dom .= '<br><br><strong>↓↓↓ Сопоставьте поля настроек с полями из AmoCRM  ↓↓↓</strong>';
        $this->print_field($dom,'api_key');
    }

    /**
     *
     */
    public function pipeline_id_callback()
    {
        $dom = '';
        if($this->amoInfo != false){
            $pipelinesArr = $this->amoInfo['pipelines'];
            $dom .= '<select id="pipeline_id" name="aafs_settings_name[pipeline_id]" required style="min-width: 300px;">';
            foreach($pipelinesArr as $pipelineRow){
                $dom .= '<option value="'.$pipelineRow['id'].'" '.($this->options['pipeline_id'] == $pipelineRow['id'] ? 'selected' : '').'>'.$pipelineRow['name'].'</option>';
            }
            $dom .= '</select>';
        }else{
            $dom = '<strong>↑↑↑ сначала сохраните данные для подключения к API  ↑↑↑</strong>';
        }
        $this->print_field($dom,'pipeline_id');
    }

    /**
     *
     */
    public function phone_field_id_callback()
    {
        $dom = '';
        if($this->amoInfo != false){
            $contactFieldsArr = $this->amoInfo['custom_fields']['contacts'];
            $dom .= '<select id="phone_field_id" name="aafs_settings_name[phone_field_id]" required style="min-width: 300px;">';
            foreach($contactFieldsArr as $contactFieldsRow){
                $dom .= '<option value="'.$contactFieldsRow['id'].'" '.($this->options['phone_field_id'] == $contactFieldsRow['id'] ? 'selected' : '').'>'.$contactFieldsRow['name'].'</option>';
            }
            $dom .= '</select>';
        }
        $this->print_field($dom,'phone_field_id');
    }

    /**
     *
     */
    public function email_field_id_callback()
    {
        $dom = '';
        if($this->amoInfo != false){
            $contactFieldsArr = $this->amoInfo['custom_fields']['contacts'];
            $dom .= '<select id="phone_field_id" name="aafs_settings_name[email_field_id]" required style="min-width: 300px;">';
            foreach($contactFieldsArr as $contactFieldsRow){
                $dom .= '<option value="'.$contactFieldsRow['id'].'" '.($this->options['email_field_id'] == $contactFieldsRow['id'] ? 'selected' : '').'>'.$contactFieldsRow['name'].'</option>';
            }
            $dom .= '</select>';
        }
        $this->print_field($dom,'email_field_id');
    }

    public function name_pattern_callback()
    {
        $dom = '<textarea id="name_pattern" name="aafs_settings_name[name_pattern]" style="min-width: 440px;" rows="3" placeholder="Заявка #date#, #name#, #phone#">%s</textarea>';
        $dom .= '<div style="width:400px;min-width:300px;padding:14px 20px;margin-bottom:15px;border:1px solid #7e8993;border-radius:15px;"><p>Доступные переменные:</p><p><b>#date#</b> - дата добавления сделки</p><p><b>#name#</b> - имя из формы</p><p><b>#phone#</b> - телефон из формы</p><p><b>#email#</b> - email из формы</p></div>';

        $this->print_field($dom,'name_pattern');
    }
}
$my_settings_page = new WpAmoCRMSyncSettingsPage();