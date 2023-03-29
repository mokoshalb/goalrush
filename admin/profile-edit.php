<?php
require_once "header.php";
if(isset($_POST['profile'])){
	$valid = 1;
	if(empty($_POST['username'])) {
	    $valid = 0;
	    $error_message .= "Username can not be empty<br>";
	}
	if($valid == 1) {
    	$_SESSION['admin']['username'] = $_POST['username'];
    	// updating the database
    	$statement = $pdo->prepare("UPDATE admin SET username=? WHERE id=?");
    	$statement->execute(array($_POST['username'],$_SESSION['admin']['id']));
    	$success_message = 'Username is updated successfully.';
	}
}
if(isset($_POST['changepass'])) {
	$valid = 1;
	if(empty($_POST['password']) || empty($_POST['re_password'])) {
        $valid = 0;
        $error_message .= "Password can not be empty<br>";
    }
    if( !empty($_POST['password']) && !empty($_POST['re_password']) ) {
    	if($_POST['password'] != $_POST['re_password']) {
	    	$valid = 0;
	        $error_message .= "Passwords do not match<br>";	
    	}        
    }
    if($valid == 1) {
    	$_SESSION['admin']['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    	//updating the database
    	$statement = $pdo->prepare("UPDATE admin SET password=? WHERE id=?");
    	$statement->execute(array(password_hash($_POST['password'], PASSWORD_BCRYPT), $_SESSION['admin']['id']));
    	$success_message = 'Password is updated successfully.';
    }
}
$statement = $pdo->prepare("SELECT * FROM admin WHERE id=?");
$statement->execute(array($_SESSION['admin']['id']));
$statement->rowCount();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$username = $row['username'];
}
?>
<section class="content-header">
	<div class="content-header-left">
		<h1>Edit Profile</h1>
	</div>
</section>
<section class="content" style="min-height:auto;margin-bottom: -30px;">
	<div class="row">
		<div class="col-md-12">
			<?php if($error_message): ?>
			<div class="callout callout-danger">
			<h4>Please correct the following errors:</h4>
			<p>
			<?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
			</p>
			</div>
			<?php endif; ?>
			<?php if($success_message): ?>
			<div class="callout callout-success">
			<h4>Success:</h4>
			<p><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></p>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_1" data-toggle="tab">Update Information</a></li>
						<li><a href="#tab_2" data-toggle="tab">Update Password</a></li>
					</ul>
					<div class="tab-content">
          				<div class="tab-pane active" id="tab_1">
							<form class="form-horizontal" action="" method="post">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Username: <span>*</span></label>
										<div class="col-sm-4">
										<input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="" class="col-sm-2 control-label"></label>
										<div class="col-sm-6">
											<button type="submit" class="btn btn-success pull-left" name="profile">Save Information</button>
										</div>
									</div>
								</div>
							</div>
							</form>
          				</div>
          				<div class="tab-pane" id="tab_2">
							<form class="form-horizontal" action="" method="post">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Password: <span>*</span></label>
										<div class="col-sm-4">
											<input type="password" class="form-control" name="password">
										</div>
									</div>
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Retype Password: <span>*</span></label>
										<div class="col-sm-4">
											<input type="password" class="form-control" name="re_password">
										</div>
									</div>
							        <div class="form-group">
										<label for="" class="col-sm-2 control-label"></label>
										<div class="col-sm-6">
											<button type="submit" class="btn btn-success pull-left" name="changepass">Save Password</button>
										</div>
									</div>
								</div>
							</div>
							</form>
          				</div>
          			</div>
				</div>			
		</div>
	</div>
</section>
<?php require_once "footer.php"; ?>