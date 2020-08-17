<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */

//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
date_default_timezone_set("Asia/Jakarta");

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Auth extends CI_Controller {

    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Auth_model','auth');
        $this->__resTraitConstruct();
        $this->load->library('Authorization_Token');

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function register_post(){
        $email 	   = $this->post('email');
		$password  = $this->post('password');	
		$nama	   = $this->post('name');
        $nohp	   = $this->post('nohp');


        $checkemail = $this->auth->checkemail($email);
        $checknohp  = $this->auth->checknohp($nohp);

        
        $data = [
            'email'		=> $email,
			'password'	=> password_hash($password, PASSWORD_DEFAULT),
			'name'	    => $nama,
			'image_profile'	=> "blank_profile.png",
			'nohp'		=> $nohp,
			'is_delete'	=> "0"
        ];

        if ($checkemail) {
            $this->response([
                'status'  => false,
                'error'   => [
                    "message"  => "email sudah tersedia",
                    "code"     => "20"
                ]
            ], 404);
        } elseif ($checknohp) {
            $this->response([
                'status'  => false,
                'error'   => [
                    "message"  => "nohp sudah tersedia",
                    "code"     => "21"
                ]
            ], 404);
        } else {
            if($this->auth->register($data) > 0){
                $this->response([
                    'status' => true,
                    'message' => 'register success'
                ], 201);
            }else{
                $this->response([
                    'status' => false,
                    'message' => 'register failled'
                ], 400);
            }
        }

        
    }

    public function login_post(){
        // $email 	    = $this->post('email');
        // $password   = $this->post('password');
        $token_fcm  = $this->post('token_fcm');
        $token_google  = $this->post('token_google');

        $url_check_token = "https://oauth2.googleapis.com/tokeninfo?id_token=".$token_google;

        $client = curl_init($url_check_token);

        curl_setopt($client,CURLOPT_RETURNTRANSFER,true);

        $response =curl_exec($client);
        $http_code = curl_getinfo($client, CURLINFO_HTTP_CODE);

        curl_close($client);

        $result = json_decode($response);

        $user_data = array();

        if($http_code == 200){     
        
            $checkemail = $this->auth->checkemail($result->email);
        
            if ($checkemail) {
                $data = [
                    'email'		    => $result->email,
                    'name'	        => $result->name,
                    'image_profile'	=> $result->picture,
                    'token_fcm' => $token_fcm,
                    'is_delete'	=> "0",
                    'created_by_updated_at' => date("Y-m-d H:i:s",time())
                ];
                 
                if($this->auth->updateFCM($data,$result->email) > 0){
             
                    $login = $this->auth->login($result->email);

                    
                    $token_data['id']   = $login->id;
                    $token_data['name'] = $login->name;
                    $token_data['email']  = $login->email;
                    $token_data['token_fcm'] = $login->token_fcm;
                    $token_data['image_profile']  = $login->image_profile;
                    $token_data['nohp']  = $login->nohp;
                    $token_data['time'] = time();

                     // generate token
                     $user_token = $this->authorization_token->generateToken($token_data);


                    $this->response([
                        'status' => true,
                        'token status' => "update",
                        'data' => $login,
                        'token_auth' => $user_token,
                    ], 200); 
                }else{
                    $this->response([
                        'status' => true,
                        'token status' => "eror",
                    ], 200); 
                } 
            }else {
                $data = [
                    'email'		    => $result->email,
                    'name'	        => $result->name,
                    'image_profile'	=> $result->picture,
                    'is_delete'	=> "0"
                ];
                if($this->auth->register($data) > 0){
                    $token = ['token_fcm' => $token_fcm];
                    $this->auth->updateFCM($token,$result->email);
                  
                    $login = $this->auth->login($result->email);

                    $token_data['id']   = $login->id;
                    $token_data['name'] = $login->name;
                    $token_data['email']  = $login->email;
                    $token_data['token_fcm'] = $login->token_fcm;
                    $token_data['image_profile']  = $login->image_profile;
                    $token_data['nohp']  = $login->nohp;
                    $token_data['time'] = time();

                     // generate token
                     $user_token = $this->authorization_token->generateToken($token_data);

            

                    $this->response([
                        'status' => true,
                        'token status' => "register",
                        'data' => $login,
                        'token_auth' => $user_token,
                    ], 200); 
                } 
            }
    
        }else{
            $this->response([
                'status' => false,
                'error' => $result->error,
                'error_description' => $result->error_description,
            ], 401); 
        }
        
        
    }

    public function users_post()
    {
        // $this->some_model->update_user( ... );
        $message = [
            'id' => 100, // Automatically generated by the model
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'message' => 'Added a resource'
        ];

        $this->set_response($message, 201); // CREATED (201) being the HTTP response code
    }

    public function users_delete()
    {
        $id = (int) $this->get('id');

        // Validate the id.
        if ($id <= 0)
        {
            // Set the response and exit
            $this->response(null, 400); // BAD_REQUEST (400) being the HTTP response code
        }

        // $this->some_model->delete_something($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, 204); // NO_CONTENT (204) being the HTTP response code
    }

}