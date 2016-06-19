<?php

    class Email extends CI_Model{
        public function InsertEmail($data)
        {
            $this->db->insert('email' , $data);
        }
        
        public function getWhere($query)
        {
            $this->db->order_by("Id", "DESC");
            $this->db->where($query);
            $query = $this->db->get('email');
            return $query->result();
        }
        
        public function DeleteEmail($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('email');
        }
        
        public function getEmail()
        {
            $query = $this->db->get('mail');
            return $query->result();
        }
        
        
        public function getBySend($id)
        {
            $this->db->where('From' ,  $id );
            $this->db->order_by("Id", "DESC");
            $query = $this->db->get('email');
            return $query->result();
        }
        
        public function Update($id , $data)
        {
            $this->db->where('Id',$id);
            $this->db->update('email',$data);
        }
        
        public function getById($id)
        {
            $this->db->where('Id' ,  $id );
            $query = $this->db->get('email');
            return $query->result();
        }
        
        public function getLastTeen($id)
        {
            $this->db->where('To' , $id);
            $this->db->order_by("Id", "desc");
            $this->db->limit(10);
            $query = $this->db->get('email');
            return $query->result();
        }
    }
?>