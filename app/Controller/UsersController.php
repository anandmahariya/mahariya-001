<?php
set_include_path(APP."Vendor/" . PATH_SEPARATOR . get_include_path());
App::import('Vendor', 'facebook/facebook');
App::import('Vendor', 'Google/Client');
App::import('Vendor', 'Google/Service/Plus');
class UsersController extends AppController {
    
    public $components = array('RequestHandler','Common');
    public $uses = array('User');
    public $facebook = null;
    public $google = null;
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $this->Auth->allow(array('login','socialconnect','logout'));
        $this->Facebook = new Facebook(array('appId'=>'125855137445175','secret'=>'55b511a667a71b21feb2998ad80e0a04'));
    }
    
    public function index() {
       //$this->redirect(array('controller'=>'dashboard','action'=>'index'));
       //exit;
    }
    
    public function profile() {
        
    }
    
    public function changelanguage($lang = null) {
        $lang = $lang != null ? $lang : 'english';
        switch($lang){
            case 'hindi' :
                $this->Cookie->write('Lang','hindi');
                $this->Session->write('Config.language','hindi');
                break;
            default :
                $this->Cookie->write('Lang','english');
                $this->Session->write('Config.language','english');
                break;
        }
        
        //Save language in user table
        $user =  $this->Session->read('Auth.User');
        $user['language'] = $lang;
        if($this->User->save($user)){
            $this->Session->setFlash(__('Language changed successfully'),'success');
        }
        
        $this->redirect($this->referer());
        exit;
    }
    
    public function login() {
        //echo Security::hash('sunil78645', null, true);
        $this->layout = 'login';
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                if(isset($this->data['User']['remember_me']) && $this->data['User']['remember_me'] == 1){
                    $this->Cookie->write('User',array('username' => $this->data['User']['username'],
                                                      'password' => $this->data['User']['password'],
                                                      'remember_me'=>1));
                   
                }else{
                    $this->data = null;
                    $this->Cookie->delete('User');
                }
                return $this->redirect($this->Auth->redirect());
            }
            $this->Session->setFlash(__('Invalid username or password, try again'));
        }
        
        // Read cookies data
        $this->request->data['User'] = $this->Cookie->read('User');
    }
    
    public function socialconnect(){
        $vendor = $_GET['provider'];
        switch($vendor){
            case 'facebook' :
                if($this->request->query('code')){
                    $fb_user = $this->Facebook->getUser();
                    if ($fb_user){
                        $fb_user = $this->Facebook->api('/me');
                        echo '<pre>';print_r($fb_user);echo '</pre>';
                        exit;
                    }
                }else{
                    $redirect_uri = Router::url(array('controller' => 'users', 'action' => 'socialconnect','?'=>array('provider'=>'facebook')),true);
                    $this->redirect($this->Facebook->getLoginUrl(array('scope'=>'email,user_birthday,user_location,user_hometown,user_photos','redirect_uri' =>$redirect_uri)));
                    exit;
                }
                break;
            case 'google' :
                $redirect_uri = Router::url(array('controller' => 'users', 'action' => 'socialconnect','?'=>array('provider'=>'google')),true);
                $client = new Google_Client();
                $client->setClientId('149842056548-tr4qnq93v29n2ud0aou4cr7tu3sd1d51.apps.googleusercontent.com');
                $client->setClientSecret('SYLJ3LxHpK9HKbJMDR_Xrsq_');
                $client->setRedirectUri($redirect_uri);
                $client->setScopes('email');
                
                if($this->request->query('code')){
                    $client->authenticate($_GET['code']);
                    $_SESSION['social_login']['access_token'] = $client->getAccessToken();
                    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
                    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
                }elseif(isset($_SESSION['social_login']['access_token']) && $_SESSION['social_login']['access_token']){
                    $client->setAccessToken($_SESSION['social_login']['access_token']);
                    $plus = new Google_Service_Plus($client);
                    $person = $plus->people->get('me');
                    echo '<pre>';print_r($person);echo '</pre>';
                    exit;
                }else{
                    $this->redirect($client->createAuthUrl());
                    exit;
                }
                break;
        }
    }
    
    function logout() {
       $this->redirect($this->Auth->logout());
    }
}