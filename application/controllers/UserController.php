<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
        
        $this->avatar_path = "avatar";
        
        $this->salt1 = "dfk-s343qe95qwe4vcv!xc13";
        $this->salt2 = 'zvcv@$vhhajjh90-vxcx';
    }

    public function indexAction()
    {
        // action body
    }

    public function profileAction()
    {
        //dolaczenie styli
        $this->view->headLink()->appendStylesheet($this->_request->getBaseUrl().'/public/styles/ui/profile.css');
        
        //jezeli uzytkownik zalogowany
        if($this->_auth->hasIdentity())
        {
            //dane z sesji o uzytkowniku
            $storage = new Zend_Session_Namespace('user_data');
            
            //pobranie danych z bazy
            $user_table = new App_Model_User();
            $user_data = $user_table->fetchRow($user_table->select('avatar,wolne_miejsce,limit_wielkosci')->where('userID=?', $storage->id));
            
            //id
            $this->view->id = $storage->id;
            
            //nick
            $this->view->user_name = $this->_auth->getIdentity();
            //avatar
            if($user_data['avatar'] != NULL)
            {
                $this->view->user_avatar_path = "./public/files/" . $storage->id . "/" . $this->avatar_path . "/" . $user_data['avatar'];
            }
            else //jesli brak avatara - w profilu avatar domyslny
            {
                $this->view->user_avatar_path = "public/styles/ui/images/def_avatar.png";
            }
            //miejsce na pliki
            $this->view->user_files_capacity = $user_data['limit_wielkosci'];
            //dostepne miejsce na pliki
            $this->view->user_files_free_capacity = $user_data['wolne_miejsce'];
            
            //pobranie danych o plikach z bazy
            $file_table = new App_Model_File();
            $file_data = $file_table->fetchAll($file_table->select('ilosc_pobran')->where('userID=?', $storage->id));
            
            //liczba plikow dodanych przez uzytkownika
            $this->view->number_of_files = $file_data->count();
            //liczba pobran wszystkich plikow
            $this->view->number_of_file_downloads = 0; //wyzerowanie
            foreach ($file_data as $file) //petla sumujaca pobrania
            {
                $this->view->number_of_file_downloads += $file['ilosc_pobran'];
            }
            
            //rola uzytkownika
            $this->view->user_role = $storage->role;
        }
    }
    
    public function activateAction()
    {
        $request = $this->getRequest();
        
        if($request->isPost())
        {
            $request_post = $request->getPost();
            
            if($request_post['code'] != '' && $request_post['id'] != '')
            {
                $user_table = new App_Model_User();
                $user_data = $user_table->fetchRow($user_table->select('activation_code, wolne_miejsce')->where('userID=?', $request_post['id']));
                
                if(md5($this->salt1 . $request_post['code'] . $this->salt2) == $user_data['activation_code'])
                {
                    $used_space = $user_data['limit_wielkosci'] - $user_data['wolne_miejsce'];
                    
                    $user_table->update(array('rola' => 'premium-user', 'limit_wielkosci' => '5120', 'wolne_miejsce' => (5120 - $used_space)), "userID=" . $request_post['id']);
                    
                    $storage = new Zend_Session_Namespace('user_data');
                    $storage->role = 'premium-user';
                    
                    $this->view->headScript()->appendScript("
                                $(function()
                                {
                                    $('body').append('<div id=\'message-box\' title=\'Informacja\' style=\'text-align:center\'>Kod jest poprawny!<br />Możesz cieszyć się możliowściami użytkownika premium</div>');

                                    //messagebox z ostrzeżeniem
                                    $('#message-box').dialog({
                                        modal : true,
                                        resizable : false,
                                        autoOpen : true,
                                        show : 'fade',
                                        hide : 'fade',
                                        height : 80,
                                        close : function(){
                                            location.href=\"" . $this->view->url(array('id' => $request_post['id']), 'profile') . "\";
                                        }
                                    });
                                });
                    ");
                }
            }
            else
            {
                //komunikat
                $this->view->headScript()->appendScript("
                                $(function()
                                {
                                    $('body').append('<div id=\'message-box\' title=\'Ostrzeżenie\' style=\'text-align:center\'>Nieprawidłowy kod!</div>');

                                    //messagebox z ostrzeżeniem
                                    $('#message-box').dialog({
                                        modal : true,
                                        resizable : false,
                                        autoOpen : true,
                                        show : 'fade',
                                        hide : 'fade',
                                        height : 80
                                    });
                                });
                            ");
                
                //ponowne wyswietlenie formy
                $this->view->headLink()->appendStylesheet($this->_request->getBaseUrl().'/public/styles/ui/tooltip.css');
                $this->view->headScript()->appendFile($this->_request->getBaseUrl().'/public/js/jquery.tools.min.js');

                $this->view->headScript()->appendScript("
                    $(function(){
                        $('.logowanie[title]').tooltip({
                            position : 'center right',
                            effect : 'fade'
                        });
                    });
                ");

                $act_form = new App_Form_Activate();
                $act_form->getElement('id')->setValue($request->getParam('id'));

                $this->view->act_form = $act_form;
            }
        }
        else
        {
            //skrypty i style
            $this->view->headLink()->appendStylesheet($this->_request->getBaseUrl().'/public/styles/ui/tooltip.css');
            $this->view->headScript()->appendFile($this->_request->getBaseUrl().'/public/js/jquery.tools.min.js');
            
            $this->view->headScript()->appendScript("
                $(function(){
                    $('.logowanie[title]').tooltip({
                        position : 'center right',
                        effect : 'fade'
                    });
                });
            ");
            
            $act_form = new App_Form_Activate();
            $act_form->getElement('id')->setValue($request->getParam('id'));

            $this->view->act_form = $act_form;
        }
    }

}