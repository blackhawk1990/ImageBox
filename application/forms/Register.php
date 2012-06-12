<?php

class App_Form_Register extends Zend_Form
{

    public function init()
    {
        $this->setName('regform');
        $this->setAttrib('enctype', 'multipart/form-data');
        
        $this->addElement('text', 'login', array(
            'filters' => array(
                'StringTrim'
            ),
            'label' => 'Login (*)',
            'class' => 'logowanie',
            'title' => 'Login składający się z samych liter(dużych lub małych) i cyfr.<br /><span style="color:red">Dodatkowe znaki zostaną wycięte!</span>'
        ));
        $this->addElement('password', 'password', array(
            'label' => 'Hasło (*)',
            'class' => 'logowanie',
            'title' => 'Hasło składające się z samych liter(dużych lub małych) i cyfr.<br /><span style="color:red">Dodatkowe znaki zostaną wycięte!</span>'
        ));
        
        $element = new Zend_Form_Element_File('avatar_file');
        $element->setLabel('Wczytaj swój avatar')
                ->setRequired(false);
        $this->addElement($element);
        
        $captcha = new Zend_Form_Element_Captcha('recaptcha', array(
            'label' => '* - pola obowiązkowe',
            'captcha' => array(
               'captcha' => 'Recaptcha',
                'privKey' => '6LcfN9ISAAAAAAyqAld92VTe62onW8EihbQwwUe3',
                'pubKey' => '6LcfN9ISAAAAALWJxWvF96-RC8JgIVA11npzXKeV',
                'theme' => 'white'
            )
        ));
        $this->addElement($captcha);
        
        $this->addElement('button', 'sub-but', array(
            'ignore' => true,
            'label' => '',
            'class' => 'button submit-but'
        ));
    }


}

