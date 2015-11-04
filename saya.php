<?php
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die('Your php version is to low. This bot required minimum 5.4.0 php version.');
}

chdir(__DIR__);
defined('ROOT_DIR') or define('ROOT_DIR', __DIR__);

defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));
// Make autoload working

/* @var $loader Composer\Autoload\ClassLoader */
$loader = require (ROOT_DIR . '/vendor/autoload.php');

$loader->addPsr4('Library\\', ROOT_DIR . '/Library');
$loader->addPsr4('Module\\', ROOT_DIR . '/Module');
/* Run */
(new \Library\Bot())->startBot();
