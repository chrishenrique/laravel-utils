<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit15a64e8361a90c54b0d6e61288007484
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Chrishenrique\\LaravelUtils\\' => 27,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Chrishenrique\\LaravelUtils\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit15a64e8361a90c54b0d6e61288007484::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit15a64e8361a90c54b0d6e61288007484::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit15a64e8361a90c54b0d6e61288007484::$classMap;

        }, null, ClassLoader::class);
    }
}
