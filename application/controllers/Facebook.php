<?php defined('BASEPATH') OR exit('No direct script access allowed');
use League\OAuth2\Client\Provider\Facebook;
class facebook extends CI_Controller {

    
    public function  __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {   
        
        $this->data['titulo'] = 'Login com Facebook';
        $config = array
        (
            'appId'  => '726421091202048',
            'secret' => '746c9b5afe693487198535c9a3ed661c'
        );
        
        $this->load->library('facebook', $config);
       /* $this->fb();
        $this->load->view('header', $this->data);
        $this->load->view('index', $this->data);
        $this->load->view('footer');
        */
    }

    private function fb()
    {
	$config = array
        (
            'appId'  => '726421091202048',
            'secret' => '746c9b5afe693487198535c9a3ed661c'
    	);
	$this->load->library('facebook', $config);
	$user = $this->facebook->getUser();
	$loginParams = array('scope' => 'email','redirect_uri' => site_url().'/index/fb_auth');
	$this->data['login_url'] = $this->facebook->getLoginUrl($loginParams);
	$logoutParams = array( 'next' => site_url().'/index/logout');
	$this->data['logout_url'] = $this->facebook->getLogoutUrl($logoutParams);
    }
	
    public function fb_auth()
    {
	$config = array
        (
            'appId'  => '726421091202048',
            'secret' => '746c9b5afe693487198535c9a3ed661c'
        );
	$this->load->library('facebook', $config);
	$user = $this->facebook->getUser();
	if($user)
        {
            try 
            {
                $user_profile = $this->facebook->api('/me');
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
	