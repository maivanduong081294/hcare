<?php
date_default_timezone_set('Asia/Bangkok');
if(!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', dirname(__FILE__));
}
if(!defined('APP_ENV')) {
    define('APP_ENV', 'development');
}
require_once "Zend/Loader.php";

Zend_Loader::registerAutoload();
ProfilerLog::startLog('loadcfg');
$configuration = new Zend_Config_Ini(APPLICATION_PATH . '/config/' . APP_ENV . '.global.ini',APP_ENV);
ProfilerLog::endLog('loadcfg');

Zend_Registry::set('configuration', $configuration);
if (APP_ENV=='production')
{
	define('IMAGE_URL', '/images/');
	define('UPLOAD_URL', '/uploads/');
	define('BASE_URL', $configuration->baseurl);
}
elseif (APP_ENV=='staging')
{
	define('IMAGE_URL', '/images/');
	define('UPLOAD_URL', '/uploads/');
	define('BASE_URL', $configuration->baseurl);
}
elseif (APP_ENV=='development')
{
	define('IMAGE_URL', '/images/');
	define('UPLOAD_URL', '/uploads/');
	define('BASE_URL', $configuration->baseurl);
}
$lang = Language_Language::getInstance()->getLang();
require_once APPLICATION_PATH . '/languages/language-' . $lang .'.php' ;
Zend_Registry::set('lang', $__language);
$frontController = Zend_Controller_Front::getInstance();
$frontController->setParam('env', APP_ENV);

$frontController->addControllerDirectory(APPLICATION_PATH . '/modules/admin/controllers', 'admin');
$admin_route = new Zend_Controller_Router_Route('admin/:controller/:action/*',
	array('controller' => 'auth', 'action' => 'index', 'module' => 'admin'));

$frontController->addControllerDirectory(APPLICATION_PATH . '/modules/user_admin/controllers', 'user_admin');
$user_admin_route = new Zend_Controller_Router_Route('admin/user/:controller/:action/*',
	array('controller' => 'index', 'action' => 'index', 'module' => 'user_admin'));

$frontController->addControllerDirectory(APPLICATION_PATH . '/modules/hnamcare/controllers', 'hnamcare');
$hnam_route = new Zend_Controller_Router_Route('/:controller/:action/*',
	array('controller' => 'index', 'action' => 'index', 'module' => 'hnamcare'));
$hnam_upload_image_route = new Zend_Controller_Router_Route('upload-image',
	array('controller' => 'index', 'action' => 'upload-image', 'module' => 'hnamcare'));

$router = $frontController->getRouter();
$router->addRoute('hnam', $hnam_route);
$router->addRoute('user_admin',$user_admin_route);
$router->addRoute('hnam_upload_image_route',$hnam_upload_image_route);
$frontController->setRouter($router);
$frontController->throwExceptions(TRUE);
$frontController->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
    'module'     => 'hnam',
    'controller' => 'error',
    'action'     => 'index'
)));
require_once APPLICATION_PATH . '/plugins/BlockManagement/BlockManagementPlugin.php';
$frontController->registerPlugin(new BlockManagementPlugin(array()));
require_once APPLICATION_PATH . '/plugins/SEOPlugin.php';
$frontController->registerPlugin(new SEOPlugin());
require_once APPLICATION_PATH . '/plugins/ProfilerPlugin.php';
$frontController->registerPlugin(new ProfilerPlugin());
unset($frontController, $config, $global_config, $dbAdapter, $logger, $router);