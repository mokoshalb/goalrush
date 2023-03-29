<?php
require_once "header.php";
if(isset($_POST['goal'])) {
	$valid = 1;
	if(empty($_POST['title'])) {
		$valid = 0;
		$error_message .= 'Goal title can not be empty<br>';
	}
	if(empty($_POST['url'])) {
		$valid = 0;
		$error_message .= 'You must set a URL<br>';
	}
	if($valid == 1){
	    $statement = $pdo->prepare("UPDATE goals SET gr_title=?, gr_url=? WHERE id=?");
	    $statement->execute(array($_POST['title'], $_POST['url'], $_REQUEST['id']));
	    $success_message = 'Goal is updated successfully!';
	}
}
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM goals WHERE id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	if($total == 0){
		header('location: logout.php');
		exit;
	}
}
foreach ($result as $row) {
	$title = $row['gr_title'];
	$url = $row['gr_url'];
}
?>
<section class="content-header">
	<div class="content-header-left">
		<h1>Edit Goal</h1>
	</div>
	<div class="content-header-right">
		<a href="goal.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>
<section class="content">
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
			<form class="form-horizontal" action="" method="post">
				<div class="box box-info">
					<div class="box-body">
					    <div class="form-group">
							<label for="" class="col-sm-2 control-label">Goal Title <span>*</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
							</div>
						</div>
				        <div class="form-group">
							<label for="" class="col-sm-2 control-label">Goal URL <span>*</span></label>
							<div class="col-sm-6">
								<input type="url" class="form-control" name="url" value="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="goal">Save</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
<?php require_once "footer.php"; ?>