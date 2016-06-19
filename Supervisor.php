<?php
    class Supervisor extends CI_Controller {
        public function index()
	{
            header("Location: Supervisor/Dashboard");
	}

        public function Dashboard()
        {
            $this->load->model('Course');
            $this->load->model('Level');
            $this->load->model('Semester');
            $this->load->model('Attendance');
            $data['Semester'] = $this->Semester->LastSemester();
            if(@count($data['Semester']) > 0)
            {
                $End =  $data['Semester'][0]->End;
                $Now = date_create()->format('Y-m-d');
                $datetime1 = new DateTime($End);
                $datetime2 = new DateTime($Now);
                if($datetime1 >= $datetime2)
                {
                    $data['Level'] = $this->Level->getBySupervisor($this->session->userdata('Id'));
                    if(@count($data['Level']) > 0)
                    {
                        $query = "Semester = ".$data['Semester'][0]->Id." AND Level = ".$data['Level'][0]->Id;
                        $data['Course'] = $this->Course->getWhere($query);
                        if(@count($data['Course']) > 0)
                        {
                            $dashboard['TotalStudent'] = @count($data['Course']);
                        }
                        else
                        {
                            $dashboard['error'] = "لايوجد طلاب مسجلين لديك في هذا الفصل";
                        }
                        
                        $this->load->model('Halqa');
                        $data['Halqa'] = $this->Halqa->getByLevel($data['Level'][0]->Id);
                        $data['AllHalqat'] = NULL;
                        if(@count($data['Halqa']) > 0)
                        {
                            foreach ($data['Halqa'] as $value)
                            {
                                $query = "Semester = ".$data['Semester'][0]->Id." AND Halqa = ".$value->Id;
                                $halqa_Course = $this->Course->getWhere($query);
                                if(@count($halqa_Course) > 0)
                                {
                                    $data['AllHalqat'][@count($data['AllHalqat'])] = $value;
                                }
                            }
                            $dashboard['TotalTeacher'] = @count($data['AllHalqat']);
                            
                            $dashboard['Attendance'] = array();
                            foreach ($data['AllHalqat'] as $value)
                            {
                                
                                $present = 0;
                                $absent = 0;
                                $late = 0;
                                $halqa_name = $this->Halqa->getById($value->Id);
                                $day = date('l', strtotime($Now));
                                if($data['Level'][0]->$day == 1)
                                {
                                    $query = "Semester = ".$data['Semester'][0]->Id." AND Halqa = ".$value->Id;
                                    $data['Student_Halqa'] = $this->Course->getWhere($query);
                                    
                                    foreach ($data['Student_Halqa'] as $info)
                                    {
                                        $query = "User_Id = ".$info->Id." AND Date = "."'".$Now."'";
                                        $status = $this->Attendance->getWhere($query);
                                        if(@count($status) > 1)
                                        {
                                            if($status[0]->Status == 1)
                                            {
                                                $present = $present+1;
                                            }
                                            elseif($status[0]->Status == 2)
                                            {
                                                $late = $late+1;
                                            }
                                            else
                                            {
                                                $absent = $absent+1;
                                            }
                                        }
                                        else
                                        {
                                            $present = $present+1;
                                        }
                                    }
                                    $present = $present / @count($data['Student_Halqa']) * 100;
                                    $late = $late / @count($data['Student_Halqa']) * 100;
                                    $absent = $absent / @count($data['Student_Halqa']) * 100;
                                    
                                    $dashboard['Attendance'][@count($dashboard['Attendance'])] = array(
                                        "Name" => $value->Name,
                                        "Present" => $present,
                                        "Absent" => $absent,
                                        "Late" => $late,
                                        "ErrorA" => 0,
                                    );
                                }
                                else
                                {
                                    $dashboard['Attendance'][@count($dashboard['Attendance'])] = array(
                                        "Name" => $value->Name,
                                        "Present" => 0,
                                        "Absent" => 0,
                                        "Late" => 0,
                                        "ErrorA" => 1,
                                    );
                                }
                            }

                        }
                        else
                        {
                            $dashboard['ErrorHalqa'] = "لاتوجد حلقات في مرحلتك";
                        }
                    }
                    else
                    {
                        $dashboard['error'] = "لاتوجد حلقات مشرفا عليها";
                    }
                }
                else
                {
                    $dashboard['error'] = "لايوجد فصل دراسي حاليا";
                }
            }
            else
            {
                $dashboard['error'] = "لايوجد فصول دراسيه مضافة";
            }

            $this->load->view('Supervisor/Dashboard' ,$dashboard);
        }
        
        public function StudentM()
        {
            $information['error'] = NULL;
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
                    $this->load->model('Level');
                    $Level = $this->Level->getWhere('Supervisor' , $this->session->userdata('Id'));
                    if(@count($Level) > 0)
                    {
                        $this->load->model('Course');
                        $content = "";
                        for($i = 0 ; $i < @count($Level) ; $i++)
                        {
                            if($i != @count($Level)-1)
                                $content = $content." Level = ".$Level[$i]->Id." OR ";
                            else
                                $content = $content." Level = ".$Level[$i]->Id;
                        }
                        $where = "Semester = ".$data['Semester'][0]->Id." AND ".$content;
                        $info = $this->Course->getWhere($where);
                        if(@count($info) > 0)
                        {
                            $query = "";
                            for($i = 0 ; $i < @count($info); $i++)
                            {
                                if($i != @count($info)-1)
                                    $query = $query." Id = ".$info[$i]->Student." OR ";
                                else
                                    $query = $query." Id = ".$info[$i]->Student;
                            }
                            $this->load->model('User');
                            $information['Student'] = $this->User->getUserWhere($query);
                        }
                        else
                        {
                            
                            $information['error'] = 'لايوجد طلاب مسجلين في الفصل الدراسي الحالي';
                        }
                    }
                    else
                    {
                        $information['error'] = 'لايوجد مرحلة مشرفا عليها';
                    }
                    
                }
                else
                {
                    $information['error'] = 'لايوجد فصل دراسي حاليا';
                }
            }
            else
            {
                $information['error'] = 'لاتوجد فصول دراسي مضافة';
            }
            
            $this->load->view('Supervisor/StudentM' , $information);

        }
        
        public function TeacherM()
        {
            $information['error'] = NULL;
            $this->load->model('Level');
            $data['Level'] = $this->Level->getWhere('Supervisor' , $this->session->userdata('Id'));
            if(@count($data['Level']) > 0)
            {
                $query = "";
                for($i =  0 ; $i < @count($data['Level']) ; $i++)
                {
                    if($i != @count($data['Level'])-1)
                        $query = $query." Level = ".$data['Level'][$i]->Id." OR ";
                    else
                        $query = $query." Level = ".$data['Level'][$i]->Id;
                }
                $this->load->model('Halqa');
                $data['Halqa'] = $this->Halqa->getHalqaWhere($query);
                if(@count($data['Halqa']) > 0)
                {
                    $query = "";
                    for($i =  0 ; $i < @count($data['Halqa']) ; $i++)
                    {
                        if($i != @count($data['Halqa'])-1)
                        {
                            if (strpos($query, $data['Halqa'][$i]->Teacher) == TRUE) 
                                continue;
                            $query = $query." Id = ".$data['Halqa'][$i]->Teacher." OR ";
                        }
                        else
                        {
                            if (strpos($query, $data['Halqa'][$i]->Teacher) == TRUE) 
                                continue;
                            $query = $query." Id = ".$data['Halqa'][$i]->Teacher;
                        }   
                    }
                    
                    $query = explode(" " , $query);
                    if($query[@count($query)-2] == "OR")
                        $query[@count($query)-2] = "";
                    $query = implode(" ", $query);

                    $this->load->model('User');
                    $information['Teacher'] = $this->User->getUserWhere($query);
                }
                else
                {
                    $information['error'] = 'لاتوجد حلقات مضافة في مراحلك';
                }
            }
            else
            {
                $information['error'] = 'لايوجد معلمين مشرفا عليهم';
            }
            $this->load->view('Supervisor/TeacherM', $information);
        }
        
        public function newUser()
        {
            $this->load->model('Nationality');
            $data['Nationality'] = $this->Nationality->getNationality();
            $this->load->view('Supervisor/newUser' , $data);
        }
        
        public function UpdateUser()
        {
            $id = $this->input->post_get('id');
            $this->load->model('User');
            $data['User'] = $this->User->getById($id);
            $data['error'] = NULL;
            if(@count($data['User']) > 0)
            {
                $this->load->model('Nationality');
                $name = $this->Nationality->getById($data['User'][0]->Nationality);
                $data['User'][0]->Nationality = $name[0]->Name;
                $data['Nationality'] = $this->Nationality->getByStatus(1);
            }
            else
            {
                $data['error'] = "خطأ في الوصول";
            }
            $this->load->view('Supervisor/UpdateUser' , $data);
        }
        
        public function setUpdate()
        {
            $id = $this->input->post_get('UpdateUser');
            $this->load->model('User');
            $data['User'] = $this->User->getById($id);
            if(@count($data['User']) > 0)
            {
                $check = $this->input->post('Full_Name');
                if(!empty($check))
                    $info['Full_Name'] = $this->input->post('Full_Name');
                $check = $this->input->post('Email');
                if(!empty($check))
                    $info['Email'] = $this->input->post('Email');
                $check = $this->input->post('Mobile');
                if(!empty($check))
                    $info['Mobile'] = $this->input->post('Mobile');
                $check = $this->input->post('Age');
                if(!empty($check))
                    $info['Age'] = $this->input->post('Age');
                $check = $this->input->post('National_Id');
                if(!empty($check))
                    $info['National_Id'] = $this->input->post('National_Id');
                $check = $this->input->post('Nationality');
                if(!empty($check))
                    $info['Nationality'] = $this->input->post('Nationality');
                $check = $this->input->post('Approve');
                if(!empty($check))
                    $info['Approve'] = $this->input->post('Approve');
                $check = $this->input->post('Passowrd');
                if(!empty($check))
                    $info['Passowrd'] = md5($this->input->post('Passowrd'));
                
                $this->User->Update($id , $info);
                header("Location: Dashboard");
            }
            else
            {
                die("Error");
            }
        }
        
        //Insert New User To Database;
        public function addUser()
        {
            $data['Full_Name']      = $this->input->post('Fname');
            $data['Full_Name']      = $data['Full_Name']." ".$this->input->post('Mname');
            $data['Full_Name']      = $data['Full_Name']." ".$this->input->post('Gname');
            $data['Full_Name']      = $data['Full_Name']." ".$this->input->post('Lname');
            $data['Email']          = $this->input->post('Email');
            $data['Password']       = md5($this->input->post('Password'));
            $data['Mobile']         = $this->input->post('Mobile');
            $data['Age']            = $this->input->post('Age');
            $data['National_Id']    = $this->input->post('National_Id');
            $data['Nationality']    = $this->input->post('Nationality');
            $data['Register_Date']  = date_create()->format('Y-m-d H:i:s');
            $data['Last_Login']     = "";
            $data['Approve']        = $this->input->post('Approve');
            $data['Status']         = 2;
            $data['Type']           = $this->input->post('Type');

            $this->load->model('User');
            $this->User->InsertUser($data);
            if($data['Type'] == 3)
            {
                $this->TeacherM();
            }else
            {
                $this->StudentM();
            }
        }
        
        public function First($error = NULL)
        {
            $data['error'] = $error;
            $this->load->view('Supervisor/First' , $data);
        }
        
        public function Seconde()
        {
            $data['error'] = FALSE;
            $National_Id = $this->input->post('National_id');
            $this->load->model('User');
            $data['Student'] = $this->User->getWhere('National_Id' , $National_Id);
            if(@count($data['Student']) > 0)
            {
                if($data['Student'][0]->Type != 4)
                {
                    $data['error'] = 'هذة الهوية ليست صحيحة';
                }
            }
            else
            {
                $data['error'] = 'هذة الهوية ليست صحيحة';
            }
            if($data['error'] != TRUE)
            {
                $this->load->model('Semester');
                $data['Semester'] = $this->Semester->getWhere('Status' , 1);
                $this->load->view('Supervisor/Seconde' , $data);
            }
            else
            {
                $this->First($data['error']);
            }
        }
        
        public function Third($data = NULL)
        {  
            $data['Student'] = $this->input->post('step2');
            $data['Semester'] = $this->input->post('semester');
            $this->load->model('Course');
            $condition = 'Student = '.$data['Student'].' AND Semester = '.$data['Semester'];
            $result = $this->Course->getWhere($condition);
            if(@count($result) > 0)
            {
                $data['error'] = "هذا الطالب مسجل بالفعل";
                $this->First($data['error']);
            }
            else
            {
                $this->load->model('Level');
                $data['Level'] = $this->Level->getLevel();
                $this->load->view('Supervisor/Third' , $data);
            }
        }
        
        public function Furth($data = NULL)
        {
            $info  = $this->input->post('step3');
            $info = explode(',',$info);
            $data['Student'] = $info[0];
            $data['Semester'] = $info[1];
            $data['Level'] = $this->input->post('level');
            $this->load->model('Halqa');
            $data['Halqa'] = $this->Halqa->getWhere('Level' , $data['Level']);
            $this->load->view('Supervisor/Furth' , $data);
        }
        
        public function DoneRegistration()
        {
            $info = $this->input->post('step4');
            $info = explode(',' , $info);
            $data['Student'] = $info[0];
            $data['Semester'] = $info[1];
            $data['Level'] = $info[2];
            $data['Halqa'] = $this->input->post('halqa');
            $this->load->model('Course');
            $this->Course->InsertCourse($data);
            $this->load->model('User');
            $this->load->model('Semester');
            $this->load->model('Level');
            $this->load->model('Halqa');
            $information['Student'] = $this->User->getWhere('Id' ,$data['Student']);
            $information['Semester'] = $this->Semester->getWhere('Id' , $data['Semester']);
            $information['Level'] = $this->Level->getWhere('Id' ,$data['Level']);
            $information['Halqa'] = $this->Halqa->getWhere('Id' , $data['Halqa']);
            $this->load->view('Supervisor/DoneRegistration' , $information);
        }
        
        public function HalqaM()
        {
            $this->load->model('Level');
            $information['Level'] = $this->Level->getWhere('Supervisor' , $this->session->userdata('Id'));
            if(@count($information['Level']) > 0)
            {
                $query = "";
                for($i = 0 ; $i < @count($information['Level']) ; $i++)
                {
                    if($i != @count($information['Level'])-1)
                        $query = $query."Level = ".$information['Level'][$i]->Id." OR ";
                    else
                        $query = $query."Level = ".$information['Level'][$i]->Id;
                }
                
                $this->load->model('Halqa');
                $information['Halqa'] = $this->Halqa->getHalqaWhere($query);
                if(@count($information['Halqa']) > 0)
                {
                    $this->load->model('User');
                    $information['Teacher'] = $this->User->getUserWhere('Type = 3');
                }
                else
                {
                    $information['error'] = "لاتوجد حلقات مضافة في مراحلك";
                }
            }
            else
            {
                $information['error'] = "لاتوجد مراحل مشرفا عليها";
            }
            $this->load->view('Supervisor/HalqaM' , $information);
        }
        
        public function newHalqa()
        {
            $this->load->model('Level');
            $data['error'] = NULL;
            $data['Level'] = $this->Level->getBySupervisor($this->session->userdata('Id'));
            if(@count($data['Level']) > 0)
            {
                $this->load->model('User');
                $data['Teacher'] = NULL;
                $info = $this->User->getWhere('Type' , 3);
                $this->load->model('Halqa');
                foreach ($info as $value)
                {
                    $query = "Teacher = ".$value->Id;
                    $Teacher = $this->Halqa->getHalqaWhere($query);
                    if(@count($Teacher) == 0)
                        $data['Teacher'][@count ($data['Teacher'])] = $value;
                }
            }
            else
            {
                $data['error'] = "لاتوجد مرحلة مشرفا عليها";
            }
            
            $this->load->view('Supervisor/newHalqa' , $data);
        }
        
        public function addHalqa()
        {
            $data['Name'] = $this->input->post('Name');
            $data['Level'] = $this->input->post('InserHalqa');
            $data['Save'] = $this->input->post('Save');
            $data['Teacher'] = $this->input->post('Teacher');
            $this->load->model('Halqa');
            $this->Halqa->InsertHalqa($data);
            header("Location: HalqaM" , FALSE);
        }
        
        public function UpdateHalqa()
        {
            $id = $this->input->post('Update');
            $this->load->model('Halqa');
            $data['Halqa'] = $this->Halqa->getById($id);
            $data['error'] = NULL;
            if(@count($data['Halqa']) > 0)
            {
                $this->load->model('Level');
                $this->load->model('User');
                $levelname = $this->Level->getById($data['Halqa'][0]->Level);
                $teachername = $this->User->getById($data['Halqa'][0]->Teacher);
                $data['Halqa'][0]->Level = $levelname[0]->Name;
                $data['Halqa'][0]->Teacher = $teachername[0]->Full_Name;
                $info = $this->User->getWhere('Type' , 3);
                foreach ($info as $value)
                {
                    $query = "Teacher = ".$value->Id;
                    $Teacher = $this->Halqa->getHalqaWhere($query);
                    if(@count($Teacher) == 0)
                        $data['Teacher'][@count ($data['Teacher'])] = $value;
                }
            }
            else
            {
                $data['error'] = "خطأ في الوصول";
            }
            $this->load->view('Supervisor/UpdateHalqa' , $data);
        }
        
        //set update halqa
        public function setUpdateHalqa()
        {
            $id = $this->input->post('UpdateHalqa');
            $data['error'] =  NULL;
            $this->load->model('Halqa');
            $data['Halqa'] = $this->Halqa->getById($id);
            if(@count($data['Halqa']) > 0)
            {
                $check = $this->input->post('Name');
                if(!empty($check))
                {
                    $info['Name'] = $this->input->post('Name');
                    $check = $this->Halqa->getByName($info['Name']);
                    if(@count($check) > 0)
                        $data['error'] = "اسم الحلقة موجود مسبقا";
                }
                $check = $this->input->post('Teacher');
                if(!empty($check))
                    $info['Teacher'] = $this->input->post('Teacher');
                $check = $this->input->post('Save');
                if(!empty($check))
                    $info['Save'] = $this->input->post('Save');
                if($data['error'] == NULL)
                {
                    $this->Halqa->Update($id , $info);
                    header("Location: HalqaM");
                }
                else
                {
                    $this->LevelM($data['error']);
                }
            }
            else
            {
                $data['error'] = "خطأ في الوصول";
                $this->HalqaM($data['error']);
            }
            
        }

        public function ViewHalqa()
        {
            $data['Halqa'] = $this->input->post_get('id');
            $infomation['error'] = NULL;
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
                    $query = " Semester = ".$data['Semester'][0]->Id." AND Halqa = ".$data['Halqa'];
                    $this->load->model('Course');
                    $infomation['Course'] = $this->Course->getWhere($query);
                    if(@count($infomation['Course']) > 0)
                    {
                        $query = "";
                        for($i = 0 ; $i < @count($infomation['Course']) ; $i++)
                        {
                            if($i != @count($infomation['Course'])-1)
                                $query = $query." Id = ".$infomation['Course'][$i]->Student." OR ";
                            else
                                $query = $query." Id = ".$infomation['Course'][$i]->Student;
                       }
                        $this->load->model('User');
                        $infomation['Student'] = $this->User->getUserWhere($query);
                        $this->load->model('Level');
                        $infomation['Level'] = $this->Level->getLevel();
                        $this->load->model('Halqa');
                        $infomation['Halqa'] = $this->Halqa->getHalqa();
                    }
                    else
                    {
                        $infomation['error'] = 'لايوجد طلاب في هذه الحلقة ';
                    }
                }
                else
                {
                    $infomation['error'] = 'لايوجد فصل دراسي حاليا';
                }
            }
            else
            {
                $information['error'] = 'لاتوجد فصول دراسي مضافة';
            }
            
            $this->load->view('Supervisor/ViewHalqa' , $infomation);
            
        }
        
        public function attendanceT()
        {
            $data['error'] = NULL;
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
                    $this->load->model('Level');
                    $query = "Supervisor = ".$this->session->userdata('Id');
                    $data['Level'] = $this->Level->getLevelWhere($query);
                    if(@count($data['Level']) > 0)
                    {
                        $day = date('l', strtotime($Now));
                        if($data['Level'][0]->$day == 1)
                        {
                            $this->load->model('Halqa');
                            $query = "Level = ".$data['Level'][0]->Id;
                            $data['Halqa'] = $this->Halqa->getHalqaWhere($query);
                            if(@count($data['Halqa']) > 0)
                            {
                                $query = "";
                                for($i = 0 ; $i < @count($data['Halqa']) ; $i++)
                                {
                                    if($i != @count($data['Halqa'])-1)
                                        $query = $query." Id = ".$data['Halqa'][$i]->Teacher." OR ";
                                    else
                                        $query = $query." Id = ".$data['Halqa'][$i]->Teacher;
                                }
                                $this->load->model('User');
                                $data['Teacher'] = $this->User->getUserWhere($query);
                                $data['None'] = NULL;
                                $data['Present'] = NULL;
                                $data['Late'] = NULL;
                                $data['Absent'] = NULL;
                                $this->load->model('Attendance');
                                foreach ($data['Teacher'] as $value)
                                {
                                    $query = "USer_Id = ".$value->Id;
                                    $CheckUser = $this->Attendance->getWhere($query);
                                    if(@count($CheckUser) == 0)
                                    {
                                        $data['None'][@count($data['None'])] = $value;
                                    }
                                    elseif($CheckUser[0]->Status == 1)
                                    {
                                        $data['Present'][@count($data['Present'])] = $value;
                                    }
                                    elseif($CheckUser[0]->Status == 2)
                                    {
                                        $data['Late'][@count($data['Late'])] = $value;
                                    }
                                    else
                                    {
                                        $data['Absent'][@count($data['Absent'])] = $value;
                                    }
                                }
                            }
                            else
                            {
                                $data['error'] = "لاتوجد حلقات مشرفا عليها";
                            }
                        }
                        else
                        {
                            $data['error'] = "لايوجد اليوم دراسة في المرحلة ";
                        }
                    }
                    else
                    {
                        $data['error'] = "لاتوجد مرحلة مشرفا عليها";
                    }
                }
                else
                {
                    $data['error'] = "انتهى الفصل الدراسي ".$data['Semester'][0]->Name;
                }
            }
            else
            {
                $data['error'] = "لايوجد فصول دراسية مضافة";
            }
            
            $this->load->view('Supervisor/attendanceT' , $data);
        }
        
        public function setAttendance()
        {
            $this->load->model('Attendance');
            $Absent = $this->input->post('Absent');
            $Late = $this->input->post('Late');
            $Present = $this->input->post('Present');
            if(isset($Absent))
            {
                $data['User_Id'] = $Absent;
                $data['Status'] = 3;
                $data['Date'] = date_create()->format('Y-m-d');
                $this->Attendance->InsertAttendance($data);
                header("Location: attendanceT");
            }
            
            if(isset($Late))
            {
                $data['User_Id'] = $Late;
                $data['Status'] = 2;
                $data['Date'] = date_create()->format('Y-m-d');
                $this->Attendance->InsertAttendance($data);
                header("Location: attendanceT");
            }
            
            if(isset($Present))
            {
                $data['User_Id'] = $Present;
                $data['Status'] = 1;
                $data['Date'] = date_create()->format('Y-m-d');
                $this->Attendance->InsertAttendance($data);
                header("Location: attendanceT");
            }
        }
        
        public function Logout()
        {
            $this->session->set_userdata('Id');
            $this->session->set_userdata('Full_Name');
            $this->session->set_userdata('Type');
            $this->session->set_userdata('username');
            header("Location: ../");
        }
        
        public function newAlert()
        {
            $this->load->view('Supervisor/newAlert');
        }
        
        public function addAlert()
        {
            $data['From'] = $this->session->userdata('Id');
            $data['To'] = $this->input->post('To');
            $data['Title'] = $this->input->post('Title');
            $data['Content'] = $this->input->post('Content');
            $data['Date'] = date_create()->format('Y-m-d');
            $timeZone = 'Asia/Riyadh';
            date_default_timezone_set('Asia/Riyadh');
            $data['Time'] = date_create()->format('H:i:s');
            $data['File_Path'] = "None";
            
            $config['upload_path'] = './upload/';
            $config['allowed_types'] = 'gif|jpg|png|txt';
            $config['max_size']	= '100000000000000000000000000000000000000000';
            $config['max_width']  = '1024';
            $config['max_height']  = '768';

            $this->load->library('upload', $config);
            if($this->upload->do_upload())
            {
                $data['File_Path'] = $this->upload->data();
                $data['File_Path'] = "./upload/".$data['File_Path']['file_name'];
            }
            
            $this->load->model('Alert');
            $this->Alert->InsertAlert($data);
            header("Location: AlertM");
        }
        
        public function AlertM()
        {
            $data['error'] = NULL;
            $this->load->model('User');
            $this->load->model('Alert');
            $query = "Type = 1";
            $user = $this->User->getUserWhere($query);
            if(@count($user) > 0)
            {
                $query = "";
                for($i = 0 ; $i < @count($user) ; $i++)
                {
                    if($i != @count($user)-1)
                        $query = $query." From = ".$user[$i]->Id." OR ";
                    else
                        $query = $query." From = ".$user[$i]->Id;
                }
            }
            $query = $query." OR From = ".$this->session->userdata('Id');
            $data['Alert'] = $this->Alert->getWhere($query);
            if(@count($data['Alert']) > 0)
            {
                foreach($data['Alert'] as $value)
                {
                    $query = "Id = ".$value->From;
                    $user = $this->User->getUserWhere($query);
                    $value->From = $user[0]->Full_Name;
                    if($value->To == 1)
                        $value->To = "جميع المستخدمين";
                    elseif($value->To == 2)
                        $value->To = "المشرفين فقط ";
                    elseif($value->To == 3)
                        $value->To = "المعلمين فقط";
                    else 
                        $value->To = "الطلاب فقط ";
                }
            }
            else
            {
                $data['error'] = "لاتوجد تنبيهات مضافة";
            }
            $this->load->view('Supervisor/AlertM' , $data);
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
            $this->load->view('Supervisor/ViewAlert' , $data);
        }
        
        public function MyAlert()
        {
            $data['error'] = NULL;
            $this->load->model('User');
            $this->load->model('Alert');
            $query = "From = ".$this->session->userdata('Id');
            $data['Alert'] = $this->Alert->getWhere($query);
            if(@count($data['Alert']) > 0)
            {
                foreach($data['Alert'] as $value)
                {
                    $query = "Id = ".$value->From;
                    $user = $this->User->getUserWhere($query);
                    $value->From = $user[0]->Full_Name;
                    if($value->To == 1)
                        $value->To = "جميع المستخدمين";
                    elseif($value->To == 2)
                        $value->To = "المشرفين فقط ";
                    elseif($value->To == 3)
                        $value->To = "المعلمين فقط";
                    else 
                        $value->To = "الطلاب فقط ";
                }
            }
            else
            {
                $data['error'] = "لاتوجد تنبيهات مضافة";
            }
            $this->load->view('Supervisor/MyAlert' , $data);
        }
        
        public function attendanceTt()
        {
            $information['error'] = NULL;
            $this->load->model('Level');
            $data['AllLevel'] = $this->Level->getWhere('Supervisor' , $this->session->userdata('Id'));
            if(@count($data['AllLevel']) > 0)
            {
                $query = "";
                $data['Level'] = NULL;
                $now = date_create()->format('Y-m-d');
                $DayName = date('l', strtotime($now));
                for($i = 0 ; $i < @count($data['AllLevel']) ; $i++)
                {
                    if($data['AllLevel'][$i]->$DayName == 1)
                        $data['Level'][@count ($data['Level'])] = $data['AllLevel'][$i];
                }
                print_r($data['Level']);
                die();
                for($i =  0 ; $i < @count($data['Level']) ; $i++)
                {
                    if($i != @count($data['Level'])-1)
                        $query = $query." Level = ".$data['Level'][$i]->Id." OR ";
                    else
                        $query = $query." Level = ".$data['Level'][$i]->Id;
                }
                if(!empty($query))
                {
                    $this->load->model('Halqa');
                    $data['Halqa'] = $this->Halqa->getHalqaWhere($query);
                    if(@count($data['Halqa']) > 0)
                    {
                        $query = "";
                        for($i =  0 ; $i < @count($data['Halqa']) ; $i++)
                        {
                            if($i != @count($data['Halqa'])-1)
                            {
                                if (strpos($query, $data['Halqa'][$i]->Teacher) == TRUE) 
                                    continue;
                                $query = $query." Id = ".$data['Halqa'][$i]->Teacher." OR ";
                            }
                            else
                            {
                                if (strpos($query, $data['Halqa'][$i]->Teacher) == TRUE) 
                                    continue;
                                $query = $query." Id = ".$data['Halqa'][$i]->Teacher;
                            }   
                        }

                        $query = explode(" " , $query);
                        if($query[@count($query)-2] == "OR")
                            $query[@count($query)-2] = "";
                        $query = implode(" ", $query);
                        $this->load->model('User');
                        $information['Teacher'] = $this->User->getUserWhere($query);

                        $this->load->model('Attendance');
                        $att['Teacher'] = NULL;
                        $att['absent'] = NULL;
                        $information['None'] = NULL;
                        $information['Present'] = NULL;
                        $information['Absent'] = NULL;
                        $information['Late'] = NULL;
                        foreach ($information['Teacher'] as $value)
                        {
                            $now = date_create()->format('Y-m-d');
                            $now = "'".$now."'";
                            $query = " User_Id = ".$value->Id." AND Date = ".$now;
                            $queryT = "Id = ".$value->Id;
                            $teacher_name = $this->User->getUserWhere($queryT);
                            $teacher_name = $teacher_name[0];
                            $result = $this->Attendance->getWhere($query);
                            if(@count($result) > 0)
                            {
                                if($result[0]->Status == 1)
                                    $information['Present'][@count ($information['Present'])] = $teacher_name;
                                elseif($result[0]->Status == 2)
                                    $information['Late'][@count ($information['Late'])] = $teacher_name;
                                else
                                    $information['Absent'][@count ($information['Absent'])] = $teacher_name;
                            }
                            else
                            {
                                $information['None'][@count ($information['None'])] = $teacher_name;
                            }
                        }
                    }
                    else
                    {
                        $information['error'] = 'لاتوجد حلقات مضافة في مراحلك';
                    }
                }
                else
                {
                    $information['error'] = 'لاتوجد حلقات مضافة في مرحلتك';
                }
            }
            else
            {
                $information['error'] = 'لايوجد معلمين مشرفا عليهم';
            }
            $this->load->view('Supervisor/attendanceT' , $information);
        }
        
        
        /*
         * 
         * Mail controller
         * 
         */
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
            $this->load->view('Supervisor/Inbox' , $data);
        }
        
        public function Compose()
        {
            $this->load->model('User');
            $query = "Type = 2 OR Type = 3";
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
                    $query = "Semester = ".$data['Semester'][0]->Id;
                    $info = $this->Course->getWhere($query);
                    $query = "";
                    for($i = 0 ; $i < @count($info) ; $i++)
                    {
                        if($i != @count($info)-1)
                            $query = $query." Id = ".$info[$i]->Student." OR ";
                        else
                            $query = $query." Id = ".$info[$i]->Student;
                    }
                    $student = $this->User->getUserWhere($query);
                    foreach ($student as $value)
                    {
                        $data['Users'][@count($data['Users'])] = $value;
                    }
                }
            }
            
            //to get how many mesaage not read;
            $this->load->model('Email');
            $query = "To = ".$this->session->userdata('Id')." AND Read = 0";
            $data['Count'] = $this->Email->getWhere($query);
            $data['Count'] = @count($data['Count']);
            $this->load->view('Supervisor/Compose', $data);
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
            $this->load->view('Supervisor/Send' , $data);
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
            $this->load->view('Supervisor/MailSend' , $data);
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
                
                $query = "Email = ".$id;
                $data['Reply'] = $this->Email->getWhere($query);  
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
            else
            {
                $data['error'] = "خطا في الدخول";
            }
            $this->load->view('Supervisor/ViewMail' , $data);
        }
        
        public function Reply()
        {
            $data['Id'] = $this->input->post('reply');
            //to get how many mesaage not read;
            $this->load->model('Email');
            $query = "To = ".$this->session->userdata('Id')." AND Read = 0";
            $data['Count'] = $this->Email->getWhere($query);
            $data['Count'] = @count($data['Count']);
            
            $this->load->view('Supervisor/Reply' , $data);
        }
        
        public function SendReply()
        {
            $id = $this->input->post('Email');
            $this->load->model('Email');
            $mail = $this->Email->getById($id);
            $data['Email'] = $id;
            $data['From'] = $this->session->userdata('Id');
            $data['To'] = $mail[0]->From;
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
            
            $this->Email->InsertEmail($data);
            header("Location: Inbox");
        }
        
        public function Requests()
        {
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
                    $this->load->model('Request');
                    $query = "Semester = ".$data['Semester'][0]->Id." AND Supervisor = ".$this->session->userdata('Id');
                    $data['Request'] = $this->Request->getWhere($query);
                    if(@count($data['Request']) == 0)
                        $data['error'] = 'لايوجد طلبات في هذا الفصل الدراسي';
                }
                else
                {
                    $data['error'] = 'لايوجد فصل دراسي حاليا';
                }
            }
            else
            {
                $data['error'] = 'لاتوجد فصول دراسي مضافة';
            }
            $this->load->view('Supervisor/Requests' , $data);
        }
        
        public function newRequest()
        {
            $data['error'] = NULL;
            $this->load->model('Semester');
            $data['Semester'] = $this->Semester->LastSemester();
            if(@count($data['Semester']) > 0)
            {
                $End =  $data['Semester'][0]->End;
                $Now = date_create()->format('Y-m-d');
                $datetime1 = new DateTime($End);
                $datetime2 = new DateTime($Now);
                if($datetime1 <= $datetime2)
                {
                    $data['error'] = 'لايمكنك رفع طلبات حاليا';
                }
            }
            else
            {
                $data['error'] = 'لاتوجد فصول دراسي مضافة';
            }
            $this->load->view('Supervisor/newRequest' ,$data);
        }
        
        public function addRequest()
        {
            $info['Supervisor'] = $this->session->userdata('Id');
            $info['Semester'] = $this->input->post('InsertRequest');
            $info['Name'] = $this->input->post('Name');
            $info['Cost'] = $this->input->post('Cost');
            $info['Comment'] = $this->input->post('Comment');
            $info['Start'] = $this->input->post('Start');
            $info['End'] = $this->input->post('End');
            $info['Approve'] = 0;
            $this->load->model('Request');
            $this->Request->InsertRequest($info);
            header("Location: Requests");

        }
        
        public function ViewRequest()
        {
            $id = $this->input->post_get('id');
            $data['error'] = NULL;
            $this->load->model('Semester');
            $data['Semester'] = $this->Semester->LastSemester();
            if(@count($data['Semester']) > 0)
            {
                $End =  $data['Semester'][0]->End;
                $Now = date_create()->format('Y-m-d');
                $datetime1 = new DateTime($End);
                $datetime2 = new DateTime($Now);
                if($datetime1 <= $datetime2)
                {
                    $data['error'] = 'خطأ في ال,,,,,وصول';
                }
            }
            else
            {
                $data['error'] = 'لايوجد فصول دراسية مضافة';
            }
            
            if(empty($data['error']))
            {
                $this->load->model('Request');
                $query = "Id = ".$id." AND Supervisor = ".$this->session->userdata('Id')." AND Semester = ".$data['Semester'][0]->Id;
                $data['Request'] = $this->Request->getWhere($query);
                if(@count($data['Request']) == 0)
                    $data['error'] = 'خطأ في الوصول';
                else
                {
                    $data['Request'][0]->Semester = $data['Semester'][0]->Name;
                }
            }
            $this->load->view('Supervisor/ViewRequest' , $data);
        }
    }
?>