<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */

//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

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
class Like extends CI_Controller {

    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Like_model','like');
        $this->__resTraitConstruct();
        $this->load->library('Authorization_Token');

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }


    public function index_post(){
        $id_pertanyaan = $this->post('id_pertanyaan');
        $id_user	   = $this->post('id_user');


        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            if($this->like->checkLike($id_pertanyaan,$id_user)){
                // data like ditemukan
                    if($this->like->delete($id_pertanyaan,$id_user) > 0){
                        $this->response([
                            'status' => true,
                            'message' => 'like berhasil di hapus'
                        ], 201);
                    }else{
                        $this->response([
                            'status' => false,
                            'message' => 'like gagal dihapus'
                        ], 401);
                    } 
            }else{
                //data like tidak ditemukan
                $data = [
                    'id_pertanyaan' => $id_pertanyaan,
                    'id_user'	 => $id_user
                ];
    
                if($this->like->insert($data) > 0){
                    $this->response([
                        'status' => true,
                        'message' => 'like berhasil di tambahkan'
                    ], 201);
                }else{
                    $this->response([
                        'status' => false,
                        'message' => 'like gagal ditambahkan'
                    ], 401);
                }
           
            }
        } else {
            // faile authentikasi token
            $this->response([
                'status' => false,
                'message' => $is_valid_token['message']
            ], 401);
        }
        
    }

    public function commentlike_post(){
        $id_komentar = $this->post('id_komentar');
        $id_user	 = $this->post('id_user');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            if($this->like->checkLikecomment($id_komentar,$id_user)){
                // data like ditemukan
                    if($this->like->deleteLikeComment($id_komentar,$id_user) > 0){
                        $this->response([
                            'status' => true,
                            'message' => 'like berhasil di hapus'
                        ], 201);
                    }else{
                        $this->response([
                            'status' => false,
                            'message' => 'like gagal dihapus'
                        ], 401);
                    } 
            }else{
                //data like tidak ditemukan
                $data = [
                    'id_komentar' => $id_komentar,
                    'id_user'	 => $id_user
                ];
    
                if($this->like->insertlikecomment($data) > 0){
                    $this->response([
                        'status' => true,
                        'message' => 'like berhasil di tambahkan'
                    ], 201);
                }else{
                    $this->response([
                        'status' => false,
                        'message' => 'like gagal ditambahkan'
                    ], 401);
                }
           
            }     
        } else {
            // faile authentikasi token
            $this->response([
                'status' => false,
                'message' => $is_valid_token['message']
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