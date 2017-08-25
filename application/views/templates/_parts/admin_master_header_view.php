<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?php echo $page_title;?></title>

<!-- jQuery UI used for DatePicker -->
<link href="<?php echo site_url('assets/js/jquery-ui-1.12.0.custom/jquery-ui.css');?>" rel="stylesheet">
<!-- BootStrap -->
<link href="<?php echo site_url('assets/css/bootstrap.min.css');?>" rel="stylesheet">
<!-- acal custom styles -->
<link href="<?php echo site_url('assets/css/acal/acal-custom.css');?>" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<?php
  // optional styles or js
  echo $before_head;
?>

</head>

<body>
<nav class="navbar navbar-inverse navbar-static-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <!-- Site title -->
      <a class="navbar-brand" href="<?php echo site_url('admin/user/profile');?>"><?php echo ucfirst($user_name); ?></a>
    </div>

    <div id="navbar" class="collapse navbar-collapse">

      <!-- PROJECT -->
      <ul class="nav navbar-nav">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <?php
              if(isset($_SESSION['selected_project_name'])){
                echo $_SESSION['selected_project_name'];
              }
              else {
                echo 'Select Project';
              }
            ?>
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            <?php
              // get projects from the database output them here
              // $menu_projects is defined in the admin controller
              if (empty($menu_projects)) {
                echo '<li><a href="javascript:;">No projects added yet</a></li>';
              }
              else {
                foreach ($menu_projects as $project) {
            ?>
                  <li>
                    <a href="<?php echo site_url('menu/set_project/'.$project->id);?>">
                      <?php echo $project->title; ?>
                    </a>
                  </li>
            <?php
                }
            }
            ?>
          </ul>
        </li>
        
        <!-- USER MENU -->
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Menu <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <?php
              echo $current_user_menu; // insert user specific menu (just admin at this time)
              // insert shortcut to calendar. Populate with session data if previously setup by user
              if( isset($_SESSION['selected_project_id']) ){
            ?>
                <li><a href="<?php echo site_url('calendar/calendar/home/'.$_SESSION['selected_project_id']); ?>">Calendar</a></li>
            <?php
              }
              else { ?>
                <li><a href="<?php echo site_url('calendar/calendar/home');?>">Calendar</a></li>
            <?php
              }
            ?>
            <li role="separator" class="divider"></li>
            <li><a href="<?php echo site_url('info');?>">Information</a></li>
            <li><a href="<?php echo site_url('admin/user/profile');?>">View my profile</a></li>
            <!--<li><a href="<?php echo site_url('admin/user/user_settings');?>">My settings</a></li>-->
            <li><a href="<?php echo site_url('admin/user/change_password');?>">Change password</a></li>
            <li><a href="<?php echo site_url('contact/contact');?>">Contact admin</a></li>
            <!-- <li><a href="<?php echo site_url('#');?>">Contact</a></li> -->
            <li role="separator" class="divider"></li>
            <li><a href="<?php echo site_url('admin/user/logout');?>">Logout</a></li>
          </ul>
        </li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li><a href="<?php echo site_url('admin/user/logout');?>">Logout</a></li>
      </ul>

    </div>
    <!--/.nav-collapse -->
  </div>
</nav>
