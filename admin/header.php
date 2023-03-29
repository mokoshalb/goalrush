<?php
ob_start();
session_start();
include "../config.php";
$error_message = "";
$success_message = "";
// Check if the user is logged in or not
if(!isset($_SESSION['admin'])) {
	header('location: login.php');
	exit;
}
// Current Page Access Level check for all pages
$cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Admin Panel - Goal Rush</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link href="../assets/images/favicon.png" rel="icon" type="image/png">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="css/AdminLTE.min.css">
	<link rel="stylesheet" href="css/skin.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body class="hold-transition fixed skin-blue sidebar-mini">
	<div class="wrapper">
		<header class="main-header">
			<a href="index.php" class="logo">
				<span class="logo-mini">GR</span>
				<span class="logo-lg">Goal Rush</span>
			</a>
			<nav class="navbar navbar-static-top">
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="img/no-photo.jpg" class="user-image" alt="user photo">
							</a>
							<ul class="dropdown-menu">
								<li class="user-footer">
									<div>
										<a href="<?php echo BASE_URL; ?>" class="btn btn-default btn-flat">Homepage</a>
									</div>
									<div>
										<a href="profile-edit.php" class="btn btn-default btn-flat">Edit Profile</a>
									</div>
									<div>
										<a href="logout.php" class="btn btn-default btn-flat">Log out</a>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		</header>
  		<aside class="main-sidebar">
    		<section class="sidebar">
      			<ul class="sidebar-menu">
			        <li class="treeview <?php if($cur_page == 'index.php') {echo 'active';} ?>">
			          <a href="index.php">
			            <i class="fa fa-laptop"></i> <span>Dashboard</span>
			          </a>
			        </li>
				    <li class="treeview <?php if( ($cur_page == 'add.php')||($cur_page == 'goal.php')||($cur_page == 'edit.php') ) {echo 'active';} ?>">
			          <a href="goal.php">
			            <i class="fa fa-futbol-o"></i> <span>Goals</span>
			          </a>
			        </li>
			        <li class="treeview <?php if( ($cur_page == 'advertisement.php') ) {echo 'active';} ?>">
					    <a href="advertisement.php"><i class="fa fa-podcast"></i><span>Advertisement</span></a>
				    </li>				
			        <li class="treeview <?php if( ($cur_page == 'settings.php') ) {echo 'active';} ?>">
			          <a href="settings.php">
			            <i class="fa fa-cog"></i> <span>Settings</span>
			          </a>
			        </li>
      			</ul>
    		</section>
  		</aside>
  		<div class="content-wrapper">