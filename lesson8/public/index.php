<?php
ini_set('display_errors', 1);

use \application\core\Application;

define('BASE_DIR', dirname(dirname(__FILE__)));
define('APP_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'application');
define('CONF_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'config');
define('LIB_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'vendor');
define('LOG_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'logs');
define('TMPL_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'views');

require_once(LIB_DIR . DIRECTORY_SEPARATOR . 'autoload.php');

Application::getInstance()->dispatch();
