<?php

    class Nationality extends CI_Model{
        
        public function InsertNationality($data)
        {
            $this->db->insert('nationality' , $data);
        }
        
        public function getNationality()
        {
            $query = $this->db->get('nationality');
            return $query->result();
        }
        
        public function DeleteNationality($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('nationality');
        }
        
        public function getByStatus($status)
        {
            $this->db->where('Status' , $status);
            $query = $this->db->get('nationality');
            return $query->result();
        }
        
        public function getById($id)
        {
            $this->db->where('Id' , $id);
            $query = $this->db->get('nationality');
            return $query->result();
        }
        
        public function getWhere($query)
        {
            $this->db->where($query);
            $query = $this->db->get('nationality');
            return $query->result();
        }
        
        public function Update($id , $data)
        {
            $this->db->where('Id' , $id);
            $this->db->update('nationality',$data);
        }
    }
?>
