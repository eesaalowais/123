<?php
    class Attendance extends CI_Model{
        public function InsertAttendance($data)
        {
            $this->db->insert('attendance' , $data);
        }
        
        public function getWhere($query)
        {
            
            $this->db->where($query);
            $query = $this->db->get('attendance');
            return $query->result();
        }
        
        public function DeleteAttendance($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('attendance');
        }
        
        public function getAttendance()
        {
            $query = $this->db->get('attendance');
            return $query->result();
        } 
        
        public function getByUser($id)
        {
            $this->db->where('User_Id' , $id);
            $query = $this->db->get('attendance');
            return $query->result();
        }
    }
?>
