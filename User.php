<?php

    class User extends CI_Model{

        public function InsertUser($data)
        {
            $this->db->insert('user' , $data);
        }
        
        public function getWhere($condition , $value)
        {
            $this->db->where($condition , $value);
            $query = $this->db->get('user');
            return $query->result();
        }
        
        public function getById($id)
        {
            $this->db->where('Id' , $id);
            $query = $this->db->get('user');
            return $query->result();
        }
        
        public function getByEmail($email)
        {
            $this->db->where('Email' , $email);
            $query = $this->db->get('user');
            return $query->result();
        }
        
        public function getByNational_Id($id)
        {
            $this->db->where('National_Id' , $id);
            $query = $this->db->get('user');
            return $query->result();
        }
        
        
        public function DeleteUser($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('user');
        }
        
        public function Login($query)
        {
            //$where = 'National_Id = '.$username." AND Password = ".$password;
            $this->db->where($query);
            $query = $this->db->get('user');
            return $query->result();
        }
        
        public function getUserWhere($query)
        {
            $this->db->where($query);
            $query = $this->db->get('user');
            return $query->result();
        }
        
        public function getLastTeen()
        {
            $this->db->where('Type' , 4);
            $this->db->order_by("Id", "desc");
            $this->db->limit(10);
            $query = $this->db->get('user');
            return $query->result();
        }
        
        public function Update($id , $data)
        {
            $this->db->where('Id' , $id);
            $this->db->update('user',$data);
        }
    }
?>