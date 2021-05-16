<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authsso extends CI_Controller {

   public function index() {

        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => '',    //  client ID 
            'clientSecret'            => '',    //  client secret  
            'redirectUri'             => '', // ini adalah URI callback untuk men-generate token setelah proses autorisasi berhasil. 
                                            //nama URI ini adalah nama URI aplikasi klien contoh   https://sikerja.bekasikota.go.id/callback
            
            'urlAuthorize'            => 'https://sso.bekasikota.go.id/oauth/authorize', // URI untuk authorize
            'urlAccessToken'          => 'https://sso.bekasikota.go.id/oauth/token',    // URI untuk mendaptakan token dari SSO server 
            'urlResourceOwnerDetails' => 'https://sso.bekasikota.go.id/api/user'       // URI endpoint untuk get data user info dari SSO server
        ]);

     try {
        
            // If we don't have an authorization code then get one
            if (!isset($_GET['code'])) {

                // Fetch the authorization URL from the provider; this returns the
                // urlAuthorize option and generates and applies any necessary parameters
                // (e.g. state).
                $authorizationUrl = $provider->getAuthorizationUrl();

                // Get the state generated for you and store it to the session.
                $_SESSION['oauth2state'] = $provider->getState();

                // Redirect the user to the authorization URL.
                header('Location: ' . $authorizationUrl);
                exit;
            } 

        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
           // Failed to get the access token or user details.
            exit($e->getMessage());
       }
    }
}

