<?php
    class Alert extends CI_Model{
        public function InsertAlert($data)
        {
            $this->db->insert('alert' , $data);
        }
        
        public function getWhere($query)
        {
            $this->db->where($query);
            $this->db->order_by("Id", "DESC");
            $query = $this->db->get('alert');
            return $query->result();
        }
        
        public function DeleteAlert($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('alert');
        }
        
        public function getAlert()
        {
            $this->db->order_by("Id", "DESC");
            $query = $this->db->get('alert');
            return $query->result();
        }
        
        public function getLastTeen()
        {
            $this->db->order_by("Id", "desc");
            $this->db->limit(10);
            $query = $this->db->get('alert');
            return $query->result();
        }
    }
?>