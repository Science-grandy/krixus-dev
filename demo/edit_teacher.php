<?php
include 'db_connect.php';
$qry = $conn->query("SELECT t.*, u.username FROM teachers t inner join users u on u.id = t.user_id where t.id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
include 'new_teacher.php';
?>