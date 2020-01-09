<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
    
    
    public function  __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        
        $this->data['titulo'] = 'Login com Facebook';
         $this->fb();
         $this->load->view('welcome_message', $this->data);
    }
    
    private function fb()
    {
        $config = array
        (
            'clientId'  => '726421091202048',
            'clientSecret' => '746c9b5afe693487198535c9a3ed661c',
            'graphApiVersion'=>'v2.10',
            'redirectUri'=>base_url('retorno/index')
        );
        $this->load->library('facebook2', $config);
        $provider = $this->facebook2;
        if (!isset($_GET['code'])) {

            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl(array('scope' => 'email','redirect_uri' => site_url('index/fb_auth')));
            $_SESSION['oauth2state'] = $provider->getState();
            
            echo '<a href="'.$authUrl.'">Log in with Facebook!</a>';
            exit;
        
        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
        
            unset($_SESSION['oauth2state']);
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
            
            echo '<pre>';
            var_dump($user);
            # object(League\OAuth2\Client\Provider\FacebookUser)#10 (1) { ...
            echo '</pre>';
        
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
    
    public function fb_auth()
    {
        $config = array
        (
            'appId'  => '726421091202048',
            'secret' => '746c9b5afe693487198535c9a3ed661c'
        );
        $this->load->library('facebook', $config);
        $user = $this->facebook2->getUser();
        if($user)
        {
            try
            {
                $user_profile = $this->facebook2->api('/me');
                $this->session->set_userdata('user_profile', $user_profile);
                redirect(site_url());
            }
            catch (FacebookApiException $e)
            {
                $user = null;
            }
        }
    }
    
    public function logout(){
        $this->session->unset_userdata('user_profile');
        redirect(site_url());
    }
}
