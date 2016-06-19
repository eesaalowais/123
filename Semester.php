<?php
    class Semester extends CI_Model{

        public function InsertSemester($data)
        {
            $this->db->insert('semester' , $data);
        }
        
        public function getWhere($condition , $value)
        {
            $this->db->where($condition , $value);
            $query = $this->db->get('semester');
            return $query->result();
        }
        
        public function getSemesterWhere($query)
        {
            $this->db->where($query);
            $query = $this->db->get('semester');
            return $query->result();
        }
        
        public function DeleteSemester($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('semester');
        }
        
        public function getSemester()
        {
            $query = $this->db->get('semester');
            return $query->result();
        } 
        
        public function LastSemester()
        {
            $this->db->order_by("Id", "desc");
            $this->db->limit(1);
            $query = $this->db->get('semester');
            return $query->result();
        }
        
        public function getById($id)
        {
            $this->db->where('Id' , $id);
            $query = $this->db->get('semester');
            return $query->result();
        }
        
        public function getByName($name)
        {
            $this->db->where('Name' , $name);
            $query = $this->db->get('semester');
            return $query->result();
        }
        
        public function Update($id , $data)
        {
            $this->db->where('Id' , $id);
            $this->db->update('semester',$data);
        }
    }
?>