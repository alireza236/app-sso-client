<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Callback extends CI_Controller {
 
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

        if (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
 
            exit('Invalid state');
 
        } else {

            try {

                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);


                // The provider provides a way to get an authenticated API request for
                // the service, using the access token; it returns an object conforming
                // to Psr\Http\Message\RequestInterface.
                $request = $provider->getAuthenticatedRequest(
                    'GET',
                    'https://sso.bekasikota.go.id/api/user',
                    $accessToken,[
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                    ]
                );

                $response = $provider->getParsedResponse($request);

                var_dump($response); // result yang akan di store kedalam session sebagai autentikasi pada aplikasi



            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                // Failed to get the access token or user details.
                exit($e->getMessage());
            }
        }
   } 

}