<?php

class NewsController extends Zend_Controller_Action
{
    public function init()
    {
        
    }
    
    public function indexAction()
    {
        
    }
    
    public function addAction()
    {
        $form = new App_Form_Add();
        $form->cancel->setAttrib('onclick', "location.href = '".$this->_request->getBaseUrl()."';");
        
        $request = $this->getRequest();
        
        if($request->isPost())
        {
            $news = new App_Model_News();
            $data = $news->fetchAll();
            $count = 0;
            
            $this->view->form = '';
            
            foreach($data as $a)
            {
                $count = $a['id'];
            }
            
            $id = $count + 1;
           
            if ($request->getPost('tytul') != '' && $request->getPost('autor') != '' && $request->getPost('tresc') != '')
            {
                //echo "Czy na pewno chcesz dodać news \"" . $request->getPost('tytul') . "\"? <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '" . $this->_request->getBaseUrl() . "/news/add/conf/1';\" value = \"\" /> <input type = \"button\" class = \"button cancel-but\" onclick = \"location.href = '" . $this->_request->getBaseUrl() . "/news/add';\" />";
                
                $data = array(
                    'tytul' => $request->getPost('tytul'),
                    'autor' => $request->getPost('autor'),
                    'tekst' => $request->getPost('tresc'),
                    'data' => new Zend_Db_Expr("CURRENT_TIMESTAMP()"),
                    'id' => $id
                );
                
                $news->insert($data);

                echo "News \"" . $request->getPost('tytul') . "\" został dodany! <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '" . $this->_request->getBaseUrl() . "';\" value = \"\" />";
                
            }
            else
            {
                echo "<div class = \"ui-widget\" style = \"margin: 50px 0 0 40px;font-size: 0.8em\"><div class = \"ui-state-error ui-corner-all\"><p style = \"text-align:center\"><span class = \"ui-icon-alert\" style = \"float: left\"></span>Wypełnij wszystkie wymagane pola!</p></div></div>";
                
                $this->view->form = $form;
            }
        }
        else if($request->getParam('conf') == '')
        {
            $this->view->form = $form;
        }
        
//        if ($request->getParam('conf'))
//        {
//            $news = new App_Model_News();
//            
//            
//            $news->insert("id=" . $id);
//
//            echo "News \"" . $request->getPost('tytul') . "\" został dodany! <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '" . $this->_request->getBaseUrl() . "';\" value = \"\" />";
//        }
    }
    
    public function deleteAction()
    {
        //wylaczenie renderowania widoku
        $this->_helper->viewRenderer->setNoRender(true);
        
        $news = new App_Model_News();
        $data = $news->fetchRow($news->select()->where('id='.$this->getRequest()->getParam('id')));
       
        
        if($this->getRequest()->getParam('conf')) //potwierdzenie skasowania
        {    
            $news->delete('id='.$this->getRequest()->getParam('id'));
            echo "News \"".$data['tytul']."\" o id: ".$this->getRequest()->getParam('id')." został usunięty! <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '".$this->_request->getBaseUrl()."';\" value = \"\" />";
        }
        else
        {
            echo "Czy chcesz usunąć news \"".$data['tytul']."\" z bazy danych? <input type = \"button\" class = \"button save-but\" onclick = \"location.href = '".$this->_request->getBaseUrl()."/news/delete/id/".$this->getRequest()->getParam('id')."/conf/1';\" /> <input type = \"button\" class = \"button cancel-but\" onclick = \"location.href = '".$this->_request->getBaseUrl()."';\" />";
        }
    }
    
