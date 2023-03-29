<?php
ob_start();
session_start();
include "../config.php";
$error_message = "";

if(isset($_POST['login'])) {
    if(empty($_POST['username']) || empty($_POST['password'])) {
        $error_message = 'Username and/or Password can not be empty<br>';
    }else{
    	$statement = $pdo->prepare("SELECT * FROM admin WHERE username=?");
    	$statement->execute(array($_POST['username']));
    	$total = $statement->rowCount();    
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);    
        if($total==0) {
            $error_message .= 'Username does not exist!<br>';
        } else {       
            foreach($result as $row) { 
                $row_password = $row['password'];
            }
            if(password_verify($_POST['password'], $row_password)){
                $_SESSION['admin'] = $row;
                header("location: index.php");
            }else{
                $error_message .= 'Password does not match<br>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Goal Rush - Admin Login</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/AdminLTE.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body class="hold-transition login-page sidebar-mini">
<div class="login-box">
	<div class="login-logo">
		<b>Goal Rush</b><br> (Admin Panel)
	</div>
  	<div class="login-box-body">
    	<p class="login-box-msg">Log in to start your session</p>
	    <?php 
	    if((isset($error_message)) && ($error_message!='')):
	        echo '<div class="error">'.$error_message.'</div>';
	    endif;
	    ?>
		<form action="" method="post">
			<div class="form-group has-feedback">
				<input class="form-control" placeholder="Username" name="username" type="username" autocomplete="off" autofocus>
			</div>
			<div class="form-group has-feedback">
				<input class="form-control" placeholder="Password" name="password" type="password" autocomplete="off" value="">
			</div>
			<div class="row">
				<div class="col-xs-8"></div>
				<div class="col-xs-4">
					<input type="submit" class="btn btn-primary btn-block btn-flat login-button" name="login" value="Log In">
				</div>
			</div>
		</form>
	</div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/app.min.js"></script>
</body>
</html>