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
class Questions extends CI_Controller {

    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Questions_model','question');
        $this->load->model('Upload_model','uploads');
        $this->__resTraitConstruct();
        $this->load->library('Authorization_Token');

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    // Get Pertanyaan
    public function index_get(){
        $id_category = $this->get('id_category');

        $catalog_arr = array();

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            if($id_category === null ){
                $ask = $this->question->get();
            }else{
                $ask = $this->question->get($id_category);
            }
            
    
            if($ask){
                // data ditemukan
                foreach($ask as $row){
                    $image = $this->question->getImage($row["id_pertanyaan"]);
                    $data_catalog = array(
                        "id_pertanyaan"=> $row["id_pertanyaan"],
                        "id_user"=> $row["id_user"],
                        "name_user"=> $row["name_user"],
                        "category"=> $row["category"],
                        "url_image_profile"=> $row["url_image"],
                        "like"=> $row["like"],
                        "comment"=> $row["comment"],
                        "pertanyaan"=> $row["pertanyaan"],
                        "created_at"=> $row["created_at"],
                        "images" => $image
                    );
                    array_push($catalog_arr,$data_catalog);
                }
                $this->response([
                    'status' => true,
                    'data' => $catalog_arr
                ], 200);
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

    //upvote pertanyaan
    public function upvote_get(){
        $ask = $this->question->upVote();
        
        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            if($ask){
                $this->response([
                    'status' => true,
                    'data' => $ask
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

    // post pertanyaan
    public function index_post(){
        $id_user 	 = $this->post('id_user');
		$id_category = $this->post('id_category');	
        $questions	 = $this->post('questions');
        $gambar      = count($_FILES['gambar']['name']);
        $folder      = './assets/images/';


        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            $data = [
                'id_user'		=> $id_user,
                'id_category'	=> $id_category,
                'pertanyaan'	=> $questions,
                'like' => 0,
                'comment' => 0,
                'is_delete'	=> "0"
            ];
    
            $insert = $this->question->insert($data);
            if($insert > 0){
                for($i=0; $i<$gambar; $i++){
                    $_FILES['file']['name'] = $_FILES['gambar']['name'][$i];
                    $_FILES['file']['type'] = $_FILES['gambar']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['gambar']['tmp_name'][$i];
                    $_FILES['file']['size'] = $_FILES['gambar']['size'][$i];
        
                    $config['upload_path']   = $folder; // lokasi penyimpanan gambar
                    $config['allowed_types'] = 'jpg|png|jpeg|webp'; // format gambar yang bisa di upload
        
                    //memanggil library upload
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config);
        
                    if($this->upload->do_upload('file')){
                        $fileData = $this->upload->data();
        
                        //resize ukuran gambar
                        $image = $fileData['file_name'];
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
        
                        $uploadData[$i]['url_image'] = base_url('assets/images/'.$fileData['file_name']);
                        $uploadData[$i]['id_pertanyaan'] = $insert;//id pertanyaan
                    }
                }
        
                if($uploadData !== null){
                    $insert = $this->uploads->insert($uploadData);
        
                    if($insert > 0){
                        $this->response([
                            'status' => true,
                            'message' => 'insert success'
                        ],201);
                    }else{
                        $this->response([
                            'status' => false,
                            'message'=> 'insert posting failed'
                        ],401);
            
                    }
                }
            }else{
                $this->response([
                    'status' => false,
                    'message' => 'postingan failled'
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

    // update pertanyaan
    public function index_put(){
        $id_ask = $this->put('id_questions');
        $id_category = $this->put('id_category');
        $ask = $this->put('ask');
        $update_by = $this->put('id_user');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            $data = [
                'id_category' => $id_category,
                'pertanyaan' => $ask,
                'created_by_updated_at' => date("Y-m-d H:i:s",time()),
                'updated_by' => $update_by,
            ];
    
            $update = $this->question->update($data,$id_ask);
    
            if($update > 0){
                $this->response([
                    'status' => true,
                    'message' => 'success'
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

      // delete questions
      public function del_put(){
        $id_questions = $this->put('id_quetions');
 
        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            $data = [
                'is_delete' => 1,
                'created_by_updated_at' => date("Y-m-d H:i:s",time())
            ];
    
    
            $update = $this->question->update($data,$id_questions);
    
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