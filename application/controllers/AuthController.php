<?php

class AuthController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
    }
    
    public function indexAction()
    {
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
                    'users',
                    'login',
                    'password'
            );
            
            if($login != '')
            {
                $validation->setIdentity($login);
                $validation->setCredential(md5($password));


                //autentykacja na podstawie obiektu tabeli walidujacej
                $validate = $this->_auth->authenticate($validation);

                //jesli walidacja sie powiodla
                if($validate->isValid())
                {
                    $this->_helper->redirector('index', 'index');
                }
                else
                {
                    //probujemy z haslem bez md5-usunac to pozniej
                    $validation->setCredential($password);

                    $validate = $this->_auth->authenticate($validation);

                    if($validate->isValid())
                    {
                        $this->_helper->redirector('index', 'index');
                    }

                    $form->password->addError("Nieprawidłowy login i/lub hasło!");
                }
            }
            else
            {
                $form->password->addError("Nieprawidłowy login i/lub hasło!");
            }
        }
        
        $this->view->form = $form;
    }
    
    public function logoutAction()
    {
        $this->_auth->clearIdentity();
        $this->_helper->redirector('index');
    }
}

?>
