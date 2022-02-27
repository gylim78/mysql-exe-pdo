<?php 

$dbhost = 'ls-dc7f13dec8470c8e3630a68e474ddc6b23d1d58c.cdonfpfhidai.ap-southeast-1.rds.amazonaws.com'; // AWS Lightsail
$dbuser = 'touchprint';
$dbpass = 'sun0104';


$db = 'sngs_orders';

date_default_timezone_set("Asia/Singapore");

$dsn = "mysql:host=$dbhost;dbname=$db;charset=UTF8";

try {
	$pdo = new PDO($dsn, $dbuser, $dbpass);

	// if ($pdo) {
	// 	echo "Connected to the $db database successfully!";
	// }
} catch (PDOException $e) {
	echo $e->getMessage();
}


?>