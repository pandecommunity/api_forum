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
class Comentar extends CI_Controller {

    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Comentar_model','comentar');
        $this->__resTraitConstruct();
        $this->load->library('Authorization_Token');

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    // post komentar
    public function index_post(){
        $id_pertanyaan = $this->post('id_questions');
        $id_user 	   = $this->post('id_user');
        $komentar = $this->post('comment');	
        
        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            $data = [
                'id_pertanyaan' => $id_pertanyaan,
                'id_user'	=> $id_user,
                'komentar'	=> $komentar,
                'like'	=> "0",
                'is_delete'	=> "0"
            ];
    
            if($this->comentar->insert($data) > 0){
                $this->response([
                    'status' => true,
                    'message' => 'comment success'
                ], 201);
            }else{
                $this->response([
                    'status' => false,
                    'message' => 'comment failled'
                ], 401);
            }        
        } else {
            // faile authentikasi token
            $this->response([
                'status' => false,
                'message' => $is_valid_token['message']
            ], 401);
        }
    
       
    }

    // update komentar
    public function index_put(){
        $id_comment = $this->put('id_comment');
        $id_user = $this->put('id_user');
        $comment = $this->put('comment');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            $data = [
                'id_user' => $id_user,
                'komentar' => $comment,
                'updated_by' => $id_user,
                'created_by_updated_at' => date("Y-m-d H:i:s",time())
            ];
    
    
            $update = $this->comentar->update($data,$id_comment);
    
            if($update > 0){
                $this->response([
                    'status' => true,
                    'message' => 'update success'
                ],201);
            }else{
                $this->response([
                    'status' => false,
                    'message'=> 'update komentar failed'
                ],401);
    
            }
        } else {
            // faile authentikasi token
            $this->response([
                'status' => false,
                'message' => $is_valid_token['message']
            ], 401);
        }

    }

    // ambil comentar berdasarkan pertanyaan
    public function index_get(){
        $id_pertanyaan = $this->get('id_questions');
    

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            if($id_pertanyaan === null ){
                $ask = $this->comentar->get();
            }else{
                $ask = $this->comentar->get($id_pertanyaan);
            }
            if($ask){
                $this->response([
                    'status' => true,
                    'data' => $ask
                ], 200);
            }else{
                $this->response([
                    'status' => false,
                    'message' => 'kd not found'
                ], 404);
            }
        } else {
            // faile authentikasi token
            $this->response([
                'status' => false,
                'message' => $is_valid_token['message']
            ], 401);
        }

        
    }

    // delete comentar
    public function del_put(){
        $id_comment = $this->put('id_comment');
        // $id_pertanyaan = $this->put('id_pertanyaan');
 

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            $data = [
                'is_delete' => 1,
                'created_by_updated_at' => date("Y-m-d H:i:s",time())
            ];
    
    
            $update = $this->comentar->update($data,$id_comment);
    
            if($update > 0){
                $this->response([
                    'status' => true,
                    'message' => 'delete success'
                ],201);
            }else{
                $this->response([
                    'status' => false,
                    'message'=> 'delete posting failed'
                ],401);
    
            }
        } else {
            // faile authentikasi token
            $this->response([
                'status' => false,
                'message' => $is_valid_token['message']
            ], 401);
        }
    }

}