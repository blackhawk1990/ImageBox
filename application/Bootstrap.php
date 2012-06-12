<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initApp()
    {
        $bootstrap = $this->bootstrap(array('FrontController', 'db'));
        $front = $bootstrap->frontController;
        //$front->setBaseUrl('/~student94361/Korto2012/');
        //$front->setBaseUrl('/ImageBox/public/');
        $front->setBaseUrl('/');
        
        //layout settings
        $view = new Zend_View();
        $view->doctype('HTML5');
        $view->headMeta()->setCharset('UTF-8');
        $view->headLink()->appendStylesheet($front->getBaseUrl().'public/styles/default.css');
        $view->headLink()->appendStylesheet($front->getBaseUrl().'public/styles/page.css');
        $view->headLink()->appendStylesheet($front->getBaseUrl().'public/styles/fonts.css');
        $view->headLink()->appendStylesheet($front->getBaseUrl().'public/styles/forms.css');
        $view->headLink()->appendStylesheet($front->getBaseUrl().'public/styles/ui/jquery-ui-1.8.16.custom.css');
        //tytul
        $view->headTitle()->append('ImageBox - Save U Image');
        
        //scripts
        $view->headScript()->appendFile($front->getBaseUrl().'public/js/jquery-1.7.min.js');
        $view->headScript()->appendFile($front->getBaseUrl().'public/js/jquery-ui-1.8.16.custom.min.js');
        
        //--------------przypisanie uprawnien do rol--------------//
        $acl = new Zend_Acl();
        
        $acl->addRole('admin');
        $acl->addRole('user');
        $acl->addRole('premium-user');
        $acl->addRole('not-logged');
        
        $acl->addResource('news-admin-opt');
        $acl->addResource('news-read');
        $acl->addResource('upload-limited');
        $acl->addResource('upload-non-limited');
        
        $acl->allow('admin'); //dostep do wszystkiego
        $acl->allow('user', 'upload-limited');
        $acl->allow('user', 'news-read');
        $acl->allow('premium-user', 'upload-non-limited');
        $acl->allow('premium-user', 'news-read');
        $acl->allow('not-logged', 'news-read');
        
        Zend_Registry::set('acl', $acl);
        
        Zend_Registry::set('avatar_path', 'avatar');
    }
}