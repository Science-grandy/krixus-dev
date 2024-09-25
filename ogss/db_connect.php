<?php 

$conn= new mysqli('localhost','kodinng_mk','300Spartans@2024','kodinng_kr8_resultportal')or die("Could not connect to mysql".mysqli_error($conn));

// Get the school name for registered school
$query = $conn->query("SELECT ss.school_name, s.name as session_name, t.name as term_name FROM school_settings ss inner join sessions s on s.school_name_id = ss.id inner join terms t on t.session_id = s.id where s.school_name_id=ss.id and t.session_id=s.id");
while ($row = $query->fetch_assoc()) {
  $school_name = $row['school_name'];
  // $term_name = $row['term_name'];
  // $session_name = $row['session_name'];
};

include_once "functions.php";