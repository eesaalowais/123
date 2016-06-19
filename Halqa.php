<?php
    class Halqa extends CI_Model{

        public function InsertHalqa($data)
        {
            $this->db->insert('halqa' , $data);
        }
        
        public function getWhere($codition , $value)
        {
            $this->db->where($codition , $value);
            $query = $this->db->get('halqa');
            return $query->result();
        }
        
        public function DeleteHalqa($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('halqa');
        }
        
        public function getHalqaWhere($query)
        {
            $this->db->where($query);
            $query = $this->db->get('halqa');
            return $query->result();
        }
        
        public function getHalqa()
        {
            $query = $this->db->get('halqa');
            return $query->result();
        }
        
        public function getById($id)
        {
            $this->db->where('Id' , $id);
            $query = $this->db->get('halqa');
            return $query->result();
        }
        
        public function getByLevel($level)
        {
            $this->db->where('Level' , $level);
            $query = $this->db->get('halqa');
            return $query->result();
        }
        
        public function getByTeacher($id)
        {
            $this->db->where('Teacher' , $id);
            $query = $this->db->get('halqa');
            return $query->result();
        }
        
        public function getByName($name)
        {
            $this->db->where('Name' , $name);
            $query = $this->db->get('halqa');
            return $query->result();
        }
        
        public function Update($id , $data)
        {
            $this->db->where('Id' , $id);
            $this->db->update('halqa',$data);
        }
    }
?>