<?php
require_once "config.php";
if(!isset($_REQUEST['search'])){
    header("Location: /");
    exit;
}
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
$statement = $pdo->prepare("SELECT * FROM goals WHERE title LIKE ? ORDER BY id DESC");
$statement->execute(array('%'.$_REQUEST['search'].'%'));
$total = $statement->rowCount();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);	
//get title, description from DB
$statement = $pdo->prepare("SELECT * FROM settings WHERE id=1");
$statement->execute();
$result1 = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result1 as $row) {
	$title = $row['title'];
	$description = $row['description'];
}

function time2str($ts){
    if(!ctype_digit($ts)){
        $ts = strtotime($ts);
    }
    $diff = time() - $ts;
    if($diff == 0){
        return 'now';
    }elseif($diff > 0){
        $day_diff = floor($diff / 86400);
        if($day_diff == 0){
            if($diff < 60) return 'just now';
            if($diff < 120) return '1 minute ago';
            if($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if($diff < 7200) return '1 hour ago';
            if($diff < 86400) return floor($diff / 3600) . ' hours ago';
        }
        if($day_diff == 1) return 'Yesterday';
        if($day_diff < 7) return $day_diff . ' days ago';
        if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
        if($day_diff < 60) return 'last month';
        return date('F Y', $ts);
    }else{
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if($day_diff == 0){
            if($diff < 120) return 'in a minute';
            if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
            if($diff < 7200) return 'in an hour';
            if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
        }
        if($day_diff == 1) return 'Tomorrow';
        if($day_diff < 4) return date('l', $ts);
        if($day_diff < 7 + (7 - date('w'))) return 'next week';
        if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
        if(date('n', $ts) == date('n') + 1) return 'next month';
        return date('F Y', $ts);
    }
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
      <meta name="referrer" content="no-referrer">
      <title><?php echo "Search Result For: ".$_REQUEST['search']." - ".$title; ?></title>
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
               <input type="text" class="form-control" name="search" value="<?php echo $_REQUEST['search']; ?>">
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
                               <input type="text" class="form-control" name="search" value="<?php echo $_REQUEST['search']; ?>">
                               <div class="input-group-append">
                                  <button class="btn btn-light" type="submit">
                                  <i class="fas fa-search"></i> 
                                  </button>
                               </div>
                            </div>
                         </form>
                     </div>
                    <?php
                    }if($total > 0){
                        $i = 0;
                        foreach ($result as $row) {
                            $title = $row['title'];
                            $hash = $row['hash'];
                            $views = $row['views'];
                            $time = $row['time'];
                            $great = $row['is_great'];
                            $thumbnail = $row['thumbnail'];
                            $i++;
                    ?>
                     <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="video-card">
                           <div class="video-card-image">
                              <a class="play-icon" href="<?php echo BASE_URL.$hash; ?>"><i class="fas fa-play-circle"></i></a>
                              <a href="<?php echo BASE_URL.$hash; ?>"><img class="img-fluid" src="<?php echo $thumbnail; ?>" alt="" onerror="this.onerror=null;this.src='<?php echo BASE_URL; ?>public/img/null.png';"></a>
                              <?php if($great==1){echo '<div class="time"><i class="fas fa-fire"></i></div>';}?>
                              <div style="right: 90%;background:blue none repeat scroll 0 0;" class="time"><a target="_blank" href="<?php echo BASE_URL."download/".$hash; ?>"><i style="color:white" class="fas fa-download"></i></a></div>
                           </div>
                           <div class="video-card-body">
                              <div class="video-title">
                                 <a href="<?php echo BASE_URL.$hash; ?>"><?php echo $title; ?></a>
                              </div>
                              <div class="video-page text-success">
                                 <a><i class="fas fa-eye text-success"></i></a> <?php echo $views; ?>
                              </div>
                              <div class="video-view">
                                 <i class="fas fa-calendar-alt"></i> <?php echo time2str($time); ?>
                              </div>
                           </div>
                        </div>
                     </div>
                    <?php
                        if($i%5==0){
                           echo '<div class="col-xl-3 col-sm-6 mb-3"><div class="video-card"><div class="video-card-image"></div></div></div>';
                        }
                     }
                    }else{?>
                    <div class="col-md-8 mx-auto text-center  pt-4 pb-5">
                         <h1>Sorry! No Goal Found.</h1>
                         <p class="land">Unfortunately, no goal was found using your search keyword <i><?php echo $_REQUEST["search"];?></i>.</p>
                         <div class="mt-5">
                            <a class="btn btn-outline-primary" href="/"><i class="mdi mdi-home"></i> GO BACK TO HOMEPAGE</a>
                         </div>
                    </div>
                    <?php } ?>
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