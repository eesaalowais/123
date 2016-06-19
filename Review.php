<?php
    class Review extends CI_Model{

        public function InsertReview($data)
        {
            $this->db->insert('review' , $data);
        }


        public function getWhere($query)
        {
            $this->db->order_by("Id", "desc");
            $this->db->where($query);
            $query = $this->db->get('review');
            return $query->result();
        }
    }
?>