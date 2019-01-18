<?php
class Hnamcare_SectionController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function headerAction()
    {
        $this->view->headLink()->appendStylesheet("/hcare/css/bootstrap.css?v=" . Globals::getVersion());
    	$this->view->headLink()->appendStylesheet("/hcare/css/font-awesome.min.css?v=" . Globals::getVersion());
        $this->view->headLink()->appendStylesheet("/hcare/css/main.css?v=" . Globals::getVersion());
        $this->view->headLink(array('rel' => 'shortcut icon', 'href' => Globals::getStaticUrl().'/hcare/images/favicon.png', 'type' => 'image/x-icon'), 'PREPEND');
        $this->view->headScript()->appendFile(Globals::getStaticUrl() . "/hcare/js/jquery.min.js?v=" . Globals::getVersion());
    }

    public function footerAction()
    {    	
        $this->view->inlineScript()->appendFile(Globals::getStaticUrl() . "/hcare/js/main.js?v=" . Globals::getVersion());
    }
}