<?php
function video2Catbox($fileurl){
    $ch = curl_init();
    $options = [
        CURLOPT_URL => 'https://catbox.moe/user/api.php',
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => [
            'url' => $fileurl,
            'reqtype' => "urlupload",
            'userhash' => "63ca5fea955e6ba58de8512e8",
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_TIMEOUT => 30,
    ];
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    /*
    $fp = fopen('catbox.txt', 'a');
    fwrite($fp, $result."\n");
    fclose($fp);
    */
    $mp4 = false;
    if(!empty($result) && filter_var($result, FILTER_VALIDATE_URL)){
        $mp4 = $result;
    }
    return $mp4;
}
?>