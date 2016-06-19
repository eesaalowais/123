<?php
    class Student extends CI_Controller {
        
        public function index()
        {
            $this->Dashboard();
        }
        
        public function Dashboard()
        {
            $this->load->model('Course');
            $data['Course'] = $this->Course->LastCourse($this->session->userdata('Id'));
            if(@count($data['Course']) > 0)
            {
                $this->load->model('Semester');
                $data['Semester'] = $this->Semester->getById($data['Course'][0]->Semester);
                if(@count($data['Semester']) > 0)
                {
                    $this->load->model('Level');
                    $data['Level'] = $this->Level->getById($data['Course'][0]->Level);
                    $this->load->model('Halqa');
                    $data['Halqa'] = $this->Halqa->getById($data['Course'][0]->Halqa);
                    $End =  $data['Semester'][0]->End;
                    $Now = date_create()->format('Y-m-d');
                    $datetime1 = new DateTime($End);
                    $datetime2 = new DateTime($Now);
                    if($datetime1 >= $datetime2)
                    {
                        $Start = $data['Semester'][0]->Start;
                        $End = $data['Semester'][0]->End;
                        $NumberDay = 0;
                        $this->load->model('Attendance');
                        $this->load->model('Save');
                        $data['Present'] = 0;
                        $data['Absent'] = 0;
                        $data['Late'] = 0;
                        $date['Save'] = 0;
                        $date['Row'] = 0;
                        while(strtotime($Now) >= strtotime($Start))
                        {
                            $day = date('l', strtotime($Start));
                            if($data['Level'][0]->$day == 1)
                            {
                                $query = "User_Id = ".$this->session->userdata('Id')." AND Date = "."'".$Start."'";
                                $check_att = $this->Attendance->getWhere($query);
                                if(@count($check_att) > 0)
                                {
                                    if($check_att[0]->Status == 1)
                                    {
                                        $data['Present'] = $data['Present']+1;
                                    }
                                    elseif($check_att[0]->Status == 2)
                                    {
                                        $data['Late'] = $data['Late']+1;
                                    }
                                    else
                                    {
                                        $data['Absent'] = $data['Absent']+1;
                                    }
                                }
                                else
                                {
                                    $data['Present'] = $data['Present']+1;
                                }
                                
                                $save = $this->Save->getWhere($query);
                                if(@count($save) >0)
                                {
                                    $date['Save'] = $save[0]->Total + $date['Save'];
                                    $date['Row'] = $save[0]->Row + $date['Row'];
                                }
                                $NumberDay = $NumberDay+1;
                            }
                            $Start = date("Y-m-d", strtotime("+1 day", strtotime($Start)));
                        }
                        
                        //اخر 10 تنبيهات
                        $this->load->model('Alert');
                        $this->load->model('User');
                        $query = "Type = 1";
                        $data['Admin'] = $this->User->getUserWhere($query);
                        $query = "";
                        for($i = 0 ; $i < @count($data['Admin']) ; $i++)
                        {
                            if($i != @count($data['Admin'])-1)
                                $query = $query." From = ".$data['Admin'][$i]->Id." OR ";
                            else
                                $query = $query." From = ".$data['Admin'][$i]->Id;
                        }
                        $query = $query." OR From = ".$data['Level'][0]->Supervisor." OR From = ".$data['Halqa'][0]->Teacher;
                        $data['Alert'] = $this->Alert->getWhere($query);
                        $dashboard['Alert'] = NULL;
                        $dashboard['error3'] = NULL;
                        if(@count($data['Alert']) > 0)
                        {
                            foreach ($data['Alert'] as $value)
                            {
                                if(strtotime($value->Date) >= strtotime($data['Semester'][0]->Start) AND strtotime($value->Date) <= strtotime($data['Semester'][0]->End))
                                {
                                    if($value->To == 4 || $value->To == 1)
                                    {
                                        $user = $this->User->getById($value->From);
                                        $value->From = $user[0]->Full_Name; 
                                        $dashboard['Alert'][@count($dashboard['Alert'])] = $value;
                                    }
                                }
                            }
                            if(@count($Information['Alert']) == 0)
                                $dashboard['error3'] = "لاتوجد تنبيهات";
                        }
                        else
                        {
                            $Information['error'] = "لاتوجد تنبيهات";
                        }
                    }
                    else
                    {
                        $dashboard['error'] = "لايوجد فصل دراسي حالي";
                    }
                }
                else
                {
                    $dashboard['error'] = "خطا في الوصول";
                }
                
                // آخر 10 رسائل بالصندوق الوارد
                $this->load->model('Email');
                $dashboard['Email'] = $this->Email->getLastTeen($this->session->userdata('Id'));
                if(@count($dashboard['Email']) == 0)
                    $dashboard['error2'] = "الصندوق الوارد فارغ";
                else
                {
                    foreach ($dashboard['Email'] as $value)
                    {
                        $user = $this->User->getById($value->From);
                        $value->From = $user[0]->Full_Name;
                    }
                }
                
                $dashboard['Present']   = $data['Present'] / $NumberDay * 100;
                $dashboard['Late']      = $data['Late'] / $NumberDay * 100;
                $dashboard['Absent']    = $data['Absent'] / $NumberDay * 100;
                $dashboard['Save'] = $date['Save'] / $NumberDay *10;
                $dashboard['TotalRow'] = $NumberDay * $data['Halqa'][0]->Save;
                $dashboard['Row'] = $date['Row'];
            }
            else
            {
                $dashboard['error'] = "لاتوجد فصول دراسية مسجل بها";
            }
            $this->load->view('Student/Dashboard' , $dashboard);
        }
        
        public function Logout()
        {
            $this->session->set_userdata('Id');
            $this->session->set_userdata('Full_Name');
            $this->session->set_userdata('Type');
            $this->session->set_userdata('username');
            header("Location: ../");
        }
        
        public function Attendance()
        {
            $data['error'] = NULL;
            $this->load->model('Course');
            $data['Course'] = $this->Course->LastCourse($this->session->userdata('Id'));
            if(@count($data['Course']) > 0)
            {
                $this->load->model('Semester');
                $data['Semester'] = $this->Semester->getById($data['Course'][0]->Semester);
                $End =  $data['Semester'][0]->End;
                $Now = date_create()->format('Y-m-d');
                $datetime1 = new DateTime($End);
                $datetime2 = new DateTime($Now);
                if($datetime1 >= $datetime2)
                {
                    $this->load->model('Attendance');
                    $this->load->model('Level');
                    $data['Level'] = $this->Level->getById($data['Course'][0]->Level);
                    $Start = $data['Semester'][0]->Start;
                    $End = $data['Semester'][0]->End;
                    $Info = array();
                    while(strtotime($Now) >= strtotime($Start))
                    {
                        $day = date('l', strtotime($Start));
                        if($data['Level'][0]->$day == 1)
                        {
                            $d = "'".$Start."'";
                            $query = "User_Id = ".$this->session->userdata('Id')." AND Date = ".$d;
                            $att = $this->Attendance->getWhere($query);
                            if(@count($att) > 0)
                            {
                                if($att[0]->Status == 1)
                                {
                                    $Info[@count($Info)] = array(
                                        "Name" => $day,
                                        "Date" => $Start,
                                        "Status" => 1,
                                    );
                                }
                                elseif($att[0]->Status == 2)
                                {
                                    $Info[@count($Info)] = array(
                                        "Name" => $day,
                                        "Date" => $Start,
                                        "Status" => 2,
                                    );
                                }
                                else
                                {
                                    $Info[@count($Info)] = array(
                                        "Name" => $day,
                                        "Date" => $Start,
                                        "Status" => 3,
                                    );
                                }
                            }
                            else
                            {
                                $Info[@count($Info)] = array(
                                    "Name" => $day,
                                    "Date" => $Start,
                                    "Status" => 4,
                                );
                            }
                            $Attendance['Info'] = $Info;
                        }
                        $Start = date("Y-m-d", strtotime("+1 day", strtotime($Start)));
                    }
                }
                else
                {
                    $data['error'] = "لايوجد فصل دراسي حالي";
                }
            }
            else
            {
                $Attendance['error'] = "لايوجد فصول مسجل فيها";
            }
            
            
            $this->load->view('Student/Attendance' , $Attendance);
        }
        
        public function Grade()
        {
            $data['error'] = NULL;
            $this->load->model('Course');
            $data['Course'] = $this->Course->LastCourse($this->session->userdata('Id'));
            
            if(@count($data['Course']) > 0)
            {
                $this->load->model('Semester');
                $data['Semester'] = $this->Semester->getById($data['Course'][0]->Semester);
                $End =  $data['Semester'][0]->End;
                $Now = date_create()->format('Y-m-d');
                $datetime1 = new DateTime($End);
                $datetime2 = new DateTime($Now);
                if($datetime1 >= $datetime2)
                {
                    $this->load->model('Save');
                    $this->load->model('Review');
                    $this->load->model('Level');
                    $data['Level'] = $this->Level->getById($data['Course'][0]->Level);
                    $Start = $data['Semester'][0]->Start;
                    $End = $data['Semester'][0]->End;
                    $Info1 = array();
                    $Info2 = array();
                    while(strtotime($Now) >= strtotime($Start))
                    {
                        $day = date('l', strtotime($Start));
                        if($data['Level'][0]->$day == 1)
                        {
                            $d = "'".$Start."'";
                            $query = "User_Id = ".$this->session->userdata('Id')." AND Date = ".$d;
                            $Save = $this->Save->getWhere($query);
                            if(@count($Save) > 0)
                            {
                                $Info1[@count($Info1)] = array(
                                    "Day" => $day,
                                    "Date" => $Start,
                                    "Name" => $Save[0]->Name,
                                    "From" => $Save[0]->From,
                                    "To" => $Save[0]->To,
                                    "Grade" => $Save[0]->Total,
                                );
                            }
                            else
                            {
                                $Info1[@count($Info1)] = array(
                                    "Day" => $day,
                                    "Date" => $Start,
                                    "Name" => "لم يتعين",
                                    "From" => "لم يتعين",
                                    "To" => "لم يتعين",
                                    "Grade" => "لم يتعين",
                                );
                            }
                            
                            $Review = $this->Review->getWhere($query);
                            if(@count($Review) > 0)
                            {
                                $Info2[@count($Info2)] = array(
                                    "Day" => $day,
                                    "Date" => $Start,
                                    "Name" => $Review[0]->Name,
                                    "From" => $Review[0]->From,
                                    "To" => $Review[0]->To,
                                    "Grade" => $Review[0]->Grade,
                                );
                            }
                            else
                            {
                                $Info2[@count($Info2)] = array(
                                    "Day" => $day,
                                    "Date" => $Start,
                                    "Name" => "لم يتعين",
                                    "From" => "لم يتعين",
                                    "To" => "لم يتعين",
                                    "Grade" => "لم يتعين",
                                );
                            }
                            
                             
                        }
                        $Start = date("Y-m-d", strtotime("+1 day", strtotime($Start)));
                    }
                    $Information['Save'] = $Info1;
                    $Information['Review'] = $Info2;
                }
                else
                {
                    $data['error'] = "لايوجد فصل دراسي حالي";
                }
            }
            else
            {
                $Information['error'] = "لايوجد فصول مسجل فيها";
            }
            $this->load->view('Student/Grade' , $Information);
        }
        
        public function Alert()
        {
            $data['error'] = NULL;
            $this->load->model('Course');
            $data['Course'] = $this->Course->LastCourse($this->session->userdata('Id'));
            if(@count($data['Course']) > 0)
            {
                $this->load->model('Semester');
                $data['Semester'] = $this->Semester->getById($data['Course'][0]->Semester);
                $End =  $data['Semester'][0]->End;
                $Now = date_create()->format('Y-m-d');
                $datetime1 = new DateTime($End);
                $datetime2 = new DateTime($Now);
                if($datetime1 >= $datetime2)
                {
                    $this->load->model('Level');
                    $this->load->model('Halqa');
                    $this->load->model('Alert');
                    $this->load->model('User');
                    $data['Level'] = $this->Level->getById($data['Course'][0]->Level);
                    $data['Halqa'] = $this->Halqa->getById($data['Course'][0]->Halqa);
                    $query = "Type = 1";
                    $data['Admin'] = $this->User->getUserWhere($query);
                    $query = "";
                    for($i = 0 ; $i < @count($data['Admin']) ; $i++)
                    {
                        if($i != @count($data['Admin'])-1)
                            $query = $query." From = ".$data['Admin'][$i]->Id." OR ";
                        else
                            $query = $query." From = ".$data['Admin'][$i]->Id;
                    }
                    $query = $query." OR From = ".$data['Level'][0]->Supervisor." OR From = ".$data['Halqa'][0]->Teacher;
                    $data['Alert'] = $this->Alert->getWhere($query);
                    $Information['Alert'] = NULL;
                    $Information['error'] = NULL;
                    if(@count($data['Alert']) > 0)
                    {
                        foreach ($data['Alert'] as $value)
                        {
                            if(strtotime($value->Date) >= strtotime($data['Semester'][0]->Start) AND strtotime($value->Date) <= strtotime($data['Semester'][0]->End))
                            {
                                if($value->To == 4 || $value->To == 1)
                                {
                                    $user = $this->User->getById($value->From);
                                    $value->From = $user[0]->Full_Name; 
                                    $Information['Alert'][@count($Information['Alert'])] = $value;
                                }
                            }
                        }
                        if(@count($Information['Alert']) == 0)
                            $Information['error'] = "لاتوجد تنبيهات";
                    }
                    else
                    {
                        $Information['error'] = "لاتوجد تنبيهات";
                    }
                    
                    
                }
                else
                {
                    $data['error'] = "لايوجد فصل دراسي حالي";
                }
            }
            else
            {
                $Information['error'] = "لايوجد فصول مسجل فيها";
            }
            $this->load->View('Student/Alert' , $Information);
        }
        
        public function ViewAlert()
        {
            $id = $this->input->get_post('id');
            $query = "Id = ".$id;
            $this->load->model('Alert');
            $data['Alert'] = $this->Alert->getWhere($query);
            $this->load->model('User');
            $query = "Id = ".$data['Alert'][0]->From;
            $user = $this->User->getUserWhere($query);
            $data['Alert'][0]->From = $user[0]->Full_Name;
            $this->load->view('Student/ViewAlert' , $data);
        }
        
        public function ViewMail()
        {
            $data['error'] = NULL;
            $id = $this->input->post_get('id');
            $this->load->model('Email');
            
            //to get how many mesaage not read;
            $query = "To = ".$this->session->userdata('Id')." AND Read = 0";
            $data['Count'] = $this->Email->getWhere($query);
            $data['Count'] = @count($data['Count']);
            
            //to send email to view
            $query = "Id = ".$id." AND To = ".$this->session->userdata('Id');
            $data['Mail'] = $this->Email->getWhere($query);
            if(@count($data['Mail']) > 0)
            {
                $this->load->model('User');
                $query = "Id = ".$data['Mail'][0]->From;
                $From = $this->User->getUserWhere($query);
                $data['Mail'][0]->From = $From[0]->Full_Name;
                $query = "Id = ".$data['Mail'][0]->To;
                $To = $this->User->getUserWhere($query);
                $data['Mail'][0]->To = $To[0]->Full_Name;
                $info['Read'] = 1;
                $this->Email->Update($id , $info);
                
                if($data['Mail'][0]->Email != 0)
                {
                    $data['Reply'] = $this->Email->getById($data['Mail'][0]->Email);  
                    if(@count($data['Reply']) > 0)
                    {
                        foreach ($data['Reply'] as $value)
                        {
                            $query = "Id = ".$value->From;
                            $From = $this->User->getUserWhere($query);
                            $value->From = $From[0]->Full_Name;
                            $query = "Id = ".$value->To;
                            $To = $this->User->getUserWhere($query);
                            $value->To = $To[0]->Full_Name;
                        }
                    }
                }
            }
            else
            {
                $data['error'] = "خطا في الدخول";
            }
            
            $this->load->view('Student/ViewMail' , $data);
        }
        
        public function Compose()
        {
            $this->load->model('User');
            $query = "Type = 1";
            $data['Users'] = $this->User->getUserWhere($query);
            $this->load->model('Semester');
            $data['Semester'] = $this->Semester->LastSemester();
            if(@count($data['Semester']) > 0)
            {
                $End =  $data['Semester'][0]->End;
                $Now = date_create()->format('Y-m-d');
                $datetime1 = new DateTime($End);
                $datetime2 = new DateTime($Now);
                if($datetime1 >= $datetime2)
                {
                    $this->load->model('Course');
                    $query = "Semester = ".$data['Semester'][0]->Id." AND Student = ".$this->session->userdata('Id');
                    $info = $this->Course->getWhere($query);
                    $this->load->model('Level');
                    $this->load->model('Halqa');
                    $level = $this->Level->getById($info[0]->Level);
                    $halqa = $this->Halqa->getById($info[0]->Halqa);
                    $supervisor = $this->User->getById($level[0]->Supervisor);
                    $teacher = $this->User->getById($halqa[0]->Teacher);
                    $data['Users'][@count($data['Users'])] = $supervisor[0];
                    $data['Users'][@count($data['Users'])] = $teacher[0];
                }
            }
            else
            {
                
            }
            //to get how many mesaage not read;
            $this->load->model('Email');
            $query = "To = ".$this->session->userdata('Id')." AND Read = 0";
            $data['Count'] = $this->Email->getWhere($query);
            $data['Count'] = @count($data['Count']);
            $this->load->view('Student/Compose', $data);
        }
        
        public function SendMail()
        {
            $data['Email'] = 0;
            $data['From'] = $this->session->userdata('Id');
            $data['To'] = $this->input->post('To');
            $data['Title'] = $this->input->post('Title');
            $data['Content'] = $this->input->post('Content');
            $data['File_Path'] = "None";
            $data['Date'] = date_create()->format('Y-m-d');
            date_default_timezone_set('Asia/Riyadh');
            $data['Time'] = date_create()->format('H:i:s');
            
            $config['upload_path'] = './upload/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size']	= '1000000';
            $config['max_width']  = '1024';
            $config['max_height']  = '768';

            $this->load->library('upload', $config);
            if($this->upload->do_upload())
            {
                $data['File_Path'] = $this->upload->data();
                $data['File_Path'] = "./upload/".$data['File_Path']['file_name'];
            }
            $this->load->model('Email');
            $this->Email->InsertEmail($data);
            header("Location: Inbox");
        }
        
        public function Inbox()
        {
            $data['error'] = NULL;
            $this->load->model('Email');
            //to get how many mesaage not read;
            $query = "To = ".$this->session->userdata('Id')." AND Read = 0";
            $data['Count'] = $this->Email->getWhere($query);
            $data['Count'] = @count($data['Count']);
            
            $query = "To = ".$this->session->userdata('Id');
            $data['Inbox'] = $this->Email->getWhere($query);
            if(@count($data['Inbox']) > 0)
            {
                $this->load->model('User');
                foreach ($data['Inbox'] as $value)
                {
                    $query = "Id = ".$value->From;
                    $From = $this->User->getUserWhere($query);
                    $value->From = $From[0]->Full_Name;
                    $query = "Id = ".$value->To;
                    $To = $this->User->getUserWhere($query);
                    $value->To = $From[0]->Full_Name;
                }
            }
            else
            {
                $data['error'] = "البريد الوارد فارغ";
            }
            $this->load->view('Student/Inbox' , $data);
        }
        
        public function Send()
        {
            $data['error'] = NULL;
            $this->load->model('Email');
            //to get how many mesaage not read;
            $query = "To = ".$this->session->userdata('Id')." AND Read = 0";
            $data['Count'] = $this->Email->getWhere($query);
            $data['Count'] = @count($data['Count']);
            
            $data['Send'] = $this->Email->getBySend($this->session->userdata('Id'));
            if(@count($data['Send']) > 0)
            {
                $this->load->model('User');
                foreach ($data['Send'] as $value)
                {
                    $query = "Id = ".$value->From;
                    $From = $this->User->getUserWhere($query);
                    $value->From = $From[0]->Full_Name;
                    $query = "Id = ".$value->To;
                    $To = $this->User->getUserWhere($query);
                    $value->To = $To[0]->Full_Name;
                }
            }
            else
            {
                $data['error'] = "لاتوجد رسائل مرسلة";
            }
            $this->load->view('Student/Send' , $data);
        }
        
        public function MailSend()
        {
            $data['error'] = NULL;
            $id = $this->input->post_get('id');
            $this->load->model('Email');
            //to get how many mesaage not read;
            $query = "To = ".$this->session->userdata('Id')." AND Read = 0";
            $data['Count'] = $this->Email->getWhere($query);
            $data['Count'] = @count($data['Count']);
            
            
            $query = "Id = ".$id." AND From = ".$this->session->userdata('Id');
            $data['Send'] = $this->Email->getWhere($query);
            if(@count($data['Send']) > 0)
            {
                $this->load->model('User');
                $query = "Id = ".$data['Send'][0]->From;
                $From = $this->User->getUserWhere($query);
                $data['Send'][0]->From = $From[0]->Full_Name;
                $query = "Id = ".$data['Send'][0]->To;
                $To = $this->User->getUserWhere($query);
                $data['Send'][0]->To = $From[0]->Full_Name;
            }
            else
            {
                $data['error'] = "خطا في الدخول";
            }
            $this->load->view('Student/MailSend' , $data);
        }
        
    }
?>