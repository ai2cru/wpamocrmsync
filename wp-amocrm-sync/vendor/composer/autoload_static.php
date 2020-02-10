<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf2f7b938ad7314ca2f1cda7876f218e0
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'AmoCRM\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'AmoCRM\\' => 
        array (
            0 => __DIR__ . '/..' . '/dotzero/amocrm/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf2f7b938ad7314ca2f1cda7876f218e0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf2f7b938ad7314ca2f1cda7876f218e0::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
