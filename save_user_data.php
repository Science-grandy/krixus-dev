<?php
	// Database connection
	include_once "config.php";
	include_once "functions.php";

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$name = sanitize($_POST['name']);
		$plan = sanitize($_POST['plan']);
		$email = sanitize($_POST['email']);
		$price = sanitize($_POST['price']);
		$duration = sanitize($_POST['duration']);
		$school_name = sanitize($_POST['school-name']);

		// Prepare SQL statement with placeholders
		$sql = "INSERT INTO subscribed_users (name, school_name, email, plan, duration, price) VALUES (?, ?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);

		// Bind parameters ('s' for string)
		$stmt->bind_param("ssssss", $name, $school_name, $email, $plan, $duration, $price);

		if ($stmt->execute()) {
			$id = $conn->insert_id;
			echo json_encode(['status' => 'success', 'id' => $id]);
		} else {
			echo json_encode(['status' => 'error']);
		}

		// Close the statement
		$stmt->close();
	}
?>
