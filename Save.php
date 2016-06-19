<?php

    class Save extends CI_Model{
        
        public function InsertSave($data)
        {
            $this->db->insert('save' , $data);
        }
        
        
        public function getWhere($query)
        {
            $this->db->order_by("Id", "DESC");
            $this->db->where($query);
            $query = $this->db->get('save');
            return $query->result();
        }

    }
?>