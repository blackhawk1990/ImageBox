<?php

class App_Form_Activate extends Zend_Form
{
    public function init()
    {
        $this->setName('actform');
        
        $this->addElement('text', 'code', array(
            'filters' => array(
                'StringTrim'
            ),
            'label' => 'Kod aktywacji',
            'class' => 'logowanie',
            'title' => 'Kod aktywacyjny otrzymany w mailu potwierdzającym zapłatę za opcję premium<br /><span style="color:red;">Kod jest 5-cyfrowy</span>'
        ));
        $this->addElement('hidden', 'id', array(
            'label' => ''
        ));
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => '',
            'class' => 'button submit-but'
        ));
    }
    
}

?>
