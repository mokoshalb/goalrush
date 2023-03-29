<?php
require_once "header.php";
if(isset($_POST['ads'])) {
    // saving into the database
    $statement = $pdo->prepare("UPDATE advertisement SET code=? WHERE id=1");
    $statement->execute(array($_POST['code']));
    $success_message = 'Advertisement is saved successfully.';
}
$statement = $pdo->prepare("SELECT * FROM advertisement WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $code = $row['code'];
}
?>
<section class="content-header">
	<div class="content-header-left">
		<h1>Advertisement</h1>
	</div>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php if(isset($_POST['ads'])): ?>
			<div class="callout callout-success">
			<h4>Success:</h4>
			<p><?php echo $success_message; ?></p>
			</div>
			<?php endif; ?>
			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
				<div class="box box-info">
					<div class="box-body">
						 <div class="form-group">
							<label for="" class="col-sm-2 control-label">Ads Code: </label>
							<div class="col-sm-9">
						        <textarea class="form-control" name="code" style="height:200px;"><?php echo $code; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="ads">Save</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
<?php require_once "footer.php"; ?>