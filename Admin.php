<?php
    class Admin extends CI_Controller {
        public function index()
	{
            header("Location:Dashboard"); 
	}
        
        public function Logout()
        {
            $this->session->set_userdata('Id');
            $this->session->set_userdata('Full_Name');
            $this->session->set_userdata('Type');
            $this->session->set_userdata('username');
            header("Location: ../");
        }
        
        //View Dashboard;
        public function Dashboard()
        {
            
            /*
             * عدد المشرفين + عدد المعلمين + عدد الطلاب + المتبقي من الميزانية
             */
            $data['error'] = NULL;
            $this->load->model('Semester');
            $this->load->model('Course');
            $this->load->model('Level');
            $this->load->model('Halqa');
            $this->load->model('Revenue');
            $this->load->model('Request');
            $this->load->model('User');
            $this->load->model('Email');
            $this->load->model('Alert');
            $this->load->model('Attendance');
            $data['Semester'] = $this->Semester->LastSemester();
            if(@count($data['Semester']) > 0)
            {
                $data['Course'] = $this->Course->getBySemester($data['Semester'][0]->Id);
                $dashboard['TotalStudent'] = @count($data['Course']);//عدد الطلاب
                
                // من هنا لحساب عدد المشرفين
                $dashboard['TotalSupervisor'] = 0;
                $data['AllLevel'] = $this->Level->getLevel();
                if(@count($data['AllLevel']) > 0)
                {
                    foreach ($data['AllLevel'] as $value)
                    {
                        $query = "Semester = ".$data['Semester'][0]->Id." AND Level = ".$value->Id;
                        $course = $this->Course->getWhere($query);
                        if(@count($course) > 0)
                            $dashboard['TotalSupervisor'] = $dashboard['TotalSupervisor']+1;
                    }
                }
                else
                {
                    $dashboard['TotalSupervisor'] = 0;
                }

                // من هنا لحساب عدد المعلمين
                $dashboard['TotalTeacher'] = 0;
                $data['AllHalqa'] = $this->Halqa->getHalqa();
                if(@count($data['AllHalqa']) > 0)
                {
                    foreach ($data['AllHalqa'] as $value)
                    {
                        $query = "Semester = ".$data['Semester'][0]->Id." AND Halqa = ".$value->Id;
                        $course = $this->Course->getWhere($query);
                        if(@count($course) > 0)
                            $dashboard['TotalTeacher'] = $dashboard['TotalTeacher']+1;
                    }
                }
                else
                {
                    $dashboard['TotalTeacher'] = 0;
                }
                
                //من هنا حساب الدعم بالكامل
                $data['Revenue'] = $this->Revenue->getBySemester($data['Semester'][0]->Id);
                if(@count($data['Revenue']) > 0)
                {
                    $data['TotalRevenue'] = 0;
                    foreach ($data['Revenue'] as $value)
                        $data['TotalRevenue'] = $value->Cost + $data['TotalRevenue'];
                }
                else
                {
                    $data['TotalRevenue'] = 0;
                }
                
                // من هنا حساب المصرفات
                $query = "Semester = ".$data['Semester'][0]->Id." AND Approve = 1";
                $data['Request'] = $this->Request->getWhere($query);
                //لحساب المصروفات المؤكدة
                if(@count($data['Request']) > 0)
                {
                    $data['TotalExpenses'] = 0;
                    foreach ($data['Request'] as $value)
                    {
                        if($value->Approve == 1)
                            $data['TotalExpenses'] = $value->Cost + $data['TotalExpenses'];
                    }
                }
                else
                {
                    $data['TotalExpenses'] = 0;
                }
                
                $dashboard['Residual'] = $data['TotalRevenue'] - $data['TotalExpenses'] ; // المتبقي من الميزانية
                
                //آخر 10 طلاب مسجلين
                $dashboard['Last10Student'] = $this->User->getLastTeen();
                if(@count($dashboard['Last10Student']) == 0)
                    $dashboard['error1'] = "لايوجد طلاب مسجلين في قاعدة البيانات";
                
                // آخر 10 رسائل بالصندوق الوارد
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
                
                // آخر 10 تنبيهات
                $dashboard['Alert'] = $this->Alert->getLastTeen();
                if(@count($dashboard['Alert']) > 0)
                {
                    foreach($dashboard['Alert'] as $value)
                    {
                        $user = $this->User->getById($value->From);
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
                    $dashboard['error3'] = "لاتوجد تنبيهات مضافة";
                } 
                
                //الحضور والغياب لكل مرحلة
                if(@count($data['AllLevel']) > 0)
                {
                    $dashboard['Attendance'] = array();
                    foreach ($data['AllLevel'] as $value)
                    {
                        $query = "Semester = ".$data['Semester'][0]->Id." AND Level = ".$value->Id;
                        $data['Student'] = $this->Course->getWhere($query);
                        if(@count($data['Student']) > 0)
                        {
                            $name = $this->Level->getById($data['Student'][0]->Level);
                            $present = 0;
                            $absent = 0;
                            $late = 0;
                            $Now = date_create()->format('Y-m-d');
                            $day = date('l', strtotime($Now));
                            if($value->$day == 1)
                            {
                                foreach ($data['Student'] as $chack_att)
                                {
                                    $query = "User_Id = ".$chack_att->Student." AND Date = "."'".$Now."'";
                                    $att = $this->Attendance->getWhere($query);
                                    if(@count($att) > 0)
                                    {
                                        if($att[0]->Status == 1)
                                            $present = $present+1;
                                        elseif($att[0]->Status == 2)
                                            $late = $late+1;
                                        else
                                            $absent = $absent+1;
                                    }
                                    else
                                    {
                                        $present = $present+1;
                                    }
                                }
                                $present = $present / @count($data['Student']) * 100;
                                $absent = $absent / @count($data['Student']) * 100;
                                $late = $late / @count($data['Student']) * 100;
                                $dashboard['Attendance'][@count($dashboard['Attendance'])] = array(
                                    "Name" => $name[0]->Name,
                                    "Present" => $present,
                                    "Absent" => $absent,
                                    "Late" => $late,
                                    "ErrorA" => 0,
                                );
                            }
                            else
                            {
                                $dashboard['Attendance'][@count($dashboard['Attendance'])] = array(
                                    "Name" => $name[0]->Name,
                                    "Present" => NULL,
                                    "Absent" => NULL,
                                    "Late" => NULL,
                                    "ErrorA" => 1,
                                );
                            }
                        }
                    }
                }
            }
            else
            {
                $data['error'] = "لايوجد فصل دراسي مضاف";
            }
            $this->load->view('Admin/Dashboard' , $dashboard);
        }
        ////////////////////////////////////////////////////////////////////////
        //View Student Management
        public function StudentM()
        {
            $this->load->model('User');
            $data['student'] = $this->User->getWhere('Type' , 4);
            $this->load->view('Admin/StudentM' , $data);
        }
        
        public function UpdateUser()
        {
            $id = $this->input->post_get('id');
            $this->load->model('User');
            $data['User'] = $this->User->getById($id);
            $data['User'][0]->Halqa = "NULL";
            
            $data['error'] = NULL;
            if(@count($data['User']) > 0)
            {
                if($data['User'][0]->Type == 4)
                {
                    $this->load->model('Course');
                    $course = $this->Course->getByStudent($id);
                    if(@count($course) > 0)
                    {
                        $this->load->model('Halqa');
                        $halqa = $this->Halqa->getById($course[0]->Halqa);
                        $data['User'][0]->Halqa = $halqa[0]->Name;
                    }
                    else
                    {
                        $data['User'][0]->Halqa = "لا توجد حلقة للطالب";
                    }
                    
                }
                else
                {
                    $data['User'][0]->Halqa = "هذه الحساب ليس طالباً";
                }
                $this->load->model('Nationality');
                $name = $this->Nationality->getById($data['User'][0]->Nationality);
                $data['User'][0]->Nationality = $name[0]->Name;
                $data['Nationality'] = $this->Nationality->getByStatus(1);
            }
            else
            {
                $data['error'] = "خطأ في الوصول";
            }
            $this->load->view('Admin/UpdateUser' , $data);
        }
        
        public function setUpdate()
        {
            $id = $this->input->post_get('UpdateUser');
            $this->load->model('User');
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
                $check = $this->input->post('Type');
                if(!empty($check))
                    $info['Type'] = $this->input->post('Type');
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

        ////////////////////////////////////////////////////////////////////////
        //View Supervisor Management
        public function SupervisorM()
        {
            $this->load->model('User');
            $data['supervisor'] = $this->User->getWhere('Type' , 2);
            $this->load->view('Admin/SupervisorM' , $data);
        }
        ////////////////////////////////////////////////////////////////////////
        //View Teacher Management
        public function TeacherM()
        {
            $this->load->model('User');
            $data['teacher'] = $this->User->getWhere('Type' , 3);
            $this->load->view('Admin/TeacherM', $data);
        }
        ////////////////////////////////////////////////////////////////////////
        //View New User;
        public function newUser($error = NULL)
        {
            $data['error'] = NULL;
            if($error != NULL)
            {
                foreach ($error as $value)
                {
                    $data['error'][@count($data['error'])] = $value;
                }
            }
            $this->load->model('Nationality');
            $data['Nationality'] = $this->Nationality->getByStatus(1);
            $this->load->view('Admin/newUser' , $data);
        }
        //Insert New User To Database;
        public function addUser()
        {
            $error = array();
            $this->load->model('User');
            $data['Full_Name']      = $this->input->post('Fname');
            $data['Full_Name']      = $data['Full_Name']." ".$this->input->post('Mname');
            $data['Full_Name']      = $data['Full_Name']." ".$this->input->post('Gname');
            $data['Full_Name']      = $data['Full_Name']." ".$this->input->post('Lname');
            $data['Email']          = $this->input->post('Email');
            $check_email = $this->User->getByEmail($data['Email']);
            if(@count($check_email) > 0)
                $error[@count ($error)] = " الايميل موجود بالغعل".$data['Email'];
            $data['Password']       = md5($this->input->post('Password'));
            $data['Mobile']         = $this->input->post('Mobile');
            $data['Age']            = $this->input->post('Age');
            $data['National_Id']    = $this->input->post('National_Id');
            $check_id = $this->User->getByNational_Id($data['National_Id']);
            if(@count($check_id) > 0)
                $error[@count ($error)] = " الهوية موجودة بالغعل".$data['National_Id'];
            if($this->input->post('Nationality') == "Others")
            {
                $this->load->model('Nationality');
                $info['Name']    = $this->input->post('otherz');
                $info['Status'] = 0;
                $query = "Name = "."'".$info['Name']."'"." AND Status = 1";
                $check = $this->Nationality->getWhere($query);
                if(@count($check) > 0)
                {
                    $error[@count ($error)] = "الجنسية موجودة بالفعل";
                }
                else
                {
                    $this->Nationality->InsertNationality($info);
                    $query = "Name = "."'".$info['Name']."'";
                    $check = $this->Nationality->getWhere($query);
                    $data['Nationality']    = $check[0]->Id;
                }
            }
            else
            {
                $data['Nationality']    = $this->input->post('Nationality');
            }
            $data['Register_Date']  = date_create()->format('Y-m-d H:i:s');
            $data['Last_Login']     = date_create()->format('Y-m-d H:i:s');
            $data['Approve']        = $this->input->post('Approve');
            $data['Status']         = 2;
            $data['Type']           = $this->input->post('Type');
            if($error == NULL)
            {
                $this->load->model('User');
                $this->User->InsertUser($data);
                if($data['Type'] == 1)
                {
                    $this->Dashboard();
                }
                elseif($data['Type'] == 2)
                {
                    $this->SupervisorM();
                }elseif($data['Type'] == 3)
                {
                    $this->TeacherM();
                }else
                {
                    $this->StudentM();
                }
            }
            else
            {
                $this->newUser($error);
            }
            
            
        }
        //Delete User From Database;
        public function deleteUser()
        {
            $id = $this->input->post('Delete');
            $this->load->model('User');
            $this->User->DeleteUser($id);
            $this->Dashboard();
        }
        ////////////////////////////////////////////////////////////////////////
        //View Nationality Management;
        public function NationalityM()
        {
            $this->load->model('Nationality');
            $data['Nationality'] = $this->Nationality->getNationality();
            $this->load->view('Admin/NationalityM', $data);
        }
        //View Update Nationality
        public function UpdateNationality()
        {
            $id = $this->input->post_get('id');
            $this->load->model('Nationality');
            $data['error'] = NULL;
            $data['Nationality'] = $this->Nationality->getById($id);
            if(@count($data['Nationality']) == 0)
            {
                $data['error'] = "خطأ في الوصول";
            }
            $this->load->view('Admin/UpdateNationality' , $data);
        }
        // Set Update Nationality
        public function setUpdateNationality()
        {
            $id = $this->input->post_get('UpdateNationality');
            $data['error'] = NULL;
            $this->load->model('Nationality');
            $data['Nationality'] = $this->Nationality->getById($id);
            if(@count($data['Nationality']) == 0)
            {
                $data['error'] = "خطأ في الوصول";
            }
            else
            {
                $check = $this->input->post('Name');
                if(!empty($check))
                    $info['Name'] = $this->input->post('Name');
                $check = $this->input->post('Status');
                if(!empty($check))
                    $info['Status'] = $this->input->post('Status');
                $this->Nationality->Update($id , $info);
                header("Location: NationalityM");
            }
        }

        //View New Nationality;
        public function newNationality()
        {
            $this->load->view('Admin/newNationality');
        }
        //Insert New National To Database;
        public function addNationality()
        {
            $data['Name'] = $this->input->post('Name');
            $data['Status'] = $this->input->post('Status');
            $this->load->model('Nationality');
            $this->Nationality->InsertNationality($data);
            $this->NationalityM();
        }
        //Delete National From Database;
        public function deleteNationality()
        {
            $id = $this->input->post('Delete');
            $this->load->model('Nationality');
            $this->Nationality->DeleteNationality($id);
            $this->NationalityM();
        }
        ////////////////////////////////////////////////////////////////////////
        //View Level Management
        public function LevelM($error = NULL)
        {
            $this->load->model('Level');
            $data['error'] = $error;
            $data['Level'] = $this->Level->getLevel();
            $this->load->view('Admin/LevelM' , $data);
        }
        // Update Level
        public function UpdateLevel()
        {
            $id = $this->input->post_get('id');
            $this->load->model('Level');
            $data['Level'] = $this->Level->getById($id);
            $data['error'] = NULL;
            if(@count($data['Level']) > 0)
            {
                $this->load->model('User');
                $user = $this->User->getById($data['Level'][0]->Supervisor);
                $data['Level'][0]->Supervisor = $user[0]->Full_Name;
                
                $info = $this->User->getWhere('Type' , 2);
                $data['Supervisor'] = NULL;
                foreach ($info as $value)
                {
                    $query = "Supervisor = ".$value->Id;
                    $supervisor = $this->Level->getLevelWhere($query);
                    if(@count($supervisor) == 0)
                        $data['Supervisor'][@count($data['Supervisor'])] = $value;
                }
            }
            else
            {
                $data['error'] = "خطأ في الوصول";
            }
            $this->load->view('Admin/UpdateLevel' , $data);
        }
        //Set Update Level
        public function setUpdateLevel()
        {
            $id = $this->input->post('UpdateUser');
            $data['error'] =  NULL;
            $this->load->model('Level');
            $data['Level'] = $this->Level->getById($id);
            if(@count($data['Level']) > 0)
            {
                $check = $this->input->post('Name');
                if(!empty($check))
                {
                    $info['Name'] = $this->input->post('Name');
                    $check = $this->Level->getByName($info['Name']);
                    if(@count($check) > 0)
                        $data['error'] = "اسم المرحلة موجود مسبقا";
                }
                $check = $this->input->post('Supervisor');
                if(!empty($check))
                    $info['Supervisor'] = $this->input->post('Supervisor');
                $check = $this->input->post('Saturday');
                if(!empty($check))
                    $info['Saturday'] = $this->input->post('Saturday');
                $check = $this->input->post('Sunday');
                if(!empty($check))
                    $info['Sunday'] = $this->input->post('Sunday');
                $check = $this->input->post('Monday');
                if(!empty($check))
                    $info['Monday'] = $this->input->post('Monday');
                $check = $this->input->post('Tuesday');
                if(!empty($check))
                    $info['Tuesday'] = $this->input->post('Tuesday');
                $check =$this->input->post('Wednesday') ;
                if(!empty($check))
                    $info['Wednesday'] = $this->input->post('Wednesday');
                $check = $this->input->post('Thursday');
                if(!empty($check))
                    $info['Thursday'] = $this->input->post('Thursday');
                $check = $this->input->post('Friday');
                if(!empty($check))
                    $info['Friday'] = $this->input->post('Friday');
                
                if($data['error'] == NULL)
                {
                    $this->Level->Update($id , $info);
                    header("Location: LevelM");
                }
                else
                {
                    $this->LevelM($data['error']);
                }
            }
            else
            {
                $data['error'] = "خطأ في الوصول";
                $this->LevelM($data['error']);
            }
            
        }
        //View New Level
        public function newLevel()
        {
            $this->load->model('User');
            $data['Supervisor'] = NULL;
            $info = $this->User->getWhere('Type' , 2);
            $this->load->model('Level');
            foreach ($info as $value)
            {
                $query = "Supervisor = ".$value->Id;
                $supervisor = $this->Level->getLevelWhere($query);
                if(@count($supervisor) == 0)
                    $data['Supervisor'][@count($data['Supervisor'])] = $value;
            }
            $this->load->view('Admin/newLevel' , $data);
        }
        //Insert Level To Database
        public function addLevel()
        {
            $data['Name'] = $this->input->post('Name');
            $data['Supervisor'] = $this->input->post('Supervisor');
            $data['Saturday'] = $this->input->post('Saturday');
            if($data['Saturday'] != 1)
                $data['Saturday'] = 0;
            $data['Sunday'] = $this->input->post('Sunday');
            if($data['Sunday'] != 1)
                $data['Sunday'] = 0;
            $data['Monday'] = $this->input->post('Monday');
            if($data['Monday'] != 1)
                $data['Monday'] = 0;
            $data['Tuesday'] = $this->input->post('Tuesday');
            if($data['Tuesday'] != 1)
                $data['Tuesday'] = 0;
            $data['Wednesday'] = $this->input->post('Wednesday');
            if($data['Wednesday'] != 1)
                $data['Wednesday'] = 0;
            $data['Thursday'] = $this->input->post('Thursday');
            if($data['Thursday'] != 1)
                $data['Thursday'] = 0;
            $data['Friday'] = $this->input->post('Friday');
            if($data['Friday'] != 1)
                $data['Friday'] = 0;
            $this->load->model('Level');
            $this->Level->InsertLevel($data);
            $this->LevelM();
        }
        //Delete Level From Database
        public function deleteLevel()
        {
            $id = $this->input->post('Delete');
            $this->load->model('Level');
            $this->Level->DeleteLevel($id);
            $this->LevelM();
        }
        ////////////////////////////////////////////////////////////////////////
        //View Halqat Management 
        public function HalqaM()
        {
            $id = $this->input->post_get('id');
            $this->load->model('Halqa');
            $this->load->model('User');
            $data['Halqa'] = $this->Halqa->getWhere('Level' , $id);
            $data['Teacher'] = $this->User->getWhere('Type' , 3);
            $data['Level'] = $id;
            $this->load->view('Admin/HalqaM' , $data);
        }
        //update halqa
        public function UpdateHalqa()
        {
            $id = $this->input->post_get('id');
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
            $this->load->view('Admin/UpdateHalqa' , $data);
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
                $check =$this->input->post('Teacher') ;
                if(!empty($check))
                    $info['Teacher'] = $this->input->post('Teacher');
                $check = $this->input->post('Save');
                if(!empty($check))
                    $info['Save'] = $this->input->post('Save');
                if($data['error'] == NULL)
                {
                    $this->Halqa->Update($id , $info);
                    header("Location: LevelM");
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
        //View New Halqa
        public function newHalqa()
        {
            $id = $this->input->post('Level');
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
            $data['Level'] = $id;
            $this->load->view('Admin/newHalqa' , $data);
        }
        //Insert Halqa To Databae
        public function addHalqa()
        {
            $data['Name'] = $this->input->post('Name');
            $data['Level'] = $this->input->post('InserHalqa');
            $data['Save'] = $this->input->post('Save');
            $data['Teacher'] = $this->input->post('Teacher');
            $this->load->model('Halqa');
            $this->Halqa->InsertHalqa($data);
            header("Location: LevelM");
        }
        //Delete Halqa From Database
        public function deleteHalqa()
        {
            $id = $this->input->post('Delete');
            $this->load->model('Halqa');
            $this->Halqa->DeleteHalqa($id);
            $this->LevelM();
        }
        ////////////////////////////////////////////////////////////////////////
        //View Semester Management
        public function SemesterM($error = NULL)
        {
            $data['error'] = $error;
            $this->load->model('Semester');
            $data['Semester'] = $this->Semester->getSemester();
            $this->load->view('Admin/SemesterM' , $data);
        }
        //View Update Semester
        public function UpdateSemester()
        {
            $id = $this->input->post_get('id');
            $data['error'] = NULL;
            $this->load->model('Semester');
            $data['Semester'] = $this->Semester->getById($id);
            if(@count($data['Semester']) == 0)
                $data['error'] = "خطأ في الوصول";
            
            $this->load->view('Admin/UpdateSemester' , $data);
        }
        // Set Update Semester
        public function setUpdateSemester()
        {
            $id = $this->input->post('UpdateSemester');
            $data['error'] = NULL;
            $this->load->model('Semester');
            $data['Semester'] = $this->Semester->getById($id);
            if(@count($data['Semester']) == 0)
                $data['error'] = "خطأ في الوصول";
            else
            {
                $check = $this->input->post('Name');
                if(!empty($check))
                {
                    $info['Name'] = $this->input->post('Name');
                    $name = $this->Semester->getByName($info['Name']) ;
                    if(@count($name) > 0)
                        $data['error'] = "الاسم موجود مسبقا";
                }
                $check = $this->input->post('Start');
                if(!empty($check))
                    $info['Start'] = $this->input->post('Start');
                $check = $this->input->post('End');
                if(!empty($check))
                    $info['End'] = $this->input->post('End');
                $check = $this->input->post('Status');
                if(!empty($check))
                    $info['Status'] = $this->input->post('Status');
                
                if($data['error'] == NULL)
                {
                    $this->Semester->Update($id , $info);
                    header("Location: SemesterM");
                }
                else
                {
                    $this->SemesterM($data['error']);
                }
            }
        }
        //View New Semester
        public function newSemester()
        {
            $this->load->view('Admin/newSemester');
        }
        //Insert Semester To Database
        public function addSemester()
        {
            $data['Name'] = $this->input->post('Name');
            $data['Start'] = $this->input->post('Start');
            $data['End'] = $this->input->post('End');
            $data['Status'] = $this->input->post('Status');
            $this->load->model('Semester');
            $this->Semester->InsertSemester($data);
            $this->SemesterM();
        }
        //Delete Semester From Database
        public function deleteSemester()
        {
            $id = $this->input->post('Delete');
            $this->load->model('Semester');
            $this->Semester->DeleteSemester($id);
            $this->SemesterM();
        }
        ////////////////////////////////////////////////////////////////////////
        public function First($error = NULL)
        {
            $data['error'] = $error;
            $this->load->view('Admin/First' , $data);
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
                $this->load->view('Admin/Seconde' , $data);
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
                $this->load->view('Admin/Third' , $data);
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
            $this->load->view('Admin/Furth' , $data);
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
            $this->load->view('Admin/DoneRegistration' , $information);
        }
        ////////////////////////////////////////////////////////////////////////
        public function newAlert()
        {
            $this->load->view('Admin/newAlert');
        }
        
        public function addAlert()
        {
            $data['From'] = $this->session->userdata('Id');;
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
            $data['Alert'] = $this->Alert->getAlert();
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
            $this->load->view('Admin/AlertM' , $data);
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
            $this->load->view('Admin/ViewAlert' , $data);
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
            $this->load->view('Admin/Inbox' , $data);
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
            $this->load->view('Admin/Compose', $data);
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
            $this->load->view('Admin/Send' , $data);
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
            $this->load->view('Admin/MailSend' , $data);
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
            
            $this->load->view('Admin/ViewMail' , $data);
        }
        
        
        ///الميزانية
        
        public function Revenues()
        {
            $this->load->model('Semester');
            $data['Semester'] = $this->Semester->getSemester();
            $this->load->view('Admin/Revenues' , $data);
        }
        public function UpdateRevenues()
        {
            $id = $this->input->post_get('id');
            $data['error'] = NULL;
            $this->load->model('Revenue');
            $data['Revenue'] = $this->Revenue->getById($id);
            if(@count($data['Revenue']) > 0)
            {
                $this->load->model('Semester');
                $name = $this->Semester->getById($data['Revenue'][0]->Semester);
                $data['Revenue'][0]->Semester = $name[0]->Name;
                $data['Semester'] = $this->Semester->getSemester();
            }
            else
            {
                $data['error'] = "خطأ في الوصول";
            }
            $this->load->view('Admin/UpdateRevenues', $data);
        }
        
        public function setUpdateRevenue()
        {
            $id = $this->input->post('UpdateRevenue');
            $data['error'] = NULL;
            $this->load->model('Revenue');
            $data['Revenue'] = $this->Revenue->getById($id);
            if(@count($data['Revenue']) > 0)
            {
                $check = $this->input->post('Semester');
                if(!empty($check))
                    $info['Semester'] = $this->input->post('Semester');
                $check = $this->input->post('Name');
                if(!empty($check))
                    $info['Name'] = $this->input->post('Name');
                $check = $this->input->post('National');
                if(!empty($check))
                    $info['National'] = $this->input->post('National');
                $check = $this->input->post('Cost');
                if(!empty($check))
                    $info['Cost'] = $this->input->post('Cost');
                $check = $this->input->post('Type');
                if(!empty($check))
                    $info['Type'] = $this->input->post('Type');
                
                $this->Revenue->Update($id , $info);
                header("Location: Revenues");
            }
            else
                header ("Location: Dashboard");
        }

        public function ViewRevenues()
        {
            $id = $this->input->post('Semester');
            $data['error'] = NULL;
            $this->load->model('Semester');
            $data['Semester'] = $this->Semester->getById($id);
            $this->load->model('Revenue');
            $data['Revenue'] = $this->Revenue->getBySemester($id);
            if(@count($data['Revenue']) ==0)
                $data['error'] = "لايوجد دعم لهذا الفصل الدراسي";
            else
            {
                $data['Total'] = 0;
                foreach ($data['Revenue'] as $value)
                {
                    $data['Total'] = $value->Cost + $data['Total'];
                }
            }
            $this->load->view('Admin/ViewRevenues' , $data);
        }

        public function addRevenues()
        {
            $data['Semester'] = $this->input->post('Semester');
            $data['Name'] = $this->input->post('Name');
            $data['National'] = $this->input->post('National');
            $data['Cost'] = $this->input->post('Cost');
            $data['Type'] = $this->input->post('Type');
            $this->load->model('Revenue');
            $this->Revenue->InsertRevenue($data);
            header("Location: Revenues");
        }

        public function Request()
        {
            $this->load->model('Request');
            $data['error']= NULL;
            $data['Request'] = $this->Request->getRequest();
            if(@count($data['Request']) > 0)
            {
                $this->load->model('Semester');
                foreach ($data['Request'] as $value)
                {
                    $semester = $this->Semester->getById($value->Semester);
                    $value->Semester = $semester[0]->Name;
                }
            }
            else
            {
                $data['error']= 'لاتوجد طلبات';
            }
            $this->load->view('Admin/Request' , $data);
        }
        
        public function ViewRequest()
        {
            $id = $this->input->post_get('id');
            $data['error'] = NULL;
            $this->load->model('Request');
            $data['Request'] = $this->Request->getById($id);
            if(@count($data['Request']) == 0)
                $data['error'] = 'خطأ في الوصول';
            else
            {
                $this->load->model('Semester');
                $semester = $this->Semester->getById($data['Request'][0]->Semester);
                $data['Request'][0]->Semester = $semester[0]->Name;
                $this->load->model('User');
                $supervisor = $this->User->getById($data['Request'][0]->Supervisor);
                $data['Request'][0]->Supervisor = $supervisor[0]->Full_Name;
                
                $this->load->model('Revenue');
                //من هنا حساب الدعم بالكامل
                $info['Revenue'] = $this->Revenue->getBySemester($semester[0]->Id);
                if(@count($info['Revenue']) > 0)
                {
                    $info['TotalRevenue'] = 0;
                    foreach ($info['Revenue'] as $value)
                        $info['TotalRevenue'] = $value->Cost + $info['TotalRevenue'];
                }
                else
                {
                    $info['TotalRevenue'] = 0;
                }
                
                // من هنا حساب المصرفات
                $query = "Semester = ".$semester[0]->Id." AND Approve = 1";
                $info['Request'] = $this->Request->getWhere($query);
                //لحساب المصروفات المؤكدة
                if(@count($info['Request']) > 0)
                {
                    $info['TotalExpenses'] = 0;
                    foreach ($info['Request'] as $value)
                    {
                        if($value->Approve == 1)
                            $info['TotalExpenses'] = $value->Cost + $info['TotalExpenses'];
                    }
                }
                else
                {
                    $info['TotalExpenses'] = 0;
                }
                
                $data['Residual'] = $info['TotalRevenue'] - $info['TotalExpenses'] ;
            }
            $this->load->view('Admin/ViewRequest' , $data);
        }
        
        public function UpdateRequest()
        {
            $Status ;
            $check = $this->input->post('Reject');
            if(!empty($check))
            {
                $id = $this->input->post('Reject');
                $data['Approve'] = 2;
            }
            $check = $this->input->post('Approve');
            if(!empty($check))
            {
                $id = $this->input->post('Approve');
                $data['Approve'] = 1;
            }
            $this->load->model('Request');
            $this->Request->Update($id , $data);
            header("Location: Request");
        }
        
        public function newRevenue()
        {
            $this->load->model('Semester');
            $data['Semester'] = $this->Semester->getSemester();
            $this->load->view('Admin/newRevenue' , $data);
        }
        
        public function Expenses()
        {
            $this->load->model('Semester');
            $data['error'] = NULL;
            $data['Semester'] = $this->Semester->getSemester();
            if(@count($data['Semester']) == 0)
                $data['error'] = "لاتوجد فصول دراسية مضافة";
            $this->load->view('Admin/Expenses' , $data);
        }
        
        public function ViewExpenses()
        {
            $id = $this->input->post('Semester');
            // لجلب مبلغ الدعم بالكامل
            $this->load->model('Revenue');
            $data['Revenue'] = $this->Revenue->getBySemester($id);
            if(@count($data['Revenue']) == 0)
            {
                $data['Total'] = 0;
            }
            else
            {
                $data['Total'] = 0;
                foreach ($data['Revenue'] as $value)
                {
                    $data['Total'] = $value->Cost + $data['Total'];
                }
            }
            
            $this->load->model('Request');
            $query = "Semester = ".$id." AND Approve = 1";
            $data['TotalComplate'] = $this->Request->getWhere($query);
            $data['Expenses'] = 0;
            if(@count($data['TotalComplate']) > 0)
            {
                foreach ($data['TotalComplate'] as $value)
                {
                    $data['Expenses'] = $data['Expenses'] + $value->Cost;
                }
            }
            else
            {
                $data['Expenses'] = 0;
            }
            $data['Residual'] = $data['Total'] - $data['Expenses'];
            $this->load->view('Admin/ViewExpenses' , $data);
        }
    }
?>
