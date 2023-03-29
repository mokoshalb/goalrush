<?php
require_once "config.php";
//get title, description from DB
$statement = $pdo->prepare("SELECT * FROM settings WHERE id=1");
$statement->execute();
$result1 = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result1 as $row) {
	$title = $row['title'];
	$description = $row['description'];
}
if(isset($_REQUEST["id"])){
    $hash = $_REQUEST["id"];
    $statement = $pdo->prepare("SELECT * FROM goals WHERE hash = ?");
    $statement->execute(array($hash));
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $total = $statement->rowCount();
	if($total > 0){
		foreach($result as $row){
		    $link = $row['video'];
		    $thumb = $row['thumbnail'];
		    $dltitle = $row['title'];
		}
	    $statement = $pdo->prepare("UPDATE goals SET views = views + ? WHERE hash = ?");
        $statement->execute(array(1, $hash));
	}else{
	    header("Location: /");
        exit;
	}
}else{
    header("Location: /");
    exit;
}
$quotes = json_decode(file_get_contents("quotes.json"), true);
$quote = $quotes[mt_rand(0,317)];
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="<?php echo $description; ?>">
      <meta name="author" content="NodeTent">
      <meta name="description" content="<?php echo $description; ?>">
      <meta property="og:title" content="Download <?php echo $dltitle." - ".$title; ?>">
      <meta property="og:type" content="video">
      <meta property="og:url" content="<?php echo BASE_URL.'download/'.$hash; ?>">
      <meta property="og:description" content="<?php echo $description; ?>">
      <meta property="og:image" content="<?php echo $thumb; ?>">
      <meta name="referrer" content="no-referrer">
      <title><?php echo "Download ".$dltitle." - ".$title; ?></title>
      <!-- Favicon Icon -->
      <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>public/img/favicon.png">
      <link href="<?php echo BASE_URL; ?>public/css/bootstrap.min.css" rel="stylesheet">
      <link href="<?php echo BASE_URL; ?>public/css/fontawesome.min.css" rel="stylesheet" type="text/css">
      <link href="<?php echo BASE_URL; ?>public/css/style.css" rel="stylesheet">
   </head>
   <body id="page-top">
      <nav class="navbar navbar-expand navbar-light bg-white static-top osahan-nav sticky-top">
         &nbsp;&nbsp;&nbsp;&nbsp;
         <a class="navbar-brand mr-1" href="<?php echo BASE_URL; ?>"><img class="img-fluid" alt="" src="<?php echo BASE_URL; ?>public/img/logo.png"></a>
         <!-- Navbar Search -->
         <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-5 my-2 my-md-0 osahan-navbar-search" action="search.php" method="post">
            <div class="input-group">
               <input type="text" class="form-control" name="search" placeholder="Search for...">
               <div class="input-group-append">
                  <button class="btn btn-light" type="submit">
                  <i class="fas fa-search"></i> 
                  </button>
               </div>
            </div>
         </form>
         <ul class="navbar-nav ml-auto ml-md-0 osahan-right-navbar"></ul>
         <a href="mailto:info@goalrush.xyz" class="btn btn-link text-secondary order-1 order-sm-0"><i class="fas fa-envelope fa-fw"></i></a>
      </nav>
      <div id="wrapper">
         <div class="single-channel-page" id="content-wrapper">
            <div class="container-fluid">
                <div class="alert alert-primary" role="alert"><?=$quote?></div>
               <div class="video-block section-padding">
                  <div class="row">
                     <?php if($showSearch){?>
                     <div class="col-md-12">
                         <form style="margin: 0 auto;width:100%;" class="d-md-inline-block form-inline ml-auto mr-0 mr-md-5 my-2 my-md-0 osahan-navbar-search" action="search.php" method="post">
                            <div class="input-group">
                               <input type="text" class="form-control" name="search" placeholder="Search for...">
                               <div class="input-group-append">
                                  <button class="btn btn-light" type="submit">
                                  <i class="fas fa-search"></i> 
                                  </button>
                               </div>
                            </div>
                         </form>
                     </div>
                    <?php }?>
                    <div class="col-md-8 mx-auto text-center pt-4 pb-5">
                        <div><?php if($mobile){echo $mobilecode."<br>";}?></div>
                         <h1><img alt="404" src="<?php echo $thumb; ?>" alt="" onerror="this.onerror=null;this.src='<?php echo BASE_URL; ?>public/img/null.png';" class="img-fluid"></h1>
                         <h4><?php echo "Download ".$dltitle; ?></h4>
                         <div class="mt-3">
                            <a class="btn btn-outline-primary" download href="/dl/<?php echo base64_encode($link); ?>/<?php echo base64_encode($dltitle); ?>"><i class="fa fa-download"></i> Download</a>
                            <a class="btn btn-outline-secondary" href="<?php echo BASE_URL.$hash; ?>"><i class="fa fa-video"></i> Watch</a>
                         </div>
                         <br><br>
                        <div class="single-video-info-content box mb-3">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- /.container-fluid -->
            <!-- Sticky Footer -->
            <footer class="sticky-footer ml-0">
               <div class="container">
                  <div class="row no-gutters">
                     <div class="col-lg-12 col-sm-12">
                        <center><p class="mt-1 mb-0">&copy; <?=date("Y")?>. Goal Rush<br></p></center>
                     </div>
                  </div>
               </div>
            </footer>
         </div>
         <!-- /.content-wrapper -->
      </div>
      <!-- /#wrapper -->
      <!-- Scroll to Top Button-->
      <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
      </a>
      <script src="<?php echo BASE_URL; ?>public/js/jquery.min.js"></script>
      <script src="<?php echo BASE_URL; ?>public/js/bootstrap.min.js"></script>
      <script src="<?php echo BASE_URL; ?>public/js/main.js"></script>
   </body>
</html>