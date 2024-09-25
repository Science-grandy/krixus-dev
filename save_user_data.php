<?php
	// Database connection
	include_once "config.php";

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$name = sanitize($_POST['name']);
		$school_name = sanitize($_POST['school-name']);
		$email = sanitize($_POST['email']);
		$plan = sanitize($_POST['plan']);
		$plan = sanitize($_POST['duration']);
		$price = sanitize($_POST['price']);

		    // Save user data to the database
		$sql = "INSERT INTO subscribed_users (name, school_name, email, plan, duration, price) VALUES ($name, $school_name, $email, $plan, $duration, $price)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':name', $name);
		$stmt->bindParam(':name', $school_name);
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':plan', $plan);
		$stmt->bindParam(':plan', $duration);
		$stmt->bindParam(':price', $price);

		if ($stmt->execute()) {
			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error']);
		}
	}

?>