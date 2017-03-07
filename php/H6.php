<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');
define('DEBUG', true);
define('APP_PATH', str_replace('\\','/',dirname(__FILE__)));
ini_set('display_errors', 0);
ini_set('log_errors', 'On');
ini_set('error_log', APP_PATH . "/data/error/php_error_".date('Ymd').".log");

$_GET = daddslashes($_GET, 1, TRUE);
$_POST = daddslashes($_POST, 1, TRUE);
$_COOKIE = daddslashes($_COOKIE, 1, TRUE);
$_SERVER = daddslashes($_SERVER);
$_FILES = daddslashes($_FILES);
$_REQUEST = daddslashes($_REQUEST, 1, TRUE);

$actions = array(
	'profile', 'login', 'checkusername', 'show', 'user', 'register'
);
if (!in_array($_GET['action'], $actions)) throw new Exception('没有找到该页面', 404);

$method = 'on' . ucfirst($_GET['action']);
$app = new Application;

if (!method_exists($app, $method)) throw new Exception('404');

if (!in_array($actions[$method], $app->filter())) {
    $control->init_user();
}

$data = $app->$method();
echo $data;
unset($data);


class Application
{
	public function __construct() {}
	
	public function filter()
	{
		return array('login','register','checkusername');
	}
	
	public function onLogin() {}

	public function onRegister() {}

	public function onShow() {}

	public function onUser() {}
}
