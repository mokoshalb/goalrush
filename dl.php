<?php
if(isset($_REQUEST['hash']) && !empty($_REQUEST['hash']) && isset($_REQUEST['title'])){
    $useragent = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36";
    $file = base64_decode($_REQUEST['hash']);
    $title = base64_decode($_REQUEST['title']).".mp4";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $file);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $filesize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    header("Content-Type: video/mp4");
    header("Content-length: $filesize");
    header("Content-Disposition: attachment; filename=$title");
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    ob_end_clean();
    @readfile($file, false, stream_context_create($arrContextOptions));
    exit;
}else{
	header("Location: ".BASE_URL);
}
?>