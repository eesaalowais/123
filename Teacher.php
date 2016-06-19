<?php
    class Teacher extends CI_Controller {
        public function index()
	{
            header("Location: Teacher/Dashboard");
	}
        
        public function Dashboard()
        {
            $this->load->model('Semester');
            $this->load->model('Halqa');
            $dashboard['error'] = NULL;
            $dashboard['errorA'] = NULL;
            $data['Semester'] = $this->Semester->LastSemester();
            if(@count($data['Semester']) > 0)
            {
                $End =  $data['Semester'][0]->End;
                $Now = date_create()->format('Y-m-d');
                $datetime1 = new DateTime($End);
                $datetime2 = new DateTime($Now);
                if($datetime1 >= $datetime2)
                {
                    $this->load->model('Halqa');
                    $data['Halqa'] = $this->Halqa->getByTeacher($this->session->userdata('Id'));
                    if(@count($data['Halqa']) > 0)
                    {
                        $this->load->model('Level');
                        $data['Level'] = $this->Level->getById($data['Halqa'][0]->Level);
                        $day = date('l', strtotime($Now));
                        if($data['Level'][0]->$day == 1)
                        {
                            
                        }
                        $this->load->model('Course');
                        $query = "Semester = ".$data['Semester'][0]->Id." AND Halqa = ".$data['Halqa'][0]->Id;
                        $data['Course'] = $this->Course->getWhere($query);
                        if(@count($data['Course']) > 0)
                        {
                            $dashboard['TotalStudent'] = @count($data['Course']);
                            
                            $this->load->model('Level');
                            $info['Level'] = $this->Level->getById($data['Halqa'][0]->Level);
                            $day = date('l', strtotime($Now));
                            if($info['Level'][0]->$day == 1)
                            {
                                $this->load->model('Attendance');
                                $Present = 0;
                                $Late = 0;
                                $Absent = 0;
                                foreach ($data['Course'] as $value)
                                {
                                    $query = "User_Id = $value->Student "." AND Date = "."'".$Now."'";
                                    $check_student_status = $this->Attendance->getWhere($query);
                                    if(@count($check_student_status) > 0)
                                    {
                                        if($check_student_status[0]->Status = 1)
                                        {
                                            $Present = $Present+1;
                                        }
                                        elseif($check_student_status[0]->Status = 2)
                                        {
                                            $Late = $Late+1;
                                        }
                                        else
                                        {
                                            $Absent = $Absent+1;
                                        }
                                    }
                                    else
                                    {
                                        $Present = $Present+1;
                                    }
                                }
                                $dashboard['Present'] = $Present/$dashboard['TotalStudent'] *100;
                                $dashboard['Late'] = $Late/$dashboard['TotalStudent'] *100;
                                $dashboard['Absent'] = $Absent/$dashboard['TotalStudent'] *100; 
                            }
                            else
                            {
                                $dashboard['errorA'] = "لايوجد دوام لهذا اليوم في هذه الحلقة";
                            }
                            
                        }
                        else
                        {
                            $dashboard['error'] = "لايوجد طلاب مسجلين في الفصل الحالي";
                        }
                    }
                    else
                    {
                        $dashboard['error'] = "لاتوجد حلقة معلما لها"; 
                    }
                }
                else
                {
                    $dashboard['error'] = "لايوجد فصل دراسي حالي";
                }
            }
            else
            {
                $dashboard['error'] = "لاتوجد فصول دراسية مضافة";
            }
            $this->load->view('Teacher/Dashboard' , $dashboard);
        }
        
        public function Logout()
        {
            $this->session->set_userdata('Id');
            $this->session->set_userdata('Full_Name');
            $this->session->set_userdata('Type');
            $this->session->set_userdata('username');
            header("Location: ../");
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
                    $this->load->model('Halqa');
                    $query = "Teacher = ".$this->session->userdata('Id');
                    $Halqa = $this->Halqa->getHalqaWhere($query);
                    if(@count($Halqa) > 0)
                    {
                        $this->load->model('Course');
                        $query = "";
                        for($i = 0 ; $i < @count($Halqa) ; $i++)
                        {
                            if($i != @count($Halqa)-1)
                                $query = $query." Halqa = ".$Halqa[$i]->Id." OR ";
                            else
                                $query = $query." Halqa = ".$Halqa[$i]->Id;
                        }
                        $query = "Semester = ".$data['Semester'][0]->Id." AND ".$query;
                        $info = $this->Course->getWhere($query);
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
                        $information['error'] = 'لايوجد حلقة معلما عليها';
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
            
            $this->load->view('Teacher/StudentM' , $information);
        }
        
        public function Attendance()
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
                    $this->load->model('Halqa');
                    $query = "Teacher = ".$this->session->userdata('Id');
                    $Halqa = $this->Halqa->getHalqaWhere($query);
                    if(@count($Halqa) > 0)
                    {
                        $this->load->model('Level');
                        $query = "Id = ".$Halqa[0]->Level;
                        $Level = $this->Level->getLevelWhere($query);
                        $date = date_create()->format('Y-m-d');
                        $day = date('l', strtotime($date));
                        if($Level[0]->$day ==1)                        
                        {
                            $this->load->model('Course');
                            $query = "";
                            for($i = 0 ; $i < @count($Halqa) ; $i++)
                            {
                                if($i != @count($Halqa)-1)
                                    $query = $query." Halqa = ".$Halqa[$i]->Id." OR ";
                                else
                                    $query = $query." Halqa = ".$Halqa[$i]->Id;
                            }
                            $query = "Semester = ".$data['Semester'][0]->Id." AND ".$query;
                            $info = $this->Course->getWhere($query);
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

                                $this->load->model('Attendance');
                                $information['None'] = NULL;
                                $information['Present'] = NULL;
                                $information['Absent'] = NULL;
                                $information['Late'] = NULL;
                                foreach ($information['Student'] as $value)
                                {
                                    $now = date_create()->format('Y-m-d');
                                    $now = "'".$now."'";
                                    $query = " User_Id = ".$value->Id." AND Date = ".$now;
                                    $queryT = "Id = ".$value->Id;
                                    $Student_name = $this->User->getUserWhere($queryT);
                                    $Student_name = $Student_name[0];
                                    $result = $this->Attendance->getWhere($query);
                                    if(@count($result) > 0)
                                    {
                                        if($result[0]->Status == 1)
                                            $information['Present'][@count ($information['Present'])] = $Student_name;
                                        elseif($result[0]->Status == 2)
                                            $information['Late'][@count ($information['Late'])] = $Student_name;
                                        else
                                            $information['Absent'][@count ($information['Absent'])] = $Student_name;
                                    }
                                    else
                                    {
                                        $information['None'][@count ($information['None'])] = $Student_name;
                                    }
                                }
                            }
                            else
                            {

                                $information['error'] = 'لايوجد طلاب مسجلين في الفصل الدراسي الحالي';
                            }
                        }
                        else
                        {
                            $information['error'] = 'لايوجد اليوم دراسة';
                        }
                    }
                    else
                    {
                        $information['error'] = 'لايوجد حلقة معلما عليها';
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
            $this->load->view('Teacher/Attendance' , $information);
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
                header("Location: Attendance");
            }
            
            if(isset($Late))
            {
                $data['User_Id'] = $Late;
                $data['Status'] = 2;
                $data['Date'] = date_create()->format('Y-m-d');
                $this->Attendance->InsertAttendance($data);
                header("Location: Attendance");
            }
            
            if(isset($Present))
            {
                $data['User_Id'] = $Present;
                $data['Status'] = 1;
                $data['Date'] = date_create()->format('Y-m-d');
                $this->Attendance->InsertAttendance($data);
                header("Location: Attendance");
            }
        }
        
        public function Grade()
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
                    $this->load->model('Halqa');
                    $query = "Teacher = ".$this->session->userdata('Id');
                    $Halqa = $this->Halqa->getHalqaWhere($query);
                    if(@count($Halqa) > 0)
                    {
                        $this->load->model('Level');
                        $query = "Id = ".$Halqa[0]->Level;
                        $Level = $this->Level->getLevelWhere($query);
                        $date = date_create()->format('Y-m-d');
                        $day = date('l', strtotime($date));
                        if($Level[0]->$day == 1)                        
                        {
                            $this->load->model('Course');
                            $query = "";
                            for($i = 0 ; $i < @count($Halqa) ; $i++)
                            {
                                if($i != @count($Halqa)-1)
                                    $query = $query." Halqa = ".$Halqa[$i]->Id." OR ";
                                else
                                    $query = $query." Halqa = ".$Halqa[$i]->Id;
                            }
                            $query = "Semester = ".$data['Semester'][0]->Id." AND ".$query;
                            $info = $this->Course->getWhere($query);
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

                                $this->load->model('Save');
                                $this->load->model('Review');
                                $information['SandR'] = NULL;
                                $information['S'] = NULL;
                                $information['R'] = NULL;
                                $information['None'] = NULL;
                                $information['Absent'] = NULL;
                                foreach ($information['Student'] as $value)
                                {
                                    $now = date_create()->format('Y-m-d');
                                    $now = "'".$now."'";
                                    $query = " User_Id = ".$value->Id." AND Date = ".$now;
                                    $queryT = "Id = ".$value->Id;
                                    $Student_name = $this->User->getUserWhere($queryT);
                                    $Student_name = $Student_name[0];
                                    $this->load->model('Attendance');
                                    $query = " User_Id = ".$value->Id." AND Date = ".$now;
                                    $check = $this->Attendance->getWhere($query);
                                    if(@count($check) == 0 || $check[0]->Status == 1 || $check[0]->Status == 2)
                                    {
                                        $Save = $this->Save->getWhere($query);
                                        $Review = $this->Review->getWhere($query);
                                        if(@count($Save) > 0 AND @count($Review) > 0)
                                        {
                                            $information['SandR'][@count($information['SandR'])] = $Student_name;
                                        }
                                        elseif(@count($Save) > 0 AND @count($Review) == 0)
                                        {
                                            $information['S'][@count($information['S'])] = $Student_name;
                                        }
                                        elseif(@count($Review) > 0 AND @count($Save) == 0)
                                        {
                                            $information['R'][@count($information['R'])] =  $Student_name;
                                        }
                                        else
                                        {
                                            $information['None'][@count($information['None'])] = $Student_name;
                                        }
                                    }
                                    else
                                    {
                                        $information['Absent'][@count($information['Absent'])] = $Student_name;
                                    }
                                    
                                }
                                $information['Semester'] = $data['Semester'][0];
                            }
                            else
                            {

                                $information['error'] = 'لايوجد طلاب مسجلين في الفصل الدراسي الحالي';
                            }
                        }
                        else
                        {
                            $information['error'] = 'لايوجد اليوم دراسة';
                        }
                    }
                    else
                    {
                        $information['error'] = 'لايوجد حلقة معلما عليها';
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
            $this->load->view('Teacher/Grade' , $information);
        }
        
        public function setSave()
        {
            $info = $this->input->post('InsertSave');
            $info = explode("," , $info);
            $data['User_Id'] = $info[0];
            $data['Name'] = $this->input->post('Name');
            $data['From'] = $this->input->post('From');
            $data['To'] = $this->input->post('To');
            $data['Etqan'] = $this->input->post('Etqan');
            $data['Row'] = $this->input->post('Rows');
            $data['Course'] = $info[1];
            $data['Date'] = date_create()->format('Y-m-d');
            $query = "Student = ".$data['User_Id']." AND Semester = ".$data['Course'];
            $this->load->model('Course');
            $info['Course'] = $this->Course->getWhere($query);
            $this->load->model('Halqa');
            $query = "Id = ".$info['Course'][0]->Halqa;
            $info['Halqa'] = $this->Halqa->getHalqaWhere($query);
            
            if($data['Row'] > $info['Halqa'][0]->Save)
                $data['Row'] = 5;
            else
                $data['Row'] = $data['Row'] / $info['Halqa'][0]->Save * 5;
            $data['Total'] = $data['Row'] + $data['Etqan'];
            $this->load->model('Save');
            $this->Save->InsertSave($data);
            header("Location: Grade");
        }
        
        public function setReview()
        {
            $info = $this->input->post('InsertReview');
            $info = explode("," , $info);
            $data['User_Id'] = $info[0];
            $data['Name'] = $this->input->post('Name');
            $data['From'] = $this->input->post('From');
            $data['To'] = $this->input->post('To');
            $data['Grade'] = $this->input->post('Etqan');
            $data['Semester'] = $info[1];
            $data['Date'] = date_create()->format('Y-m-d');
            $this->load->model('Review');
            $this->Review->InsertReview($data);
            header("Location: Grade");
        }

        public function Save()
        {
            $this->load->view('Teacher/Save');
        }
        
        public function Review()
        {
            $this->load->view('Teacher/Review');
        }
        
        public function ViewStudent()
        {
            $id = $this->input->post_get('id');
            $information['error'] = NULL;
            $this->load->model('Course');
            $data['Course'] = $this->Course->LastCourse($id);
            if(@count($data['Course']) > 0)
            {
                $this->load->model('Semester');
                $query = "Id = ".$data['Course'][0]->Semester;
                $data['Semester'] = $this->Semester->getSemesterWhere($query);
                $End =  $data['Semester'][0]->End;
                $Start = $data['Semester'][0]->Start;
                $Now = date_create()->format('Y-m-d');
                if(strtotime($Now) <= strtotime($End))
                    $End = $Now;
                $information['Date'] = NULL;
                $information['Status'] = NULL;
                $information['Save'] = NULL;
                $information['Review'] = NULL;
                $this->load->model('Attendance');
                $this->load->model('Level');
                $this->load->model('Save');
                $this->load->model('Review');
                $query = "Id = ".$data['Course'][0]->Level;
                $data['Level'] = $this->Level->getLevelWhere($query);
                while (strtotime($Start) <= strtotime($End))
                {
                    $d = "'".$Start."'";
                    $query = "User_Id = ".$id." AND Date = ".$d;
                    $day = date('l', strtotime($Start));
                    if($data['Level'][0]->$day == 1)
                    {
                        $att = $this->Attendance->getWhere($query);
                        if(@count($att) > 0)
                        {
                            $information['Date'][@count($information['Date'])] = $Start;
                            $information['Status'][@count($information['Status'])] = $att[0]->Status;
                        }
                        else
                        {
                            $information['Date'][@count($information['Date'])] = $Start;
                            $information['Status'][@count($information['Status'])] = "لم يتعين";
                        }
                        
                        $Save  = $this->Save->getWhere($query);
                        if(@count($Save) > 0)
                        {
                            $information['Save'][@count($information['Save'])] = $Save[0];
                        }
                        else
                        {
                            $information['Save'][@count($information['Save'])] = "لم ترصد الدرجة";
                        }
                        
                        $Review  = $this->Review->getWhere($query);
                        if(@count($Review) > 0)
                        {
                            $information['Review'][@count($information['Review'])] = $Review[0]->Grade;
                        }
                        else
                        {
                            $information['Review'][@count($information['Review'])] = "لم ترصد الدرجة";
                        }
                    }
                    $Start = date("Y-m-d", strtotime("+1 day", strtotime($Start)));
                }
            }
            else
            {
                $information['error'] = "الطالب لم يسجل في اي مستوى دراسي";
            }
            
            //print_r($information['Save']);
            //die();
            $this->load->view("Teacher/ViewStudent" , $information);
        }
        
        public function newAlert()
        {
            $this->load->view('Teacher/newAlert');
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
            $this->load->model('Halqa');
            $query1 = "Teacher = ".$this->session->userdata('Id');
            $data['Halqa'] = $this->Halqa->getHalqaWhere($query1);
            if(@count($data['Halqa']) > 0)
            {
                $this->load->model('Level');
                $query1 = "Id = ".$data['Halqa'][0]->Level;
                $data['Level'] = $this->Level->getLevelWhere($query1);
                $query = $query." OR From = ".$data['Level'][0]->Supervisor;
            }
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
            $this->load->view('Teacher/AlertM' , $data);
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
            $this->load->view('Teacher/ViewAlert' , $data);
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
            $this->load->view('Teacher/MyAlert' , $data);
        }
        
        public function UpdateSave()
        {
            $this->load->model('Save');
            $query = "User_Id = ".$this->input->post_get('id')." AND Course = ".$this->input->post_get('S')." AND Date = '".date_create()->format('Y-m-d')."'";
            $data['Save'] = $this->Save->getWhere($query);
            $this->load->view('Teacher/UpdateSave' , $data);
        }
        
        public function setUpdateSave()
        {
            header("Location: Grade");
        }

        public function UpdateReview()
        {
            $this->load->model('Review');
            $query = "User_Id = ".$this->input->post_get('id')." AND Semester = ".$this->input->post_get('S')." AND Date = '".date_create()->format('Y-m-d')."'";
            $data['Review'] = $this->Review->getWhere($query);
            $this->load->view('Teacher/UpdateReview' , $data);
        }
        
        public function setUpdateReview()
        {
            header("Location: Grade");
        }
    }
?>