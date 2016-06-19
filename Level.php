<?php
    class Level extends CI_Model{

        public function InsertLevel($data)
        {
            $this->db->insert('level' , $data);
        }

        public function getWhere($condition , $value)
        {
            $this->db->where($condition , $value);
            $query = $this->db->get('level');
            return $query->result();
        }
        
        public function getById($id)
        {
            $this->db->where('Id' , $id);
            $query = $this->db->get('level');
            return $query->result();
        }

        public function getLevel()
        {
            $query = $this->db->get('level');
            return $query->result();
        }

        public function DeleteLevel($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('level');
        }
        
        public function getLevelWhere($query)
        {
            $this->db->where($query);
            $query = $this->db->get('level');
            return $query->result();
        }
        
        public function getBySupervisor($id)
        {
            $this->db->where('Supervisor' , $id);
            $query = $this->db->get('level');
            return $query->result();
        }
        
        public function getByName($name)
        {
            $this->db->where('Name' , $name);
            $query = $this->db->get('level');
            return $query->result();
        }
        
        public function Update($id , $data)
        {
            $this->db->where('Id' , $id);
            $this->db->update('level',$data);
        }
    }
?>