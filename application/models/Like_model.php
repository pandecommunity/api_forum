<?php

class Like_model extends CI_Model{

    public function insert($data){
        $this->db->insert('tbl_like',$data);
        return $this->db->affected_rows();
    }

    public function insertlikecomment($data){
        $this->db->insert('tbl_like_komentar',$data);
        return $this->db->affected_rows();
    }

    public function checkLike($id_pertanyaan,$id_user){
        $user =$this->db->get_where('tbl_like',[
            'id_pertanyaan' => $id_pertanyaan,
            'id_user'=>$id_user])
            ->row();

        return $user;
    }

    public function checkLikecomment($id_komentar,$id_user){
        $user =$this->db->get_where('tbl_like_komentar',[
            'id_komentar' => $id_komentar,
            'id_user'=>$id_user])
            ->row();

        return $user;
    }

    public function delete($id_pertanyaan,$id_user){
        $this->db->delete('tbl_like',[
            'id_pertanyaan' => $id_pertanyaan,
            'id_user'=>$id_user]);
        return $this->db->affected_rows();
    }

    public function deleteLikeComment($id_komentar,$id_user){
        $this->db->delete('tbl_like_komentar',[
            'id_komentar' => $id_komentar,
            'id_user'=>$id_user]);
        return $this->db->affected_rows();
    }
}