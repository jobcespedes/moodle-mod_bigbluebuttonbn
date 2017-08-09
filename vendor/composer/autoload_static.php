<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc35a4b54d8efe6b09742ecfafa45f75d
{
    public static $prefixLengthsPsr4 = array (
        'm' => 
        array (
            'mikemccabe\\JsonPatch\\' => 21,
        ),
        'S' => 
        array (
            'Symfony\\Component\\EventDispatcher\\' => 34,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'mikemccabe\\JsonPatch\\' => 
        array (
            0 => __DIR__ . '/..' . '/mikemccabe/json-patch-php/src',
        ),
        'Symfony\\Component\\EventDispatcher\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/event-dispatcher',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $prefixesPsr0 = array (
        'O' => 
        array (
            'OpenCloud' => 
            array (
                0 => __DIR__ . '/..' . '/rackspace/php-opencloud/lib',
            ),
        ),
        'G' => 
        array (
            'Guzzle\\Tests' => 
            array (
                0 => __DIR__ . '/..' . '/guzzle/guzzle/tests',
            ),
            'Guzzle' => 
            array (
                0 => __DIR__ . '/..' . '/guzzle/guzzle/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc35a4b54d8efe6b09742ecfafa45f75d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc35a4b54d8efe6b09742ecfafa45f75d::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitc35a4b54d8efe6b09742ecfafa45f75d::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
