<?php

class Category_model extends CI_Model{
    public function get($id = null){
        if($id === null){
            return $this->db->get('tbl_category')->result_array();
        }else{
            $user =$this->db->get_where('tbl_category',['id_category' => $id])->result_array();
            return $user;
        }
    }

}