<?php
function video2Imgur($filename, $uploadFilePath){
    $ch = curl_init();
    $options = [
        CURLOPT_URL => 'https://api.imgur.com/3/upload',
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer xxxxxxxxxxxxx',
        ],
        CURLOPT_POSTFIELDS => [
            'video' => $uploadFilePath,
            'type' => 'url',
            'name' => $filename,
            'title' => $filename,
            'description' => $filename,
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_TIMEOUT => 30,
    ];
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    $mp4 = false;
    $pms = json_decode($result, true);
    $fp = fopen('imgur.txt', 'a');
    fwrite($fp, $result."\n");
    fclose($fp);  
    if(isset($pms['status'])){
        if($pms['status'] == 200){
            $mp4 = $pms['data']['link'];   
        }
    }
    return $mp4;
}
?>