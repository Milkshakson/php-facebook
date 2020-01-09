<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
    public $client_id;
    public $client_secret;
    
    public function  __construct()
    {
        parent::__construct();   
        include_once('fb-credencial.php');
        $config = array
        (
            'clientId'  => $this->client_id,
            'clientSecret' => $this->client_secret,
            'graphApiVersion'=>'v2.10',
            'redirectUri'=>base_url('welcome/index')
        );
        $this->load->library('facebook2', $config);
    }
    
    public function index()
    {
        
        $this->data['titulo'] = 'Login com Facebook';
         $this->fb();
         $this->load->view('welcome_message', $this->data);
    }
    
    private function fb()
    {
        $provider = $this->facebook2;
        if (!isset($_GET['code'])) {

            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl(array('scope' => 'email'));
            $this->session->set_userdata('oauth2state',$provider->getState());
            
            echo '<a href="'.$authUrl.'">Log in with Facebook!</a>';
            exit;
        
        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $this->session->oauth2state)) {
        
            $this->session->unset_userdata('oauth2state');
            echo 'Invalid state.';
            exit;
        
        }
        
        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        
        // Optional: Now you have a token you can look up a users profile data
        try {
        
            // We got an access token, let's now get the user's details
            $user = $provider->getResourceOwner($token);
        
            // Use these details to create a new profile
            printf('Hello %s!', $user->getFirstName());
            
            $logoutUrl = base_url('welcome/logout');
            echo '<a href="'.$logoutUrl.'">Sair</a>';
            
            
            pre($user);
        
        } catch (\Exception $e) {
        
            // Failed to get user details
            exit('Oh dear...');
        }
        
        echo '<pre>';
        // Use this to interact with an API on the users behalf
        var_dump($token->getToken());
        # string(217) "CAADAppfn3msBAI7tZBLWg...
        
        // The time (in epoch time) when an access token will expire
        var_dump($token->getExpires());
        # int(1436825866)
        echo '</pre>';
    }
    public function logout(){
        $this->session->set_userdata([]);
        redirect(base_url());
    }
}