    public function editAction()
    {
        $form = new App_Form_Edit();
        $form->cancel->setAttrib('onclick', "location.href = '".$this->_request->getBaseUrl()."';");
        
        $request = $this->getRequest();
        
        if($request->isGet())
        {
            if($request->getParam('id'))
            {
                $news = new App_Model_News();
                $data = $news->fetchRow($news->select()->where('id='.$request->getParam('id')));

                //ustawienie wartosci w formatce
                $form->tytul->setValue($data['tytul']);
                $form->tresc->setValue($data['tekst']);
                $form->autor->setValue($data['autor']);
            }
            
            $this->view->form = $form;
        }
        else if($request->isPost()) //obsluga danych zatwierdzonych w formularzu
        {
            $news = new App_Model_News();
            $data = $news->fetchRow($news->select()->where('id='.$request->getParam('id')));
            
            if(($request->getPost('tresc') != $data['tekst']) && ($request->getPost('autor') != $data['autor']) && ($request->getPost('tytul') != $data['tytul']))
            {
                $news->update(array(
                    'tekst' => $request->getPost('tresc'),
                    'autor' => $request->getPost('autor'),
                    'tytul' => $request->getPost('tytul')), 'id='.$request->getParam('id'));
                echo "Zapisano zmienione dane newsa \"".$request->getPost('tytul')."\" ! <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '".$this->_request->getBaseUrl()."';\" value = \"\" />";
                $this->view->form = '';
            }
            else if(($request->getPost('tresc') != $data['tekst']) && ($request->getPost('autor') != $data['autor']))
            {
                $news->update(array(
                    'tekst' => $request->getPost('tresc'),
                    'autor' => $request->getPost('autor')), 'id='.$request->getParam('id'));
                echo "Zapisano zmienione dane newsa \"".$data['tytul']."\" ! <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '".$this->_request->getBaseUrl()."';\" value = \"\" />";
                $this->view->form = '';
            }
            else if(($request->getPost('tresc') != $data['tekst']) && ($request->getPost('tytul') != $data['tytul']))
            {
                $news->update(array(
                    'tekst' => $request->getPost('tresc'),
                    'tytul' => $request->getPost('tytul')), 'id='.$request->getParam('id'));
                echo "Zapisano zmienione dane newsa \"".$request->getPost('tytul')."\" ! <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '".$this->_request->getBaseUrl()."';\" value = \"\" />";
                $this->view->form = '';
            }
            else if(($request->getPost('autor') != $data['autor']) && ($request->getPost('tytul') != $data['tytul']))
            {
                $news->update(array(
                    'autor' => $request->getPost('autor'),
                    'tytul' => $request->getPost('tytul')), 'id='.$request->getParam('id'));
                echo "Zapisano zmienione dane newsa \"".$request->getPost('tytul')."\" ! <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '".$this->_request->getBaseUrl()."';\" value = \"\" />";
                $this->view->form = '';
            }
            else if($request->getPost('tresc') != $data['tekst'])
            {
                $news->update(array('tekst' => $request->getPost('tresc')), 'id='.$request->getParam('id'));
                echo "Zapisano zmienione dane newsa \"".$data['tytul']."\" ! <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '".$this->_request->getBaseUrl()."';\" value = \"\" />";
                $this->view->form = '';
            }
            else if($request->getPost('autor') != $data['autor'])
            {
                $news->update(array('autor' => $request->getPost('autor')), 'id='.$request->getParam('id'));
                echo "Zapisano zmienione dane newsa \"".$data['tytul']."\" ! <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '".$this->_request->getBaseUrl()."';\" value = \"\" />";
                $this->view->form = '';
            }
            else if($request->getPost('tytul') != $data['tytul'])
            {
                $news->update(array('tytul' => $request->getPost('tytul')), 'id='.$request->getParam('id'));
                echo "Zapisano zmienione dane newsa \"".$request->getPost('tytul')."\" ! <input class = \"button submit-but\" type = \"button\" onclick = \"location.href = '".$this->_request->getBaseUrl()."';\" value = \"\" />";
                $this->view->form = '';
            }
            else
            {
                if($request->getParam('id'))
                {
                    $news = new App_Model_News();
                    $data = $news->fetchRow($news->select()->where('id='.$request->getParam('id')));

                    //ustawienie wartosci w formatce
                    $form->tytul->setValue($data['tytul']);
                    $form->tresc->setValue($data['tekst']);
                    $form->autor->setValue($data['autor']);
                }
                
                $this->view->form = $form;
                
                echo "Nie zmieniono żadnych danych !";
            }
        }
        else
        {
            $this->view->form = $form;
        }
      
    }
}

?>
