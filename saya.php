#!/usr/bin/php7.0
<?php require(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'Autoload.php');

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die('Your php version is to low. This bot required minimum 5.4.0 php version.');
}

chdir(__DIR__);
defined('ROOT_DIR') or define('ROOT_DIR', __DIR__);

defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));
// Make autoload working

/* Run */
(new \Saya\Core\Bot())->startBot();
