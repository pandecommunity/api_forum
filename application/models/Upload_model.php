<?php

class Upload_model extends CI_Model{
    public function insert($data = array()){
        return $this->db->insert_batch('tbl_image',$data);
    }

}