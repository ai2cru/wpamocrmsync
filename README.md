# wpamocrmsync
WpAmoCRMSync allows you to send data from forms directly to the AmoCRM.

#### Important, to use integration you must include special code in your template!

# Description

Major features in WpAmoCRMSync include:

* Easy connection setup.
* Easy to use in template.
* Add a message to the plugin call and it will drop into the order comments.
* If the contact exists, the order will be added to it.
* Possible to set a order name using the pattern. E.g: "Order #date#, #name"

Simple usage:
```php
if (is_plugin_active('wp-amocrm-sync/aafs.php')) {
    try {
        \AI2C\WpAmoCRMSync::createAmoLead('Имя','Email','Телефон','Сообщение');
    }catch (Exception $e){
        // echo 'Exception: ', $e->getMessage(), "\n";
    }
}
```
# Installation

Upload the WpAmoCRMSync plugin to your Wordpress, activate it. Then enter your AmoCRM settings and correlate plugin fields with AmoCRM fields.

You're done!

# Changelog

## [1.1] - 2020-02-10

### Added

- First version of the plugin
