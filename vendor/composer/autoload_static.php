<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit22e3cb167000a1b44fa9d5e6c12fbde4
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Luizottavioc\\CleanArchAuthentication\\' => 37,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Luizottavioc\\CleanArchAuthentication\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit22e3cb167000a1b44fa9d5e6c12fbde4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit22e3cb167000a1b44fa9d5e6c12fbde4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit22e3cb167000a1b44fa9d5e6c12fbde4::$classMap;

        }, null, ClassLoader::class);
    }
}