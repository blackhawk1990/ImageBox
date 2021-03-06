<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->_auth = Zend_Auth::getInstance();
        
        $this->storage = new Zend_Session_Namespace('user_data');
        if(!$this->_auth->hasIdentity()) //nie zalogowany
        {
            $this->storage->role = "not-logged";
        }
    }

    public function indexAction()
    {
        //jezeli uzytkownik zalogowany pokazujemy glowna strone
        if($this->_auth->hasIdentity())
        {
            $table = new App_Model_News();
            $news = $table->fetchAll($table->select()->order('data DESC'));
            $this->view->newses = $news;
        }
        else //jesli nie przekierowujemy go do ekranu logowania
        {
            $this->_helper->redirector->goToRoute(array(), 'login');
        }
    }

}

