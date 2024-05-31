<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3ea00f61e054ad4a1228b54af332fc7e
{
    public static $files = array (
        '2df68f9e79c919e2d88506611769ed2e' => __DIR__ . '/..' . '/respect/stringifier/src/stringify.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Stripe\\' => 7,
        ),
        'R' => 
        array (
            'Respect\\Validation\\' => 19,
            'Respect\\Stringifier\\' => 20,
        ),
        'M' => 
        array (
            'Makaira\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
        'Respect\\Validation\\' => 
        array (
            0 => __DIR__ . '/..' . '/respect/validation/library',
        ),
        'Respect\\Stringifier\\' => 
        array (
            0 => __DIR__ . '/..' . '/respect/stringifier/src',
        ),
        'Makaira\\' => 
        array (
            0 => __DIR__ . '/..' . '/makaira/shared-libs/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'K' => 
        array (
            'Kore' => 
            array (
                0 => __DIR__ . '/..' . '/kore/data-object/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3ea00f61e054ad4a1228b54af332fc7e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3ea00f61e054ad4a1228b54af332fc7e::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit3ea00f61e054ad4a1228b54af332fc7e::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit3ea00f61e054ad4a1228b54af332fc7e::$classMap;

        }, null, ClassLoader::class);
    }
}
