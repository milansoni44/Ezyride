<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="keywords" content="admin, dashboard, bootstrap, template, flat, modern, theme, responsive, fluid, retina, backend, html5, css, css3">
        <meta name="description" content="">
        <meta name="author" content="ThemeBucket">
        <link rel="shortcut icon" href="#" type="image/png">

        <title><?php echo $title; ?></title>

        <!--icheck-->
        <link href="<?php echo base_url(); ?>assets/js/iCheck/skins/minimal/minimal.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/js/iCheck/skins/square/square.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/js/iCheck/skins/square/red.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/js/iCheck/skins/square/blue.css" rel="stylesheet">

        <!--dashboard calendar-->
        <link href="<?php echo base_url(); ?>assets/css/clndr.css" rel="stylesheet">

        <!--dynamic table-->
        <link href="<?php echo base_url(); ?>assets/js/advanced-datatable/css/demo_page.css" rel="stylesheet" />
        <link href="<?php echo base_url(); ?>assets/js/advanced-datatable/css/demo_table.css" rel="stylesheet" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/data-tables/DT_bootstrap.css" />

        <!--common-->
        <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/css/style-responsive.css" rel="stylesheet">




        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="sticky-header">

        <section>
            <!-- left side start-->
            <div class="left-side sticky-left-side">

                <!--logo and iconic logo start-->
                <div class="logo">
                    <a href="<?php echo base_url('home'); ?>"><img src="<?php echo base_url(); ?>assets/images/logo.png" alt=""></a>
                </div>

                <div class="logo-icon text-center">
                    <a href="<?php echo base_url('home'); ?>"><img src="<?php echo base_url(); ?>assets/images/logo_icon.png" alt=""></a>
                </div>
                <!--logo and iconic logo end-->

                <div class="left-side-inner">

                    <!-- visible to small devices only -->
                    <div class="visible-xs hidden-sm hidden-md hidden-lg">
                        <div class="media logged-user">
                            <img alt="" src="<?php echo base_url(); ?>assets/images/photos/user-avatar.png" class="media-object">
                            <div class="media-body">
                                <h4><a href="#">
                                        <?php
//                                        $admin_name = $this->ion_auth->user()->row();
//                                        echo $admin_name->first_name . ' ' . $admin_name->last_name;
                                        echo USER_NAME;
                                        ?>
                                    </a></h4>
                            </div>
                        </div>

                        <h5 class="left-nav-title">Account Information</h5>
                        <ul class="nav nav-pills nav-stacked custom-nav">
                            <li><a href="#"><i class="fa fa-user"></i> <span>Profile</span></a></li>
                            <li><a href="#"><i class="fa fa-cog"></i> <span>Settings</span></a></li>
                            <li><a href="<?php echo base_url('auth/change_password'); ?>"><i class="fa fa-cog"></i> <span>Change Password</span></a></li>
                            <li><a href="<?php echo base_url('auth/login_detail'); ?>"><i class="fa fa-cog"></i> <span>Login Detail</span></a></li>
                            <li><a href="<?php echo base_url('auth/logout'); ?>"><i class="fa fa-sign-out"></i> Log Out</a></li>
                        </ul>
                    </div>

                    <!--sidebar nav start-->
                    <ul class="nav nav-pills nav-stacked custom-nav">
                        <li class="<?php
                        if ($title == 'Admin Home') {
                            echo 'active';
                        }
                        ?>"><a href="<?php echo base_url('home'); ?>"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>

                        <li class="menu-list <?php
                        if ($title == 'Admin List' || $title == 'Create User' || $title == 'Edit User') {
                            echo 'nav-active';
                        }
                        ?>"><a href=""><i class="fa fa-laptop"></i> <span>User</span></a>
                            <ul class="sub-menu-list">
                                <li class="<?php if ($title == 'Admin List') {
                                echo 'active';
                            } ?>"><a href="<?php echo base_url('auth'); ?>"> User List</a></li>
                                <li class="<?php if ($title == 'Create User') {
                                echo 'active';
                            } ?>"><a href="<?php echo base_url('auth/create_user'); ?>"> Add New User</a></li>
                            </ul>
                        </li>
                        <li class="<?php if ($title == 'Application Users') { echo 'active'; } ?>"><a href="<?php echo base_url('customers'); ?>"> <i class="fa fa-laptop"></i>App User List</a></li>
                        <li class="<?php
                            if ($title == 'Car Details') {
                                echo 'active';
                            }
                        ?>"><a href="<?php echo base_url('car_detail/index'); ?>"><i class="fa fa-laptop"></i> <span>Car Detail</span></a></li>

                        <li class="<?php
                        if ($title == 'Rides') {
                            echo 'active';
                        }
                        ?>"><a href="<?php echo base_url('rides/index'); ?>"><i class="fa fa-laptop"></i> <span>Rides</span></a></li>

                        <li class="<?php
                        if ($title == 'Rides Detail') {
                            echo 'active';
                        }
                        ?>"><a href="<?php echo base_url('rides_detail/index'); ?>"><i class="fa fa-laptop"></i> <span>Rides Detail</span></a></li>

                    </ul>


                    <!--sidebar nav end-->

                </div>
            </div>
            <!-- left side end-->

            <!-- main content start-->
            <div class="main-content" >

                <!-- header section start-->
                <div class="header-section">

                    <!--toggle button start-->
                    <a class="toggle-btn"><i class="fa fa-bars"></i></a>
                    <!--toggle button end-->

                    <!--search start-->
                    <!-- <form class="searchform" action="index.html" method="post">
                         <input type="text" class="form-control" name="keyword" placeholder="Search here..." />
                     </form>
                    <!--search end-->

                    <!--notification menu start -->
                    <div class="menu-right">
                        <ul class="notification-menu">
                            <li>
                                <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?php echo base_url(); ?>assets/images/photos/user-avatar.png" alt="" />
                                    <?php
//                                    $admin_name = $this->ion_auth->user()->row();
//                                    echo $admin_name->first_name . ' ' . $admin_name->last_name;
                                    echo USER_NAME;
                                    ?>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                                    <li><a href="#"><i class="fa fa-user"></i>  Profile</a></li>
                                    <li><a href="#"><i class="fa fa-cog"></i>  Settings</a></li>
                                    <li><a href="<?php echo base_url('auth/change_password'); ?>"><i class="fa fa-cog"></i> <span>Change Password</span></a></li>
                                    <li><a href="<?php echo base_url('auth/login_detail'); ?>"><i class="fa fa-cog"></i> <span>Login Detail</span></a></li>
                                    <li><a href="<?php echo base_url('auth/logout'); ?>"><i class="fa fa-sign-out"></i> Log Out</a></li>
                                </ul>
                            </li>

                        </ul>
                    </div>
                    <!--notification menu end -->

                </div>
                <!-- header section end-->
