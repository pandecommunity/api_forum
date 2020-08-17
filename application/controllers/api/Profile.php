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
class Profile extends CI_Controller {

    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Profile_model','profile');
        $this->load->model('Questions_model','question');
        $this->__resTraitConstruct();
        $this->load->library('image_lib');
        $this->load->library('Authorization_Token');

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    // Get profile
    public function index_get(){
        $id_user = $this->get('id_user');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            if($id_user === null ){
                $ask = $this->profile->get();
            }else{
                $ask = $this->profile->get($id_user);
            }
    
            $question = $this->question->getQuestion($id_user);
            
    
            if($ask){
                $this->response([
                    'status' => true,
                    'data' => $ask,
                    'list_questions' => $question
                ], 201);
            }else{
                $this->response([
                    'status' => false,
                    'message' => 'kd not found'
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

     // update profile
     public function index_post(){
        $id_user = $this->post('id_user');
        $name = $this->post('name');
        $nohp = $this->post('comment');
        $gambar      = $_FILES['gambar']['name'];

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            $folder = './assets/images/';

        $config['upload_path']   = $folder; // lokasi penyimpanan gambar
        $config['allowed_types'] = 'jpg|png|jpeg|webp'; // format gambar yang bisa di upload

        if ($gambar == '') {
            $gambar = "blank_profile.png";
        } else {
            $this->load->library('upload', $config);
            $this->_upload_image('gambar', $id_user);
        }

        $data = [
            'name' => $name,
            'nohp' => $nohp,
            'image_profile' => base_url("assets/images/".$gambar),
            'created_by_updated_at' => date("Y-m-d H:i:s",time())
        ];


        $update = $this->profile->update($data,$id_user);

        if($update > 0){
            $this->response([
                'status' => true,
                'message' => 'update success'
            ],201);
        }else{
            $this->response([
                'status' => false,
                'message'=> 'updated posting failed'
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

     public function index_put(){
        $id = $this->put('id'); 
        $old_password = $this->put('old_password');
        $new_password = $this->put('new_password');

        $check = $this->profile->check($id,$old_password);

        $data = ['password'=> password_hash($new_password, PASSWORD_DEFAULT)];

        if($check === 1){
            $update = $this->profile->update($data,$id);
            if($update > 0){
                $this->response([
                    'status' => true,
                    'message' => 'update success'
                ],201);
            }else{
                $this->response([
                    'status' => false,
                    'message'=> 'updated failed'
                ],400);
    
            }
        }else{
            $this->response([
                'status' => true,
                'message' => 'password gagal di ganti'
            ],201);
        }


     }

    


    protected function _upload_image($key, $id)
    {
        $folder = './assets/images/';
        // echo $folder;
        // die();
        if (!$this->upload->do_upload($key)) {
            echo $this->upload->display_errors();
        } else {
            $image = $this->upload->data('file_name');
            $config['image_library'] = 'gd2';
            $config['source_image'] = $folder . $image;
            $config['create_thumb'] = FALSE;
            $config['maintain_ratio'] = FALSE;
            $config['quality'] = '100%';
            $config['width']  = 800;
            $config['height'] = 800;
            $config['new_image'] = $folder . $image;
            $this->load->library('image_lib', $config);
            $this->image_lib->clear();
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            // $this->load->model('resize_model');
        }
    }



}