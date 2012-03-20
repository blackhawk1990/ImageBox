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
        // action body
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





