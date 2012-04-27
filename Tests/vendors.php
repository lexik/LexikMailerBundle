#!/usr/bin/env php
<?php

set_time_limit(0);

if (isset($argv[1])) {
    $_SERVER['SYMFONY_VERSION'] = $argv[1];
}

$vendorDir = __DIR__.'/../vendor';
if (!is_dir($vendorDir)) {
    mkdir($vendorDir);
}

$deps = array(
    array('symfony',           'https://github.com/symfony/symfony.git'),
    array('doctrine-common',   'https://github.com/doctrine/common.git'),
    array('doctrine-dbal',     'https://github.com/doctrine/dbal.git'),
    array('doctrine',          'https://github.com/doctrine/doctrine2.git'),
    array('doctrine-fixtures', 'https://github.com/doctrine/data-fixtures.git'),
    array('swiftmailer',       'https://github.com/swiftmailer/swiftmailer.git'),
    array('twig',              'https://github.com/fabpot/Twig.git'),
);

$revs = array(
    'v2.0.12' => array(
        'symfony'           => 'v2.0.12',
        'doctrine-common'   => '2.1.4',
        'doctrine-dbal'     => '2.1.6',
        'doctrine'          => '2.1.6',
        'doctrine-fixtures' => 'origin/master',
        'swiftmailer'       => 'v4.1.5',
        'twig'              => 'v1.6.2',
    ),
);

if (!isset($_SERVER['SYMFONY_VERSION'])) {
    $_SERVER['SYMFONY_VERSION'] = 'origin/master';
}

foreach ($deps as $index => $dep) {
    list($name, $url) = $dep;
    $rev = isset($revs[$_SERVER['SYMFONY_VERSION']][$name]) ? $revs[$_SERVER['SYMFONY_VERSION']][$name] : 'origin/master';

    $installDir = (substr($name, -6) == 'Bundle') ? $vendorDir.'/bundles/Symfony/Bundle/'.$name : $vendorDir.'/'.$name;
    if (!is_dir($installDir)) {
        echo sprintf("> Installing %s\n", $name);

        system(sprintf('git clone %s %s', escapeshellarg($url), escapeshellarg($installDir)));
    } else {
        echo sprintf("> Updating %s\n", $name);
    }

    system(sprintf('cd %s && git fetch origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
}
