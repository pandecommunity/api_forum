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
class Upload extends CI_Controller {

    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Upload_model','uploads');
        $this->__resTraitConstruct();
        $this->load->library('image_lib');

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

 

     // update profile
     public function index_post(){
        $id_pertanyaan = $this->post('id_pertanyaan');
        $gambar      = count($_FILES['gambar']['name']);
        $folder = './assets/images/';

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
                $uploadData[$i]['id_pertanyaan'] = $id_pertanyaan;
            }
        }

        if($uploadData !== null){
            $insert = $this->uploads->insert($uploadData);

            if($insert > 0){
                $this->response([
                    'status' => true,
                    'message' => 'update success'
                ],201);
            }else{
                $this->response([
                    'status' => false,
                    'message'=> 'updated posting failed'
                ],400);
    
            }
        }
        

     }

    protected function _upload_image($key)
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