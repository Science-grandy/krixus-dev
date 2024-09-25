<?php
include 'db_connect.php';
$qry = $conn->query("SELECT s.*, u.username FROM students s left join users u on u.id = s.user_id where s.id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v) {
	$$k = $v;
}
include 'new_student.php';
?>