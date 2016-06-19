<?php
    class Login extends CI_Controller {
        
        public function index($error = NULL)
        {
            header("Location: Login/Access" , FALSE);
        }
        
        
        public function Access($error = NULL)
        {
            $data['error'] = $error;
            $this->load->view('access' , $data);
        }

        public function setAccess()
        {
            $data['National_id'] = $this->input->post('username');
            $data['Password'] = md5($this->input->post('password'));
            $this->load->model('User');
            $query = "National_Id = ".$data['National_id']." AND Password = '".$data['Password']."'";
            $data['User'] = $this->User->Login($query);
            if(@count($data['User']) > 0)
            {
                $this->session->set_userdata('Id' , $data['User'][0]->Id);
                $this->session->set_userdata('Full_Name' , $data['User'][0]->Full_Name);
                $this->session->set_userdata('Type' , $data['User'][0]->Type);
                $this->session->set_userdata('username' , $data['User'][0]->National_Id);
                if($this->session->userdata('Type') == 1)
                {
                    header("Location: ../Admin/Dashboard");
                }
                elseif($this->session->userdata('Type') == 2)
                {
                    header("Location: ../Supervisor/Dashboard");
                }
                elseif($this->session->userdata('Type') == 3)
                {
                    header("Location: ../Teacher/Dashboard");
                }
                else
                {
                    header("Location: ../Student/Dashboard");
                }
            }
            else
            {
                $error = "خطا في اسم المستخدم  او كلمة المرور";
                $this->Access($error);
            }
        }
    }
?>