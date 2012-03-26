<?php

class App_Form_Upload extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id', 'upload-form');
        
        $this->addElement('file', 'file', array(
           'label' => '',
            'class' => 'file-select'
        ));
        
        $submit_but = new Zend_Form_Element_Button('file-submit', array(
            'label' => '',
            'id' => 'submit',
            'class' => 'button submit-but',
            'disableLoadDefaultDecorators' => true
        ));
        
        //wczytanie jedynie podstawowego widoku elementu formy(bez decoratorow)
        $submit_but->addDecorator('ViewHelper');
        $submit_but->addDecorator('HtmlTag', array ('tag' => 'dd', 'id' => 'submit-element'));
        $this->addElement($submit_but);
        
        $cancel = new Zend_Form_Element_Button('cancel', array(
            'label' => '',
            'class' => 'button cancel-but',
            'disableLoadDefaultDecorators' => true
        ));
        
        //wczytanie jedynie podstawowego widoku elementu formy(bez decoratorow)
        $cancel->addDecorator('ViewHelper');
        $cancel->addDecorator('HtmlTag', array ('tag' => 'dd', 'id' => 'cancel-element'));
        $this->addElement($cancel);
    }


}

