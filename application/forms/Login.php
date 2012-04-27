<?php

class App_Form_Login extends Zend_Form
{
    public function init()
    {
        $this->setName('logform');
        
        $this->addElement('text', 'login', array(
            'filters' => array(
                'StringTrim'
            ),
            'label' => 'Login',
            'class' => 'logowanie'
        ));
        $this->addElement('password', 'password', array(
            'label' => 'Hasło',
            'class' => 'logowanie',
        ));
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => '',
            'class' => 'login'
        ));
    }
    
}

?>
