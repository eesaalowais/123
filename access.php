<?php
    if($this->session->userdata('username'))
    {
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
        elseif($this->session->userdata('Type') == 4)
        {
            header("Location: ../Student/Dashboard");
        }
        else
        {
            die("Access Error");
        }
    }
?>
<!DOCTYPE html>
<html lang="en" dir="rtl">
    <head>
        <meta charset="utf-8" />
        <title>Metronic | User Login 4</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/bootstrap/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/bootstrap-switch/css/bootstrap-switch-rtl.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/css/components-rtl.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../Resource/assets/global/css/plugins-rtl.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/pages/css/login-4-rtl.min.css" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="favicon.ico" /> </head>
    <body class=" login">
        <div class="logo">
            <a href="index.html">
                <img src="../Resource/assets/pages/img/logo-big.png" alt="" /> </a>
        </div>
        <div class="content">
            <form name="myform" onsubmit="return validate()" action="setAccess" method="post">
                <h3 class="form-title">تسجيل الدخول</h3>
                <?php
                if(!empty($error))
                {
                ?>
                <div class="alert alert-danger">
                    <button class="close" data-close="alert"></button>
                    <?php 
                        echo $error;
                    ?>
                </div>
                <?php
                }
                ?>
                <span class="label label-danger" id="check_username"></span>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">اسم المستخدم</label>
                    <div class="input-icon">
                        <i class="fa fa-user"></i>
                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="اسم المستخدم" name="username" id="username"/> </div>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">كلمة المرور</label>
                    <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input class="form-control placeholder-no-fix" type="password" placeholder="كلمة المرور" name="password" /> </div>
                </div>
                <div class="form-actions">
                    <label class="checkbox">
                        <input type="checkbox" name="remember" value="1" /> تذكرني </label>
                        <input type="submit" name="login" id="login" class="btn green pull-right" value="تسجيل الدخول">
                </div>
            </form>
        </div>
        <script src="../Resource/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../Resource/assets/pages/scripts/login-4.min.js" type="text/javascript"></script>
        <script>
        function validate()
        {
            var mobile = document.myform.username.value;
            var check = 0;
            document.getElementById("check_username").innerHTML="";

            if (isNaN(mobile))
            {  
              document.getElementById("check_username").innerHTML="الرجاء ادخال ارقام فقط";  
              check = 1;  
            } 

            if(check == 1)
            {
                check = 0;
                return false;
            }

            return TRUE;
        }
        </script>            
    </body>
</html>