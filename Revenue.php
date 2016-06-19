<?php
    class Revenue extends CI_Model{

        public function InsertRevenue($data)
        {
            $this->db->insert('revenues' , $data);
        }

        public function getWhere($query)
        {
            $this->db->where($query);
            $query = $this->db->get('revenues');
            return $query->result();
        }

        public function getById($id)
        {
            $this->db->where('Id' , $id);
            $query = $this->db->get('revenues');
            return $query->result();
        }
        
        public function getBySemester($id)
        {
            $this->db->where('Semester' , $id);
            $query = $this->db->get('revenues');
            return $query->result();
        }
        
        public function getRevenue()
        {
            $query = $this->db->get('revenues');
            return $query->result();
        }

        public function DeleteRevenue($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('revenues');
        }
        public function Update($id , $data)
        {
            $this->db->where('Id',$id);
            $this->db->update('revenues',$data);
        }

    }
?>