=== WpAmoCRMSync ===
Tags: amocrm, leads, contacts, integration, api
Requires at least: 4.0
Tested up to: 5.3.2
Stable tag: 5.3
License: GPLv2 or later

WpAmoCRMSync allows you to send data from forms directly to the AmoCRM.

!!! Important, to use integration you must include special code in your template!

== Description ==

Major features in WpAmoCRMSync include:

* Easy connection setup.
* Easy to use in template.
* Add a message to the plugin call and it will drop into the order comments.
* If the contact exists, the order will be added to it.
* Possible to set a order name using the pattern. E.g: "Order #date#, #name"

Simple usage:
if (is_plugin_active('wp-amocrm-sync/aafs.php')) {
    try {
        \AI2C\WpAmoCRMSync::createAmoLead('Имя','Email','Телефон','Сообщение');
    }catch (Exception $e){
        // echo 'Exception: ', $e->getMessage(), "\n";
    }
}

== Installation ==

Upload the WpAmoCRMSync plugin to your Wordpress, activate it. Then enter your AmoCRM settings and correlate plugin fields with AmoCRM fields.

You're done!

== Changelog ==

= 1.1 =
*Release Date - 10th February, 2020*

* First version of the plugin
