<?php

class FileController extends Zend_Controller_Action
{

    public function init()
    {
        //pokazanie formularza logowania, gdy uzytkownik nie zalogowany
        if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $log_form = new App_Form_Login();
            $log_form->setAttrib('action', $this->view->url(array(), 'login'));
            $this->view->form = $log_form;
        }
        
        //tablica zamieniajaca rozszerzenie na typ mime dla przegladarki
        $mimes = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'pdf' => 'application/pdf',
            'mp3' => 'audio/mp3',
            'ogg' => 'audio/ogg',
            'aac' => 'audio/aac',
            'wmv' => 'video/x-ms-wmv'
        );
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
                
                //sciezka pliku
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
        
        //dolaczenie skryptow
        $this->view->headScript()->appendFile($this->_request->getBaseUrl().'/public/js/feather.js');
        $this->view->headScript()->appendFile($this->_request->getBaseUrl().'/public/js/jquery.counter.js');
        //dolaczenie styli
        $this->view->headLink()->appendStylesheet($this->_request->getBaseUrl().'/public/styles/collection.css');
        
        if($request->isGet())
        {
            if($request->getParam('id') != '')
            {
                $this->view->id = $request->getParam('id');
                
                //odczyt z bazy danych
                $file_table = new App_Model_File();
                $this->view->file_data = $file_table->fetchAll($file_table->select()->where('userID=?', $request->getParam('id'))->order('data_dodania DESC'));
                
                //pobranie danych o userze (jego roli i id) z sesji
                $storage = new Zend_Session_Namespace('user_data');
                $this->view->user_role = $storage->role;
                $this->view->user_id = $storage->id;
                
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

    public function showAction()
    {
        //zablokowanie wyswietlania domyslnego widoku i layoutu
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        $request = $this->getRequest();
        
        if($request->isGet())
        {
            if($request->getParam('id') != '')
            {
                //pobranie z bazy informacji o polozeniu pliku
                $file_table = new App_Model_File();
                $file_data = $file_table->fetchRow($file_table->select('url,format')->where('fileID=?', $request->getParam('id')));
                //wyciagniecie danych z pliku
                $file = file_get_contents($file_data['url']);
                
                //wyslanie pliku do przegladarki
                $this->getResponse()
                     ->setHeader('Content-Type', $mimes[$file_data['format']])
                     ->appendBody($file);
            }
        }
    }

    public function downloadAction()
    {
        //zablokowanie wyswietlania domyslnego widoku i layoutu
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        $request = $this->getRequest();
        
        if($request->isGet())
        {
            if($request->getParam('id') != '')
            {
                //pobranie z bazy informacji o polozeniu pliku
                $file_table = new App_Model_File();
                $file_data = $file_table->fetchRow($file_table->select('url,format,ilosc_pobran,userID')->where('fileID=?', $request->getParam('id')));
                //wyciagniecie danych z pliku
                $file = file_get_contents($file_data['url']);
                //pobranie dotychczasowej liczby pobran pliku
                $file_downloads = $file_data['ilosc_pobran'];
                $file_downloads++;
                
                //wyslanie pliku do przegladarki
                $this->getResponse()
                     ->setHeader('Content-Type', 'application/x-download');
                
                $this->getResponse()
                     ->setHeader('Content-Disposition', 'attachment; filename="file_' . $request->getParam('id') . '.' . $file_data['format'] . '"')
                     ->appendBody($file);
                
                //zwiekszenie licznika pobran
                $file_table->update(array('ilosc_pobran' => $file_downloads), 'fileID=' . $request->getParam('id'));
            }
        }
        
    }
    
    public function zipAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        
        $view = new Zend_View();
        
        $request = $this->getRequest();
        
        //dolaczenie skryptow
        $view->headScript()->appendFile($this->_request->getBaseUrl().'/public/js/feather.js');
        $view->headScript()->appendFile($this->_request->getBaseUrl().'/public/js/jquery.counter.js');
        //dolaczenie styli
        $view->headLink()->appendStylesheet($this->_request->getBaseUrl().'/public/styles/collection.css');
        
        //jezeli przeslano dane getem
        if($request->isGet())
        {
            if($request->getParam('id') != '')
            {
                //sciezka plikow
                $file_path = "files/".$request->getParam('id');
                
                //nazwa archiwum
                $arc_name = "user_" . $request->getParam('id') . ".zip";
                
                //tymczasowa sciezka do archiwum
                $arc_path = $file_path . "/tmp/" . $arc_name;
                
                //tworzenie archiwum
                if(!$request->getParam('download'))
                {
                    //jezeli katalog tymczasowy nie istnieje, zostaje utworzony
                    if(!file_exists($file_path . "/tmp"))
                    {
                        mkdir($file_path . "/tmp");
                    }

                    $ziparc = new ZipArchive();
                    //utworzenie archiwum
                    $res = $ziparc->open($arc_path, ZIPARCHIVE::CREATE);

                    //archiwum zostalo utworzone
                    if($res === TRUE)
                    {
                        $ziparc->addEmptyDir("user_" . $request->getParam('id') . "_collection");

                        $tmp = dir($file_path);

                        //odczyt wszystkich plikow z folderu z albumem
                        while(($file_name = $tmp->read()) != NULL)
                        {
                            //wykluczenie niedozwolonych nazw plikow
                            if($file_name != '.' && $file_name != '..' && $file_name != '' && $file_name != 'tmp')
                            {
                                $ziparc->addFile($file_path . "/" . $file_name, "user_" . $request->getParam('id') . "_collection/" . $file_name);
                            }
                        }

                        $tmp->close();

                        $ziparc->close();
                        
                        $view->id = $request->getParam('id');
                
                        //odczyt z bazy danych
                        $file_table = new App_Model_File();
                        $view->file_data = $file_table->fetchAll($file_table->select()->where('userID=?', $request->getParam('id'))->order('data_dodania DESC'));

                        //pobranie danych o userze (jego roli i id) z sesji
                        $storage = new Zend_Session_Namespace('user_data');
                        $view->user_role = $storage->role;
                        $view->user_id = $storage->id;

                        $view->headScript()->appendScript("
                            $(function()
                            {
                                $('body').append('<div id=\'message-box\' title=\'Sukces\'>Archiwum zostało wygenerowane!<br />Naciśnij OK, aby rozpocząć pobieranie</div>');

                                //messagebox z info - po kliknięciu zamyka się i przenosi na stronę główną
                                $('#message-box').dialog({
                                    modal : true,
                                    resizable : false,
                                    autoOpen : true,
                                    buttons : [{
                                        text : 'OK',
                                        click : function(){ $(this).dialog('close'); location.href='".$this->view->url(array('id' => $request->getParam('id'), 'download' => 1), 'compress_all')."' }
                                    }]
                                });
                            });
                        ");
                        
                        //dodanie sciezki i wyrenderowanie widoku kolekcji
                        $view->addScriptPath(APPLICATION_PATH . "/views/scripts/");
                        
                        $rendered = $view->render('file/collection.phtml');
                        echo $rendered;
                    }
                    else
                    {
                        $view->headScript()->appendScript("
                            $(function()
                            {
                                $('body').append('<div id=\'message-box\' title=\'Błąd\'>Błąd tworzenia archiwum!</div>');

                                //messagebox z info - po kliknięciu zamyka się i przenosi na stronę główną
                                $('#message-box').dialog({
                                    modal : true,
                                    resizable : false,
                                    autoOpen : true,
                                    buttons : [{
                                        text : 'OK',
                                        click : function(){ $(this).dialog('close'); location.href='".$this->view->url(array('id' => $request->getParam('id')), 'collection')."' }
                                    }]
                                });
                            });
                        ");
                    }
                }
                else //pobieranie archiwum
                {
                    //rozmiar pliku
                    $fsize = filesize($arc_path);
                    
                    //wyciagniecie danych z pliku
                    $file = file_get_contents($arc_path);

                    //wyslanie pliku do przegladarki
                    $this->getResponse()
                        ->setHeader('Content-Type', 'application/x-download');
                    
                    $this->getResponse()
                        ->setHeader('Content-Length', $fsize);

                    $this->getResponse()
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $arc_name . '"')
                        ->appendBody($file);

                    //skasowanie pliku tymczasowego
                    unlink($arc_path);
                }
            }
        }
        
    }


}



