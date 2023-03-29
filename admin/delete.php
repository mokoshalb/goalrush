<?php
require_once "header.php";
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM goals WHERE id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if($total == 0){
		header('location: logout.php');
		exit;
	}
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as $row) {
    	$hash = $row['gr_hash'];
    }
}
//then delete from our DB
$statement = $pdo->prepare("DELETE FROM goals WHERE id=?");
$statement->execute(array($_REQUEST['id']));
header('location: goal.php');
?>