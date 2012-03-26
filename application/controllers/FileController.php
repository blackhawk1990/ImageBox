<?php

class FileController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function uploadAction()
    {
        $request = $this->getRequest();
        
        $this->view->upload_form = '';
        
        $this->view->headLink()->appendStylesheet($this->_request->getBaseUrl().'/styles/ui/upload.css');
        $this->view->headScript()->appendFile($this->_request->getBaseUrl().'/js/upload.js');
        $this->view->headScript()->appendScript("
            $(function(){
                $('#submit').click(function(){
                    upload('upload-form', 'progress-bar', ".$request->getParam('id').");
                });
            });
        ");
      
        $upload_form = new App_Form_Upload();
        
        if($request->isPost())
        {
            if(isset($_FILES['file']))
            {
                //jezeli katalog usera nie istnieje, zostaje utworzony
                if(!file_exists("files/".$request->getParam('id')))
                {
                    mkdir("files/".$request->getParam('id'));
                }
                
                move_uploaded_file($_FILES['file']['tmp_name'], "files/".$request->getParam('id')."/".$_FILES['file']['name']);
                
                $this->_helper->redirector->goToRoute(array('id' => $request->getParam('id')), 'collection');
            }
        }
        else
        {
            $this->view->upload_form = $upload_form;
        }
    }

    public function collectionAction()
    {   
        $request = $this->getRequest();
        
        if($request->isGet())
        {
            if($request->getParam('id') != '')
            {
                $this->view->id = $request->getParam('id');
                $this->view->render('file/collection.phtml');
            }
            else
            {
                $this->view->render('file/collection_error.phtml');
            }
        }
        else
        {
            $this->view->render('file/collection_error.phtml');
        }
    }


}





