<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Callback extends CI_Controller {
 
   public function index() {

        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => '',  //  client ID 
            'clientSecret'            => '',  //  client secret  
            'redirectUri'             => '',  // ini adalah URI callback untuk men-generate token setelah proses autorisasi berhasil. 
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

                // dapetin access token menggunakan authorization code grant dari authorization server dan tampung di variabel $accessToken.
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);


                // lakukan Fetch data berupa user info dari authorization server dengan menggunakan acces token yg sudah didapat pd variable $accessToken
                $request = $provider->getAuthenticatedRequest(
                    'GET',
                    'https://sso.bekasikota.go.id/api/user',
                    $accessToken,[
                        "Accept" => "application/json",
                        "Content-Type" => "application/json",
                    ]
                );

                $response = $provider->getParsedResponse($request);  // proses parsing data user dari JSON ke array dan tampung di variable $response 

                var_dump($response); // result yang akan di store kedalam session untuk autentikasi pada aplikasi

                /* 
                   Note : sebelum menyimpan data user ke auth session lakukan proses kueri data dgn menggunakan NIP untuk memastikan ada kesesuaian data antara SSO server dgn aplikasi klien
                          apabila data tidak sesuai maka lakukan redirect ke http://sso.bekasikota.go.id/authsso/failed, dan apabila  data berdasarkan NIP sesuai antara SSO server 
                          dgn aplikasi klien maka simpan  ke dalam session sebagai autentikasi..
                          
                          $nip = $response['nip]
                          query NIP in database... 

                          if (isset($nip)) {
                                  redirect('/home');
                            } else {
                                //abort(403, 'Unauthorized.');
                                redirect('http://sso.bekasikota.go.id/authsso/failed');
                            }
                */


            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                // Failed to get the access token or user details.
                exit($e->getMessage());
            }
        }
   } 

}