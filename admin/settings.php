<?php
require_once "header.php";
if(isset($_POST['seo'])) {
	// updating the database
	$statement = $pdo->prepare("UPDATE settings SET title=?, description=? WHERE id=1");
	$statement->execute(array($_POST['title'], $_POST['description']));
	$success_message = 'SEO settings is updated successfully.';
}
if(isset($_POST['favicon'])) {
	$valid = 1;
	$path = $_FILES['photo_favicon']['name'];
    $path_tmp = $_FILES['photo_favicon']['tmp_name'];
    if($path == '') {
    	$valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    } else {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }
    if($valid == 1) {
    		unlink('../assets/images/favicon.png');
    		move_uploaded_file($path_tmp, '../assets/images/favicon.png');
    		$success_message = 'Favicon is updated successfully.';
    }
}
$statement = $pdo->prepare("SELECT * FROM settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$title = $row['title'];
	$description = $row['description'];
}
?>
<section class="content-header">
	<div class="content-header-left">
		<h1>Settings</h1>
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
						<li class="active"><a href="#tab_1" data-toggle="tab">SEO</a></li>
						<li><a href="#tab_2" data-toggle="tab">Favicon</a></li>
					</ul>
					<div class="tab-content">
					    <div class="tab-pane active" id="tab_1">
          					<form class="form-horizontal" action="" method="post">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Title: *</label>
										<div class="col-sm-9">
											<input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Description: </label>
										<div class="col-sm-9">
											<textarea class="form-control" name="description" style="height:200px;"><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
										</div>
									</div>	
									<div class="form-group">
										<label for="" class="col-sm-2 control-label"></label>
										<div class="col-sm-6">
											<button type="submit" class="btn btn-success pull-left" name="seo">Save</button>
										</div>
									</div>
								</div>
							</div>
							</form>
          				</div>
          				<div class="tab-pane" id="tab_2">
          					<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
							            <label for="" class="col-sm-2 control-label">Existing Photo</label>
							            <div class="col-sm-6" style="padding-top:6px;">
							                <img src="../assets/images/favicon.png" class="existing-photo" style="height:40px;">
							            </div>
							        </div>
									<div class="form-group">
							            <label for="" class="col-sm-2 control-label">New Photo</label>
							            <div class="col-sm-6" style="padding-top:6px;">
							                <input type="file" name="photo_favicon">
							            </div>
							        </div>
							        <div class="form-group">
										<label for="" class="col-sm-2 control-label"></label>
										<div class="col-sm-6">
											<button type="submit" class="btn btn-success pull-left" name="favicon">Update Favicon</button>
										</div>
									</div>
								</div>
							</div>
							</form>
          				</div>
          			</div>
				</div>
			</form>
		</div>
	</div>
</section>
<?php require_once "footer.php"; ?>