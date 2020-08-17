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
class Category extends CI_Controller {

    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Category_model','category');
        $this->__resTraitConstruct();
        $this->load->library('image_lib');
        $this->load->library('Authorization_Token');

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    // Get category
    public function index_get(){
        $id_category = $this->get('id_category');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) and $is_valid_token['status'] == TRUE) {
            if($id_category === null ){
                $category = $this->category->get();
            }else{
                $category = $this->category->get($id_category);
            }
    

            if($category){
                $this->response([
                    'status' => true,
                    'data' => $category,
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

   


}