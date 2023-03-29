<?php
function generateHash($string, $len=8){
    $hex = md5($string."GoalRush");
    $pack = pack('H*', $hex);
    $uid = base64_encode($pack);
    $uid = preg_replace('/[^a-zA-Z 0-9]+/', "", $uid);
    if($len<4){
        $len=4;
    }elseif($len>128){
        $len=128;
    }
    return substr($uid, 0, $len);
}
if(isset($_REQUEST['video']) && !empty($_REQUEST['video'])){
    $title = $_REQUEST['title'];
    $thumbnail = $_REQUEST['thumbnail'];
    $video = $_REQUEST['video'];
    $is_great = $_REQUEST['is_great'];
    $time = time();
    $hash = generateHash($time);
    require_once "config.php";
    $statement = $pdo->prepare("SELECT * FROM goals WHERE title=?");
	$statement->execute(array($title));
	$total = $statement->rowCount();
	if($total>0){
	    echo "EXIST";
	}else{
        $statement = $pdo->prepare("INSERT IGNORE INTO goals (title,video,thumbnail,hash,views,time,is_great) VALUES (?,?,?,?,?,?,?)");
    	$statement->execute(array($title,$video,$thumbnail,$hash,0,$time,$is_great));
    	echo $hash;
	}
}else{
    echo "NULL";
}
?>