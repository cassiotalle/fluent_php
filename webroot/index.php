<?

ob_start();
header('Content-type: text/html; charset="utf-8"', true);
session_start();
define("DS", DIRECTORY_SEPARATOR);
define('WEBROOT', realpath(dirname(__file__)) . DS);
define('PATH', str_replace('webroot', '', realpath(dirname(__file__))));
define('CORE', PATH . 'fluent'.DS.'core' . DS);
define('LIBS', PATH . 'fluent'.DS.'libs' . DS);
define('CONTROLLER', PATH . 'controller' . DS);
define('VIEW', PATH . 'view' . DS);
define('SITE', PATH . 'webroot' . DS);
define('MODEL', PATH . 'data' . DS);
define('LAYOUT', VIEW . '_layouts' . DS);
include (CORE . 'functions.php');
register_shutdown_function('development_true');
include (CORE . 'app.php');
include (CORE . 'neon.php');
include (PATH . 'fluent'. DS. 'definitions.php');
date_default_timezone_set(App::$time_zone);
include (PATH . 'fluent'. DS . 'routers.php');
include (CORE . 'controller.php');
require (CORE . 'router.php');
session_destroy();
?>
