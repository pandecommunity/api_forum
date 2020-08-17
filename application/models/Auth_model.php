<?php

class Auth_model extends CI_Model{

    public function checkemail($email)
    {
        $this->db->get_where("tbl_user", ["email" => $email])->row();
        return $this->db->affected_rows();
    }
    
    public function checknohp($nohp)
    {
        $this->db->get_where("tbl_user", ["nohp" => $nohp])->row();
        return $this->db->affected_rows();
    }

    public function login($email)
    {
        $user = $this->db->get_where("tbl_user", ["email" => $email])->row();
        if ($user) {
            $user->password = "";
            return $user;
        } else {
            return 23;
        }
    }
    public function register($data){
        $this->db->insert('tbl_user',$data);
        return $this->db->affected_rows();
    }


    public function updateFCM($data,$email){
        $this->db->update('tbl_user',$data,['email' => $email]);
        return $this->db->affected_rows();
    }
}