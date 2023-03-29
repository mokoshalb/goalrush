<?php
require_once "header.php";
$statement = $pdo->prepare("SELECT * FROM goals");
$statement->execute();
$totalGoals = $statement->rowCount();
$statement = $pdo->prepare("SELECT gr_views FROM goals");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);;
$totalViews = 0;
foreach($result as $row){
    $totalViews += $row["gr_views"];
}
?>
<section class="content-header">
  <h1>Dashboard</h1>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-red"><i class="fa fa-video-camera"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Goals</span>
          <span class="info-box-number"><?php echo htmlspecialchars($totalGoals, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-eye"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Views</span>
          <span class="info-box-number"><?php echo htmlspecialchars($totalViews, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require_once "footer.php"; ?>