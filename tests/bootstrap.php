<?php

define('FACTORY_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'factories');
require_once('PHPUnit/Util/Filesystem.php'); // workaround for PHPUnit <= 3.6.11
require_once('PHPUnit/Autoload.php');
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'FactoryGirl.php');