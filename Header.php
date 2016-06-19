<?php
    if(!$this->session->userdata('username'))
    {
        header("Location:Login");
        die();
    }

    if($this->session->userdata('Type') != 1)
    {
        header("Location:Login");
        die();
    }
?>
<!DOCTYPE html>
<html lang="en" dir="rtl">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>Metronic | Inbox</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/bootstrap/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/bootstrap-switch/css/bootstrap-switch-rtl.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../Resource/assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5-rtl.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../Resource/assets/global/css/components-rtl.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../Resource/assets/global/css/plugins-rtl.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="../Resource/assets/apps/css/inbox-rtl.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="../Resource/assets/layouts/layout4/css/layout-rtl.min.css" rel="stylesheet" type="text/css" />
        <link href="../Resource/assets/layouts/layout4/css/themes/light-rtl.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="../Resource/assets/layouts/layout4/css/custom-rtl.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" /> </head>
    <!-- END HEAD -->

    <body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo">
        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top">
            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner ">
                <!-- BEGIN LOGO -->
                <div class="page-logo">
                    <a href="index.html">
                        <img src="../Resource/assets/layouts/layout4/img/logo-light.png" alt="logo" class="logo-default" /> </a>
                    <div class="menu-toggler sidebar-toggler">
                        <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
                    </div>
                </div>
                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>

                <div class="page-top">
                    <div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <li class="dropdown dropdown-user dropdown-dark">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <span class="username username-hide-on-mobile"> Nick </span>
                                    <!-- DOC: Do not remove below empty space(&nbsp;) as its purposely used -->
                                    <img alt="" class="img-circle" src="../Resource/assets/layouts/layout4/img/avatar9.jpg" /> </a>
                                <ul class="dropdown-menu dropdown-menu-default">
                                    <li>
                                        <a href="page_user_profile_1.html">
                                            <i class="icon-user"></i> My Profile </a>
                                    </li>
                                    <li>
                                        <a href="app_calendar.html">
                                            <i class="icon-calendar"></i> My Calendar </a>
                                    </li>
                                    <li>
                                        <a href="app_inbox.html">
                                            <i class="icon-envelope-open"></i> My Inbox
                                            <span class="badge badge-danger"> 3 </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="app_todo_2.html">
                                            <i class="icon-rocket"></i> My Tasks
                                            <span class="badge badge-success"> 7 </span>
                                        </a>
                                    </li>
                                    <li class="divider"> </li>
                                    <li>
                                        <a href="Logout">
                                            <i class="icon-key"></i> Log Out </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- END USER LOGIN DROPDOWN -->
                            <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                            <li class="dropdown dropdown-extended quick-sidebar-toggler">
                                <span class="sr-only">Toggle Quick Sidebar</span>
                                <i class="icon-logout"></i>
                            </li>
                            <!-- END QUICK SIDEBAR TOGGLER -->
                        </ul>
                    </div>
                    <!-- END TOP NAVIGATION MENU -->
                </div>
                <!-- END PAGE TOP -->
            </div>
            <!-- END HEADER INNER -->
        </div>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            