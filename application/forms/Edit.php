<?php

class App_Form_Edit extends Zend_Form
{
    public function init()
    {
        
        $this->setAttrib('id', 'edit-form');

        $this->addElement('text', 'tytul', array(
            'label' => 'Tytuł (*)',
            'class' => 'edit-input'
        ));

        $this->addElement('text', 'autor', array(
            'label' => 'Autor (*)',
            'class' => 'edit-input'
        ));

        $this->addElement('textarea', 'tresc', array(
            'label' => 'Treść (*)',
            'class' => 'edit-textarea'
        ));

        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => '',
            'class' => 'button submit-but',
            'disableLoadDefaultDecorators' => true
        ));
        
        //wczytanie jedynie podstawowego widoku elementu formy(bez decoratorow)
        $submit->addDecorator('ViewHelper');
        $submit->addDecorator('HtmlTag', array ('tag' => 'dd', 'id' => 'submit-element'));
        $this->addElement($submit);
        
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
?>
