<?php

    class Course extends CI_Model{

        public function InsertCourse($data)
        {
            $this->db->insert('course' , $data);
        }
        
        public function getWhere($query)
        {
            $this->db->where($query);
            $query = $this->db->get('course');
            return $query->result();
        }
        
        public function DeleteCourse($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('course');
        }
        
        public function getCourse()
        {
            $query = $this->db->get('course');
            return $query->result();
        } 
        
        public function LastCourse($id)
        {
            $this->db->order_by("Id", "desc");
            $this->db->limit(1);
            $this->db->where('Student' , $id);
            $query = $this->db->get('course');
            return $query->result();
        }
        
        public function getBySemester($id)
        {
            $this->db->where('Semester' , $id);
            $query = $this->db->get('course');
            return $query->result();
        }
        
        public function getByStudent($id)
        {
            $this->db->order_by("Id", "desc");
            $this->db->limit(1);
            $this->db->where('Student' , $id);
            $query = $this->db->get('course');
            return $query->result();
        }
    }
?>
