<?php
class admin_extviews_Sample
{
	public static function hello()
	{
		$content = '';

		$view = new Zend_View();
		$view->message = 'Test test';
		
//		$view->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/section');
//		$content = $view->render('menu.phtml');

		$view->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/test_extview');
		$content = $view->render('index.phtml');
		
		return $content;		
	}
}