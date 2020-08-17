<?php

class Comentar_model extends CI_Model{

    public function insert($data){
        $this->db->insert('tbl_komentar',$data);
        return $this->db->affected_rows();
    }

    public function update($data,$id_coment){
        $this->db->update('tbl_komentar',$data,['id_komentar' => $id_coment]);
        return $this->db->affected_rows();
    }

    public function get($id_pertanyaan = null){
        if($id_pertanyaan != null){
            $query = "SELECT tbl_komentar.id_komentar,tbl_komentar.id_pertanyaan,tbl_user.id as id_user,tbl_user.name,tbl_user.image_profile,tbl_komentar.komentar,tbl_komentar.like,tbl_komentar.created_at,
	    	tbl_komentar.is_delete
            FROM tbl_pertanyaan,tbl_komentar,tbl_user
            WHERE
            tbl_komentar.id_user = tbl_user.id &&
            tbl_komentar.id_pertanyaan = tbl_pertanyaan.id_pertanyaan &&
            tbl_komentar.is_delete = 0 &&
            tbl_komentar.id_pertanyaan = $id_pertanyaan
            order by tbl_komentar.like DESC";
            return $this->db->query($query)->result_array();
        }

    }
}