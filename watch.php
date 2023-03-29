<?php
require_once "config.php";

//get title, description from DB
$statement = $pdo->prepare("SELECT * FROM settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$title = $row['title'];
	$description = $row['description'];
}

//get midnight time for today and yesterday
$todayMidnight = strtotime('today midnight');
$yesterdayMidnight = strtotime('yesterday midnight');

//to process if url contain hash
if(isset($_REQUEST["hash"])){
    $statement = $pdo->prepare("SELECT * FROM goals WHERE hash = ?");
    $statement->execute(array($_REQUEST["hash"]));
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $total = $statement->rowCount();
	if($total == 0){
		header('location: 404.php');
		exit;
	}
    foreach ($result as $row) {
        $clipTitle = $row['title'];
        $clipUrl = $row['video'];
        $clipThumb = $row['thumbnail'];
        $views = $row['views'];
        $clipHash = $row['hash'];
        $clipTime = $row['time'];
    }
    $statement = $pdo->prepare("UPDATE goals SET views = views + ? WHERE hash = ?");
    $statement->execute(array(1, $clipHash));
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
        <title><?php echo $clipTitle." - ".$title; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?php echo $description; ?>">
        <meta property="og:title" content="<?php echo $clipTitle." - ".$title; ?>">
        <meta property="og:type" content="video">
        <meta property="og:url" content="<?php echo BASE_URL.$clipHash; ?>">
        <meta property="og:description" content="<?php echo $description; ?>">
        <meta property="og:image" content="<?php echo $clipThumb; ?>">
        <meta name="referrer" content="no-referrer">
        <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>public/img/favicon.png">
        <link href="<?php echo BASE_URL; ?>public/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo BASE_URL; ?>public/css/fontawesome.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo BASE_URL; ?>public/css/style.css" rel="stylesheet">
   </head>
   <body id="page-top">
      <nav class="navbar navbar-expand navbar-light bg-white static-top osahan-nav sticky-top">
         &nbsp;&nbsp;&nbsp;&nbsp;
         <a class="navbar-brand mr-1" href="<?php echo BASE_URL; ?>"><img class="img-fluid" alt="" src="<?php echo BASE_URL; ?>public/img/logo.png"></a>
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
         <div id="content-wrapper">
            <div class="container-fluid pb-0">
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
                     <div class="col-md-8">
                        <div class="single-video-left">
                           <div class="single-video">
                                <div><?php if($mobile){echo $mobilecode;}?></div>
                                <div class="embed-responsive embed-responsive-16by9">
								    <div id="player" class="embed-responsive-item"></div>
								</div>
                           </div>
                           <div class="single-video-title box mb-3">
                              <div class="float-right">
                                  <a href="<?php echo BASE_URL."download/".$clipHash; ?>">
                                      <button class="btn btn btn-outline-primary" type="button"><i class="fas fa-download"></i></button></a></div>
                              <h2><?php echo $clipTitle; ?></h2>
                              <p class="mb-0 text-success"><i class="fas fa-eye text-success"></i> <?php echo $views+1; ?> views</p>
                              <p class="mb-0"><i class="fas fa-calendar-alt"></i>&nbsp;<?php echo time2str($clipTime); ?></p>
                           </div>
                           <div class="mb-3"><?php if($mobile){echo $mobilecode;}?></div>
                        </div>
                        <?php if(!$mobile){?>
                        <div class="single-video-info-content box mb-3"></div>
                        <?php }?>
                     </div>
                     <div class="col-md-4">
                        <div class="single-video-right">
                           <div class="row">
                              <?php if(!$mobile){?>
                              <div class="col-md-12">
                                 <div class="box mb-3"> </div>
                              </div>
                              <?php }?>
                              <div class="col-md-12">
                              <div class="main-title">
                                  <!--
                                <div class="btn-group float-right right-action">
                                   <a href="#" class="right-action-link text-gray" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                   Sort by <i class="fa fa-caret-down" aria-hidden="true"></i>
                                   </a>
                                   <div class="dropdown-menu dropdown-menu-right">
                                      <a class="dropdown-item" href="#"><i class="fas fa-fw fa-time"></i> &nbsp; Latest</a>
                                      <a class="dropdown-item" href="#"><i class="fas fa-fw fa-signal"></i> &nbsp; Viewed</a>
                                      <a class="dropdown-item" href="#"><i class="fas fa-fw fa-times-circle"></i> &nbsp; Close</a>
                                   </div>
                                </div>-->
                                <h6>Up Next</h6>
                             </div>
                                <?php
                                $statement = $pdo->prepare("SELECT * FROM goals ORDER BY id DESC LIMIT 12");
                                $statement->execute();
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    $ltitle = $row['title'];
                                    $thumbnail = $row['thumbnail'];
                                    $lviews = $row['views'];
                                    $ltime = $row['time'];
                                    $great = $row['is_great'];
                                    $hash = $row['hash'];
                                ?>
                                 <div class="video-card video-card-list">
                                    <div class="video-card-image">
                                       <a class="play-icon" href="<?php echo BASE_URL.$hash; ?>"><i class="fas fa-play-circle"></i></a>
                                       <a href="<?php echo BASE_URL.$hash; ?>">
                                           <img class="img-fluid" style="height:80px" src="<?php echo $thumbnail;?>" alt="" onerror="this.onerror=null;this.src='<?php echo BASE_URL; ?>public/img/null.png';"></a>
                                       <?php if($great==1){echo '<div class="time"><i class="fas fa-fire"></i></div>';}?>
                                    </div>
                                    <div class="video-card-body">
                                       <div class="video-title">
                                          <a href="<?php echo BASE_URL.$hash; ?>"><?php echo strlen($ltitle)>75?substr(rtrim($ltitle),0,70)."...":$ltitle; ?></a>
                                       </div>
                                       <div class="video-page text-success">
                                        <i class="fas fa-eye text-success"></i>&nbsp;<?php echo $lviews;?> views
                                       </div>
                                       <div class="video-view">
                                        <i class="fas fa-calendar-alt"></i>&nbsp;<?php echo time2str($ltime); ?>
                                       </div>
                                    </div>
                                 </div>
                                 <?php }?>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
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
      </div>
      <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
      <script src="<?php echo BASE_URL; ?>public/js/jquery.min.js"></script>
      <script src="<?php echo BASE_URL; ?>public/js/bootstrap.min.js"></script>
      <script src="<?php echo BASE_URL; ?>public/js/xg-player.min.js"></script>
      <script src="<?php echo BASE_URL; ?>public/js/main.js"></script>
      <script>
        const player = new Player({
            id: 'player',
            url: atob("<?php echo base64_encode($clipUrl); ?>"),
            autoplay: true,
            pip: true,
            playsinline: true,
            fluid: true,
        })
      </script>
   </body>
</html>