<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
    
    
    public function  __construct()
    {
        parent::__construct();   
        include_once('fb-credencial.php');
        $config = array
        (
            'clientId'  => $client_id,
            'clientSecret' => $client_secret,
            'graphApiVersion'=>'v2.10',
            'redirectUri'=>base_url('welcome/retorno')
        );
        $this->load->library('facebook2', $config);
    }
    
    public function index()
    {
        
        $this->data['titulo'] = 'Login com Facebook';
         $this->fb();
         $this->load->view('welcome_message', $this->data);
    }
    public function retorno(){
        $provider = $this->facebook2;
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        $user = $provider->getResourceOwner($token);
        pre($user);
        pre($_REQUEST);
    }
    private function fb()
    {
        $provider = $this->facebook2;
        if (!isset($_GET['code'])) {

            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl(array('scope' => 'email'));
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

    public function acessofacebook() {
        session_destroy();
        ob_start();
        session_start();
        if (empty($_SESSION["UserLogin"])) {

            $config = array
        (
            'clientId'  => '600451487413800',
            'clientSecret' => '330be096f90552ee5618f60599fc2638',
            'graphApiVersion'=>'v2.10',
            'redirectUri'=>base_url('retorno/index')
        );
            $facebook = $this->facebook2;
            $authUrl = $facebook->getAuthorizationUrl([
                "scope" => ["email"]
            ]);
            $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
            // echo $error;
            if ($error) {
                echo 'Erro ao tentar se conectar ao facebook';
            }
            $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);
            if ($code) {
                $token = $facebook->getAccessToken("authorization_code", [
                    "code" => $code
                ]);
                $_SESSION["UserLogin"] = serialize($facebook->getResourceOwner($token));
                $resultado = $facebook->getResourceOwner($token);
                $reg = array();
                //$reg["google_id"] = $resultado->getId();
                $id = $resultado->getId();
                
                $rs_cliente = $bdCliente->getByParam(" where facebook_id = '$id' ");
                $reg_cliente = Tabelas::PrepareDataSet($rs_cliente);
                
                
                 if(is_array($reg_cliente) && count($reg_cliente)>0){
                   
                     // gravar na sessao
                     $_SESSION["sessao_id"] = $resultado->getId();
                     $_SESSION["sessao_nome"] = $resultado->getName();
                     $_SESSION["sessao_nome_ultimo"] = $resultado->getFirstName();
                     $_SESSION["sessao_foto"] = "https://".APP_HOST."/admin/App/Lib/arquivos/fotos/".$reg_cliente[0]["foto"];
                     $_SESSION["sessao_email"] = $resultado->getEmail();
                }else{
                    
                     // gravar na sessao
                     $_SESSION["sessao_id"] = $resultado->getId();
                     $_SESSION["sessao_nome"] = $resultado->getName();
                     $_SESSION["sessao_nome_ultimo"] = $resultado->getFirstName();
                     $_SESSION["sessao_foto"] = $resultado->getPictureUrl();
                     $_SESSION["sessao_email"] = $resultado->getEmail();
                     // gravar no banco
                     $reg["facebook_id"]= $resultado->getId();
                     $reg["nome"] = $resultado->getName();
                     $reg["nome_meio"] = $resultado->getFirstName();
                }
                
               
            
                
                
                
                
                
                header("Location:$authUrl");
            }
            header("Location:$authUrl");
            //echo "<a href='{$authUrl}'>Facebook Login</a>";
        } else {
            $this->render('home/home');
        }
        ob_end_flush();
    }

    
    public function logout(){
        session_start();
        //session_regenerate_id(true);

        session_destroy();
        session_abort();

        unset($_SESSION);

        session_unset();
        redirect(site_url());
    }
}
