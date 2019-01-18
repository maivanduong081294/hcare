<?php

class Business_WsHelper_Language
{
	
	public static function getCurrentLangName()
	{
		$frontController = Zend_Controller_Front::getInstance();
		$request = $frontController->getRequest();
		$langname = $request->getParam('lang');
		return $langname;
	}
	
	public static function getCurrentLangId()
	{
		$langname = self::getCurrentLangName();
		$lang = self::getCurrentLangName();
		$_language = Business_Ws_Languages::getInstance();
		$mylang = $_language->getDetailByName($lang);
		if($mylang != null) return $mylang['langid'];
		else return 0;
	}
	
	public static function getDefaultLangName()
	{
		$_language = Business_Ws_Languages::getInstance();
		$langname = $_language->getDefaultLangName();
		return $langname;
	}
	
	public static function getDefaultLangId()
	{
		$_language = Business_Ws_Languages::getInstance();
		$langid = $_language->getDefaultLang();
		return $langid;		
	}
	
}

?>