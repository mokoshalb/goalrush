<?php
require_once "config.php";
$statement = $pdo->prepare("SELECT * FROM goals ORDER BY id DESC LIMIT 0, 1000");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
$now = date("Y-m-d");
$map = '<url><loc>'.BASE_URL.'</loc><priority>1.0</priority><changefreq>always</changefreq><lastmod>'.$now.'</lastmod></url>';;
foreach($result as $row) {
	$url = BASE_URL.$row['hash'];
	$download = BASE_URL.'download/'.$row['hash'];
	$time = date("Y-m-d", $row['time']);
    $map .= '<url><loc>'.$url.'</loc><priority>0.8</priority><changefreq>never</changefreq><lastmod>'.$time.'</lastmod></url>';
    //$map .= '<url><loc>'.$download.'</loc><priority>0.8</priority><changefreq>never</changefreq><lastmod>'.$time.'</lastmod></url>';
}
header("Content-Type:text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'.$map.'</urlset>';
?>