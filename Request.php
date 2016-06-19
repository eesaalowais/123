<?php
    class Request extends CI_Model{

        public function InsertRequest($data)
        {
            $this->db->insert('request' , $data);
        }

        public function getWhere($query)
        {
            $this->db->where($query);
            $query = $this->db->get('request');
            return $query->result();
        }

        public function getById($id)
        {
            $this->db->where('Id' , $id);
            $query = $this->db->get('request');
            return $query->result();
        }
        
        public function getRequest()
        {
            $query = $this->db->get('request');
            return $query->result();
        }

        public function DeleteRequest($id)
        {
            $this->db->where('Id',$id);
            $this->db->delete('request');
        }
        public function Update($id , $data)
        {
            $this->db->where('Id',$id);
            $this->db->update('request',$data);
        }
    }
?>