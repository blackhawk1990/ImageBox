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
        
        $this->view->headLink()->appendStylesheet($this->_request->getBaseUrl().'/public/styles/ui/upload.css');
        $this->view->headScript()->appendFile($this->_request->getBaseUrl().'/public/js/upload.js');
        $this->view->headScript()->appendScript("
            $(function(){
                $('#submit').click(function(){
                    upload('upload-form', 'progress-bar', ".$request->getParam('id').");
                });
            });
        ");
      
        $upload_form = new App_Form_Upload();
        $upload_form->cancel->setAttrib('onclick', "location.href='".$this->view->url(array('id' => $request->getParam('id')), 'collection')."'");
        
        if($request->isPost())
        {
            if(isset($_FILES['file']))
            {
                //jezeli katalog usera nie istnieje, zostaje utworzony
                if(!file_exists("files/".$request->getParam('id')))
                {
                    mkdir("files/".$request->getParam('id'));
                }
                
                $file_path = "files/".$request->getParam('id')."/".$_FILES['file']['name'];
                
                move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
                unlink($_FILES ['file'] ['tmp_name']);
                
                //zapis do bazy danych
                $file_table = new App_Model_File();
                $file = pathinfo($file_path);
                
                $file_data = array(
                    'userID' => $request->getParam('id'),
                    'url' => $file_path,
                    'data_dodania' => new Zend_Db_Expr('CURDATE()'),
                    'format' => $file['extension'],
                    'ilosc_pobran' => 0,
                    'nazwa' => strlen(str_replace('.' . $file['extension'], '', $_FILES['file']['name'])) > 20 ? substr(str_replace('.' . $file['extension'], '', $_FILES['file']['name']), 0, 20) . "..." : substr(str_replace('.' . $file['extension'], '', $_FILES['file']['name']), 0, 20), //nazwa nie dluzsza niz 20 znakow
                    'ocena' => 0,
                    'waga' => round((filesize($file_path) / 1024) / 1024, 2)
                );
                
                $file_table->insert($file_data);
                
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
                
                //odczyt z bazy danych
                $file_table = new App_Model_File();
                $this->view->file_data = $file_table->fetchAll($file_table->select()->where('userID=' . $request->getParam('id')));
                
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
    
    public function deleteAction()
    {
        $request = $this->getRequest();
        
        if($request->isGet())
        {
            if($request->getParam('id') != '')
            {
                //kasowanie z bazy danych
                $file_table = new App_Model_File();
                $this->view->file = $file_table->fetchRow('fileID=' . $request->getParam('id'));
                //kasowanie pliku
                unlink($this->view->file['url']);
                
                $file_table->delete('fileID=' . $request->getParam('id'));
            }
        }
    }

}





