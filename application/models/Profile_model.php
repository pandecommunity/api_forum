<?php

class Profile_model extends CI_Model{
    public function get($id = null){
        if($id === null){
            return $this->db->get('tbl_user')->row();
        }else{
            $user =$this->db->get_where('tbl_user',['id' => $id])->row();
            $user->password = "";
            return $user;
        }
    }

    public function update($data,$id_user){
        $this->db->update('tbl_user',$data,['id' => $id_user]);
        return $this->db->affected_rows();
    }

    public function check($id_user,$password)
    {
        $user = $this->db->get_where("tbl_user", ["id" => $id_user])->row();;
        if ($user) {
            if (password_verify($password, $user->password)) {
                return 1;
            } else {
                return 20;
            }
        } else {
            return 23;
        }
    }
}