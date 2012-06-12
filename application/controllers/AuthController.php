<?php

class AuthController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();

        //utworzenie obiektu przechowujacego w sesji role uzytkownika
        $this->storage = new Zend_Session_Namespace('user_data');
            
        $this->salt1 = "dfk-s343qe95qwe4vcv!xc13";
        $this->salt2 = 'zvcv@$vhhajjh90-vxcx';
    }
    
    public function indexAction()
    {
        //newsy
        $news_table = new App_Model_News();
        $news = $news_table->fetchAll($news_table->select()->order('data DESC'));
        $this->view->newses = $news;
        
        $this->view->logerror = '';
        
        if($this->_auth->hasIdentity())
        {
            $this->_helper->redirector('index', 'index');
        }
        
        $form = new App_Form_Login();
        $form->setAttrib('action', $this->view->url(array(), 'login'));
        
        $request = $this->getRequest()->getPost();
        
        if($request)
        {
            //filtr na login i haslo przepuszczajacy tylko litery i cyfry
            $filter = new Zend_Filter_Alnum();
            
            $login = $filter->filter($request['login']);
            $password = $filter->filter($request['password']);
            
            $validation = new Zend_Auth_Adapter_DbTable(
                    null,
                    'Users',
                    'nick',
                    'password'
            ); //na hosting
            
//            $validation = new Zend_Auth_Adapter_DbTable(
//                    null,
//                    'users',
//                    'login',
//                    'password'
//            );
            
            if($login != '')
            {
                $validation->setIdentity($login);
                $validation->setCredential(md5($password));
                
                //autentykacja na podstawie obiektu tabeli walidujacej
                $validate = $this->_auth->authenticate($validation);

                //jesli walidacja sie powiodla
                if($validate->isValid())
                {
                    //utworzenie obiektu tabeli uzytkownikow z bazy danych
                    $user = new App_Model_User();
                    $user_data = $user->fetchRow($user->select('rola,userID')->where('nick=\'' . $this->_auth->getIdentity() . '\' AND password=\'' . md5($password) . '\''));
                    //zapisanie roli do sesji
                    $this->storage->role = $user_data['rola'];
                    //zapisanie id do sesji
                    $this->storage->id = $user_data['userID'];
                    //$this->view->user_data = $validation->getResultRowObject();
                    $this->_helper->redirector->gotoRoute(array('id' => $user_data['userID']), 'profile');
                }
                else
                {
                    //probujemy z haslem bez md5-usunac to pozniej
                    $validation->setCredential($password);

                    $validate = $this->_auth->authenticate($validation);

                    if($validate->isValid())
                    {
                        //utworzenie obiektu tabeli uzytkownikow z bazy danych
                        $user = new App_Model_User();
                        $user_data = $user->fetchRow($user->select('rola,userID')->where('nick=\''.$this->_auth->getIdentity().'\' AND password=\''.$password.'\''));
                        //zapisanie roli do sesji
                        $this->storage->role = $user_data['rola'];
                        //zapisanie id do sesji
                        $this->storage->id = $user_data['userID'];
                        
                        $this->_helper->redirector->gotoRoute(array('id' => $user_data['userID']), 'profile');
                    }

                    $this->view->logerror = "Nieprawidłowy login i/lub hasło!";
                }
            }
            else
            {
                $this->view->logerror = "Nieprawidłowy login i/lub hasło!";
            }
        }
        
        $this->view->form = $form;
    }
    
    public function logoutAction()
    {
        $this->_auth->clearIdentity();
        $this->storage->role = "not-logged";
        unset($this->storage->id);
        $this->_helper->redirector->goToRoute(array(), 'login');
    }
    
    public function registerAction()
    {
        if(!$this->_auth->hasIdentity())
        {
            //id nowego uzytkownika
            $user = new App_Model_User();
            $user_data = $user->fetchAll($user->select('userID'));
            
            foreach($user_data as $id)
            {
                $user_id = $id['userID'];
            }
            $id = $user_id + 1;
            
            //skrypty i style
            $this->view->headLink()->appendStylesheet($this->_request->getBaseUrl().'/public/styles/ui/tooltip.css');
            $this->view->headLink()->appendStylesheet($this->_request->getBaseUrl().'/public/styles/ui/uploadify/uploadify.css');
            $this->view->headScript()->appendFile($this->_request->getBaseUrl().'/public/js/jquery.tools.min.js');
            $this->view->headScript()->appendFile($this->_request->getBaseUrl().'/public/js/jquery.uploadify-3.1.min.js');
            $this->view->headScript()->appendScript("
                $(function(){
                    $('.logowanie[title]').tooltip({
                        position : 'center right',
                        effect : 'fade'
                    });
                    
                    $('#avatar_file').uploadify({
                        'swf'      : 'public/swf/uploadify.swf',
                        'uploader' : 'public/php_scripts/uploadify.php',
                        'auto' : false,
                        'fileTypeDesc' : 'Image Files',
                        'fileTypeExts' : '*.gif; *.jpg; *.png',
                        'multi' : false,
                        'queueSizeLimit' : 1,
                        'method'   : 'post',
                        'formData' : {
                            'user_id' : '" . $id . "',
                            'avatar_path' : '" . Zend_Registry::get('avatar_path') . "',
                            'base_url' : '" . $this->_request->getBaseUrl() . "'
                        },
                        'onFallback' : function() {
                            $('body').append('<div id=\'message-box\' title=\'Błąd\' style=\'text-align:center\'>Zainstaluj <a href=\"http://www.adobe.com/go/getflashplayer\" target=\"_blank\">Flash Player</a> !</div>');

                            //messagebox z informacja
                            $('#message-box').dialog({
                                modal : true,
                                resizable : false,
                                autoOpen : true,
                                show : 'fade',
                                hide : 'fade',
                                height : 80
                            });
                        },
                        'onUploadError' : function(file, errorCode, errorMsg, errorString) {
                            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
                        },
                        'overrideEvents' : ['onDialogOpen'],
                        'onSelectError' : function(file, errorCode, errorMsg) {
                            if(errorCode == -100)
                            {
                                $('body').append('<div id=\'message-box\' title=\'Błąd\' style=\'text-align:center\'>Możesz wybrać tylko jeden plik!</div>');
                                
                                //messagebox z informacja
                                $('#message-box').dialog({
                                    modal : true,
                                    resizable : false,
                                    autoOpen : true,
                                    show : 'fade',
                                    hide : 'fade',
                                    height : 80
                                });
                            }
                            else
                            {
                                alert(errorMsg);
                            }
                        },
                        'onUploadSuccess' : function(file, data, response) {
                                        $.post('" . $this->view->url(array(), 'register_avatar') . "', { avatar_path : data, id : " . $id . " }, function(data){
                                                $('body').append('<div id=\'message-box\' title=\'Informacja\' style=\'text-align:center\'>Pomyślnie załadowano avatar</div>');

                                                //messagebox z informacja
                                                $('#message-box').dialog({
                                                    modal : true,
                                                    resizable : false,
                                                    autoOpen : true,
                                                    show : 'fade',
                                                    hide : 'fade',
                                                    height : 80,
                                                    close: function(event, ui) { //wyslanie formularza po zamknieciu okienka
                                                        $('#regform').submit();
                                                    }
                                                });
                                            });
                        }
                    });
                });
            ");
            
            //flaga dla akcji rejestracji
            $this->view->register = 1;
            
            $request = $this->getRequest();
            
            //dalsze wyswietlanie z boku formatki logowania
            $log_form = new App_Form_Login();
            $log_form->setAttrib('action', $this->view->url(array(), 'login'));
            
            $this->view->form = $log_form;
            
            if($request->isPost())
            {
                //TYMCZASOWE
                $storage = new Zend_Session_Namespace('register_data');
                
                //komunikat(TYMCZASOWY)
                $this->view->headScript()->appendScript("
                                $(function()
                                {
                                    $('body').append('<div id=\'message-box\' title=\'Info\' style=\'text-align:center\'>Kod to: " . $storage->act_code . "</div>');

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
                
                $post_request = $request->getPost();
                
                if($post_request['login'] != '' && $post_request['password'] != '')
                {
                    //obiekt captcha
                    $storage = new Zend_Session_Namespace('register_data');
                    
                    //odpowiedz z captcha
                    $captcha_response = array('recaptcha_challenge_field' => $post_request['recaptcha_challenge_field'], 
                                              'recaptcha_response_field' => $post_request['recaptcha_response_field']);
                    
                    if($storage->captcha->isValid($captcha_response))
                    {
                        //filtr na login i haslo przepuszczajacy tylko litery i cyfry
                        $filter = new Zend_Filter_Alnum();

                        $login = $filter->filter($post_request['login']);
                        $password = $filter->filter($post_request['password']);

                        //wstawienie do bazy danych
                        $new_user_data = array(
                            'nick' => $login,
                            'password' => md5($password)
                        );
                        
                        $user->update($new_user_data, "userID=" . ($id - 1));
                        
                        //przepisanie loginu do formularza logowania
                        $login_value = $log_form->getElement('login');
                        $login_value->setValue($login);
                        
                        $this->view->headScript()->appendScript("
                            $(function()
                            {
                                $('body').append('<div id=\'message-box\' title=\'Sukces\' style=\'text-align:center\'>Pomyślnie zakończono rejestrację!<br />Możesz teraz zalogować się na swoje konto</div>');

                                //messagebox z informacja
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
                        //print_r($new_user_data);
                        //print_r($file_name);
                    }
                    else //jeszcze raz wyswietlamy formularz, gdy captcha zle wypelniona
                    {
                        //kasowanie tymczasowych danych z bazy
                        $user->delete("userID=" . ($id - 1));
                        
                        $reg_form = new App_Form_Register();
                        $submit_but = $reg_form->getElement('sub-but');
                        $submit_but->setAttrib('onclick', '$(\'#avatar_file\').uploadify(\'upload\', \'*\');');
                        
                        //przepisanie loginu
                        $login_value = $reg_form->getElement('login');
                        $login_value->setValue($post_request['login']);

                        //obiekt captcha
                        $storage = new Zend_Session_Namespace('register_data');
                        $storage->captcha = $reg_form->getElement('recaptcha');

                        $this->view->reg_form = $reg_form;
                        
                        $this->view->headScript()->appendScript("
                            $(function()
                            {
                                $('body').append('<div id=\'message-box\' title=\'Ostrzeżenie\' style=\'text-align:center\'>Wypełnij captcha poprawnie!</div>');

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
                    }
                }
                else
                {
                    //kasowanie tymczasowych danych z bazy
                    $user->delete("userID=" . ($id - 1));
                    
                    $reg_form = new App_Form_Register();
                    $submit_but = $reg_form->getElement('sub-but');
                    $submit_but->setAttrib('onclick', '$(\'#avatar_file\').uploadify(\'upload\', \'*\');');
                
                    $this->view->reg_form = $reg_form;
                    
                    $this->view->headScript()->appendScript("
                            $(function()
                            {
                                $('body').append('<div id=\'message-box\' title=\'Ostrzeżenie\' style=\'text-align:center\'>Wypełnij wszystkie wymagane pola!</div>');

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
                    
                    //podswietlenie niewypelnionych pol
                    if($post_request['login'] == '' && $post_request['password'] == '')
                    {
                        $this->view->headScript()->appendScript("
                            $(function()
                            {
                                $('#login').css({'outline' : 'solid thick red'});
                                $('#password').css({'outline' : 'solid thick red'});
                            });
                        ");
                    }
                    else if($post_request['login'] == '')
                    {
                        $this->view->headScript()->appendScript("
                            $(function()
                            {
                                $('#login').css({'outline' : 'solid thick red'});
                            });
                        ");
                    }
                    else if($post_request['password'] == '')
                    {
                        $this->view->headScript()->appendScript("
                            $(function()
                            {
                                $('#password').css({'outline' : 'solid thick red'});
                            });
                        ");
                        
                        //przepisanie loginu
                        $login_value = $reg_form->getElement('login');
                        $login_value->setValue($post_request['login']);
                    }
                }
            }
            else
            {
                //id nowego uzytkownika
                $user = new App_Model_User();
                $user_data = $user->fetchAll($user->select('userID'));

                foreach($user_data as $id)
                {
                    $user_id = $id['userID'];
                }
                $id = $user_id + 1;

                //jezeli katalog usera nie istnieje, zostaje utworzony
                if (!file_exists("files/" . $id))
                {
                    mkdir("files/" . $id);
                }

                //gdy nie istnieje katalog avatara, tworzymy go
                if (!file_exists("files/" . $id . "/" . Zend_Registry::get('avatar_path')))
                {
                    mkdir("files/" . $id . "/" . Zend_Registry::get('avatar_path'));
                }
                
                $reg_form = new App_Form_Register();
                $submit_but = $reg_form->getElement('sub-but');
                $submit_but->setAttrib('onclick', '$(\'#avatar_file\').uploadify(\'upload\', \'*\');');
                
                //obiekt captcha
                $storage = new Zend_Session_Namespace('register_data');
                $storage->captcha = $reg_form->getElement('recaptcha');
                
                $this->view->reg_form = $reg_form;
            }
        }
    }
    
    public function registeravatarAction()
    {
        //zablokowanie wyswietlania domyslnego widoku i layoutu
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        $request = $this->getRequest();
        
        if($request->isPost())
        {
            $post_request = $request->getPost();
            
            if($post_request['id'] != '' && $post_request['avatar_path'] != '')
            {
                $user_table = new App_Model_User();
                
                //kod aktywacyjny
                $code = $this->generatePassword($post_request['id'], 5);
                
                //zapamietanie kodu(TYMCZASOWE)
                $storage = new Zend_Session_Namespace('register_data');
                $storage->act_code = $code;
                
                //wstawienie do bazy danych
                $new_user_data = array(
                    'userID' => $post_request['id'],
                    'nick' => 'login',
                    'password' => 'password',
                    'rola' => 'user',
                    'wolne_miejsce' => 1024,
                    'limit_wielkosci' => 1024,
                    'avatar' => $post_request['avatar_path'],
                    'activation_code' => md5($this->salt1 . $code . $this->salt2)
                );
                
                $user_table->insert($new_user_data);
                
                echo '1';
            }
            else
            {
                echo '0';
            }
        }
        else
        {
            echo '0';
        }
    }
    
    public function generatePassword($id,$length)
    {   
        $chars = array('a','b','c','d','e','f','g','h','1','2','3','4','5','6','7','8','9','0');
        $special_chars = array('@','$','%','!','#');

        $code_chars = array();
        $code = "";
        $last = array();
        $i = 0;

        //initializing array
        for($j=0;$j<$length;$j++)
        {
            $last += array($j => 0);
        }
        while($i != $length)
        {
            $next = rand(0, $length - 1);
            if($last[$next] == 0 && $i != 0 && $id % $i == 0)
            {
                $code_chars[$next] = $special_chars[rand(0, 4)];
                $last[$next] = 1;
                $i++;
            }
            else if($last[$next] == 0)
            {
                $code_chars[$next] = $chars[rand(0, 17)];
                $last[$next] = 1;
                $i++;
            }
        }

        for($i=0;$i<$length;$i++)
        {
            $code .= $code_chars[$i];
        }

        return $code;
    }
}

?>
