<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initApp()
    {
        $bootstrap = $this->bootstrap(array('FrontController', 'db'));
        $front = $bootstrap->frontController;
        //$front->setBaseUrl('/~student94361/Korto2012/');
        $front->setBaseUrl('/Korto2012/public/');
        
        //layout settings
        $view = new Zend_View();
        $view->doctype('HTML5');
        $view->headMeta()->setCharset('UTF-8');
        $view->headLink()->appendStylesheet($front->getBaseUrl().'styles/default.css');
        $view->headLink()->appendStylesheet($front->getBaseUrl().'styles/ui/jquery-ui-1.8.16.custom.css');
        //tytul
        $view->headTitle()->append('Kortowiada 2012 - serwis informacyjny');
        
        //scripts
        $view->headScript()->appendFile($front->getBaseUrl().'js/jquery-1.7.min.js');
        $view->headScript()->appendFile($front->getBaseUrl().'js/jquery-ui-1.8.16.custom.min.js');
    }
}

