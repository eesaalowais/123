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
        <link rel="shortcut icon" href="favicon.ico" /> 
    </head>
    <body class=" login">
        <div class="logo">
            <a href="index.html">
                <img src="../Resource/assets/pages/img/logo-big.png" alt="" /> </a>
        </div>
        <div class="content">
        <?php
        if(!empty($error))
        {
        ?>
        <div class="alert alert-danger">
            <button class="close" data-close="alert"></button>
            <?php 
                foreach ($error as $value)
                    echo $value."<br>";
            ?>
        </div>
        <?php
        }
        ?>
        <form role="form" action="addUser" name="register" onsubmit="return check()" method="post">
            <h3 style="margin-left: 225px; margin-top: 24px; width: 91px;">التسجيل</h3>
            <p style="width: 125px; margin-left: 189px; margin-top: 21px;"> الرجاء إدخال البيانات </p>
            
            
            <span class="label label-danger" id="check_fname"></span>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">الإسم</label>
                <div class="input-icon">
                    <i class="fa fa-font"></i>
                    <input type="text" class="form-control" placeholder="الأسم الأول" name="Fname" id="Fname" minlength="3" required>
                </div>
            </div>

            <span class="label label-danger" id="check_mname"></span>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">إسم الإب</label>
                <div class="input-icon">
                    <i class="fa fa-font"></i>
                    <input type="text" class="form-control" placeholder="اسم الأب" name="Mname" id="Mname" minlength="3" required>
                </div>
            </div>

            <span class="label label-danger" id="check_gname"></span>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">إسم الجد</label>
                <div class="input-icon">
                    <i class="fa fa-font"></i>
                    <input type="text" class="form-control"  placeholder="اسم الجد" name="Gname" id="Gname" minlength="3" required>
                </div>
            </div>

            <span class="label label-danger" id="check_lname"></span>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">إلعائلة</label>
                <div class="input-icon">
                    <i class="fa fa-font"></i>
                    <input type="text" class="form-control" placeholder="إلعائلة" name="Lname" id="Lname" minlength="3" required>
                </div>
            </div>

            <span class="label label-danger" ></span>    
            <div class="form-group">
                <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                <label class="control-label visible-ie8 visible-ie9">البريد الإلكتروني</label>
                <div class="input-icon">
                    <i class="fa fa-envelope"></i>
                    <input type="email" class="form-control" placeholder="البريد الإلكتروني"  name="Email" id="Email" required>
                </div>
            </div>

            <span class="label label-danger" ></span> 
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">كلمة المرور</label>
                <div class="input-icon">
                    <i class="fa fa-lock"></i>
                    <input type="password" class="form-control" placeholder="كلمة المرور"  name="Password" id="Password" minlength="6" required="">
                </div>
            </div>

            <span class="label label-danger" ></span> 
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">تأكيد كلمة المرور</label>
                <div class="input-icon">
                    <i class="fa fa-lock"></i>
                    <input type="password" class="form-control" placeholder="تأكيد كلمة المرور" name="cpassword" id="cpassword" minlength="6" required>
                </div>
            </div>

            <span class="label label-danger" id="check_mobile"></span>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">الجوال</label>
                <div class="input-icon">
                    <i class="fa fa-check"></i>
                    <input type="text" class="form-control" placeholder="الجوال" name="Mobile" id="Mobile" minlength="10" maxlength="10" required>
                </div>
            </div>

            <span class="label label-danger" ></span>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">العمر</label>
                <div class="input-icon">
                    <i class="fa fa-check"></i>
                    <input type="number" class="form-control" placeholder="العمر" name="Age" id="Age" min="5" max="60" required>
                </div>
            </div>

            <span class="label label-danger" id="check_id"></span>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">رقم الهوية الوطنية/الإقامة</label>
                <div class="input-icon">
                    <i class="fa fa-check"></i>
                    <input type="text" class="form-control" placeholder="رقم الهوية الوطنية/الإقامة" name="National_Id" id="National_Id" minlength="10" maxlength="10" required>
                </div>
            </div>

            <span class="label label-danger" ></span>
            <div class="form-group">
                <select class="form-control"  name="Nationality" onChange="dis_able()">
                    <option value="">اختار</option>
                    <?php
                    foreach ($Nationality as $value)
                    {
                    ?>
                    <option value="<?php echo $value->Id; ?>"><?php echo $value->Name; ?></option>
                    <?php
                    }
                    ?>
                    <option value="Others">أخرى</option>
                </select>
                <br>
                <input disabled type="text" placeholder="أخرى" class="form-control" name="otherz" id="otherz" minlength="3" required>
                <div class="form-control-focus"> </div>
            </div>

            <div class="form-actions">
                <button type="submit" id="register-submit-btn" class="btn green pull-right"> تسجيل </button>
            </div>
        </form>
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
        var password = document.getElementById("Password");
        var confirm_password = document.getElementById("cpassword");

        function validatePassword()
        {
          if(password.value != confirm_password.value) {
            confirm_password.setCustomValidity("كلمة المرور غير متطابقة");
          } else {
            confirm_password.setCustomValidity('');
          }
        }

        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;

        function check()
        {
            var arabic = /^[\u0600-\u06FF-\s]+$/;
            var Fname = document.register.Fname.value;
            var Mname = document.register.Mname.value;
            var Gname = document.register.Gname.value;
            var Lname = document.register.Lname.value;
            var National_id = document.register.National_Id.value;
            var Mobile = document.register.Mobile.value;
            var check = 0;
            document.getElementById("check_id").innerHTML=""; 
            document.getElementById("check_mobile").innerHTML=""; 
            document.getElementById("check_fname").innerHTML=""; 
            document.getElementById("check_mname").innerHTML=""; 
            document.getElementById("check_gname").innerHTML="";
            document.getElementById("check_lname").innerHTML=""; 

            if (isNaN(National_id))
            {  
              document.getElementById("check_id").innerHTML="القيمة ليست رقمية"; 
              check = 1;  
            }

            if (isNaN(Mobile))
            {  
              document.getElementById("check_mobile").innerHTML="القيمة ليست رقمية";  
              check = 1;  
            }

            if(!Fname.match(arabic)) 
            { 
                document.getElementById("check_fname").innerHTML="الرجاء إدخال حروف عربية";
                check = 1; 
            }

            if(!Mname.match(arabic)) 
            { 
                document.getElementById("check_mname").innerHTML="الرجاء إدخال حروف عربية";
                check = 1; 
            }
            
            if(!Gname.match(arabic)) 
            { 
                document.getElementById("check_gname").innerHTML="الرجاء إدخال حروف عربية";
                check = 1; 
            }

            if(!Lname.match(arabic)) 
            { 
                document.getElementById("check_lname").innerHTML="الرجاء إدخال حروف عربية";
                check = 1; 
            }

            if(check == 1)
            {
                check = 0;
                return false;
            }

            return TRUE;
        }
        
        function dis_able()
        {
            if(document.register.Nationality.value != 'Others')
                document.register.otherz.disabled=1;
            else
                document.register.otherz.disabled=0;
        }

    </script>           
    </body>
</html>


