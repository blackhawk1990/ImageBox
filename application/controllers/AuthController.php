<?php

class AuthController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
        //utworzenie obiektu przechowujacego w sesji role uzytkownika
        $this->storage = new Zend_Session_Namespace('user_data');
    }
    
    public function indexAction()
    {
        $this->view->logerror = '';
        
        if($this->_auth->hasIdentity())
        {
            $this->_helper->redirector('index', 'index');
        }
        
        $form = new App_Form_Login();
        $request = $this->getRequest()->getPost();
        
        if($request)
        {
            //filtr na login i haslo przepuszczajacy tylko litery i cyfry
            $filter = new Zend_Filter_Alnum();
            
            $login = $filter->filter($request['login']);
            $password = $filter->filter($request['password']);
            
            $validation = new Zend_Auth_Adapter_DbTable(
                    null,
                    'Users',
                    'nick',
                    'password'
            ); //na hosting
            
//            $validation = new Zend_Auth_Adapter_DbTable(
//                    null,
//                    'users',
//                    'login',
//                    'password'
//            );
            
            if($login != '')
            {
                $validation->setIdentity($login);
                $validation->setCredential(md5($password));
                
                //autentykacja na podstawie obiektu tabeli walidujacej
                $validate = $this->_auth->authenticate($validation);

                //jesli walidacja sie powiodla
                if($validate->isValid())
                {
                    //utworzenie obiektu tabeli uzytkownikow z bazy danych
                    $user = new App_Model_User();
                    $user_data = $user->fetchRow($user->select('rola,userID')->where('nick=\'' . $this->_auth->getIdentity() . '\' AND password=\'' . md5($password) . '\''));
                    //zapisanie roli do sesji
                    $this->storage->role = $user_data['rola'];
                    //zapisanie id do sesji
                    $this->storage->id = $user_data['userID'];
                    //$this->view->user_data = $validation->getResultRowObject();
                    $this->_helper->redirector('profile', 'user', array('id' => $user_data['userID']));
                }
                else
                {
                    //probujemy z haslem bez md5-usunac to pozniej
                    $validation->setCredential($password);

                    $validate = $this->_auth->authenticate($validation);

                    if($validate->isValid())
                    {
                        //utworzenie obiektu tabeli uzytkownikow z bazy danych
                        $user = new App_Model_User();
                        $user_data = $user->fetchRow($user->select('rola,userID')->where('nick=\''.$this->_auth->getIdentity().'\' AND password=\''.$password.'\''));
                        //zapisanie roli do sesji
                        $this->storage->role = $user_data['rola'];
                        //zapisanie id do sesji
                        $this->storage->id = $user_data['userID'];
                        
                        $this->_helper->redirector('profile', 'user', array('id' => $user_data['userID']));
                    }

                    $this->view->logerror = "Nieprawidłowy login i/lub hasło!";
                }
            }
            else
            {
                $this->view->logerror = "Nieprawidłowy login i/lub hasło!";
            }
        }
        
        $this->view->form = $form;
    }
    
    public function logoutAction()
    {
        $this->_auth->clearIdentity();
        $this->_helper->redirector('index');
        unset($this->storage->role);
    }
}

?>
