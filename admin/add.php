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

	function generateHash($string, $len=8){
        $hex = md5($string . "NodeTent");
        $pack = pack('H*', $hex);
        $uid = base64_encode($pack);
        $uid = preg_replace('/[^a-zA-Z 0-9]+/', "", $uid);
        if($len<4){
            $len=4;
        }elseif($len>128){
            $len=128;
        }
        while (strlen($uid)<$len){
            $uid = $uid.gen_uuid(22);
        }
        return substr($uid, 0, $len);
    }
    
	if($valid == 1) {
		$statement = $pdo->prepare("INSERT INTO goals (gr_title,gr_url,gr_time,gr_hash) VALUES (?,?,?,?)");
		$statement->execute(array($_POST['title'], $_POST['url'], time(), generateHash($_POST['title'])));
		$success_message = 'Goal is added successfully!';
	}
}
?>
<section class="content-header">
	<div class="content-header-left">
		<h1>Add Goal</h1>
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
								<input type="text" class="form-control" name="title">
							</div>
						</div>
				        <div class="form-group">
							<label for="" class="col-sm-2 control-label">Goal URL <span>*</span></label>
							<div class="col-sm-6">
								<input type="url" class="form-control" name="url">
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