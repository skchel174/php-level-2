<?php
use \application\core as Core;

define('BASE_DIR', dirname(dirname(__FILE__)));
define('APP_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'application');

require_once BASE_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

Core\Application::getInstance()->session()->start();
Core\Application::getInstance()->dispatch();
