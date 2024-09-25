<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files
require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

if (isset($_POST['name'])) {
    // Get form data
	$name = $_POST['name'];
	$email = $_POST['email'];
	$school = $_POST['school'];
	$comment = $_POST['comment'];
	$meet_date = $_POST['meet_date'];

    // Krixus email address to notify
	$krixusEmail = "krixus@kodin.ng";

    // Email subject and body for the Krixus team
	$subject = "New Demo Request Scheduled";
	$body = "
	<h2>Demo Request Details</h2>
	<p><strong>Name:</strong> $name</p>
	<p><strong>Email:</strong> $email</p>
	<p><strong>School:</strong> $school</p>
	<p><strong>Preferred Meet Date:</strong> $meet_date</p>
	<p><strong>Comment:</strong> $comment</p>
	";

    // Email subject and body for the user
	$userSubject = "Demo Request Confirmation";
	$userBody = "
	<h2>Thank you for scheduling a demo!</h2>
	<p>Hi $name,</p>
	<p>Your demo request for $meet_date has been received. We will contact you soon to confirm.</p>
	<p>Thanks,<br>Krixus Team</p>
	";

    // Setup PHPMailer
	$mail = new PHPMailer(true);

	try {
    // Server settings
		$mail->isSMTP();
    $mail->Host = 'mail.kodin.ng'; // Set your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'krixus@kodin.ng'; // SMTP username
    $mail->Password = '300Spartans@2024'; // SMTP password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    // Set the sender
    $mail->setFrom('krixus@kodin.ng', 'Krixus');

    // Add recipient (Krixus)
    $mail->addAddress($krixusEmail);

    // Add reply-to email (User's email)
    $mail->addReplyTo('krixus@kodin.ng', 'Krixus');

    // Send email to user
    $mail->addCC('sciencegrandy@gmail.com');

    // Set email format to HTML
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    // Send the email to Krixus
    $mail->send();

    // Prepare new mail for user confirmation
    $mail->clearAllRecipients();
    $mail->addAddress($email);
    $mail->Subject = $userSubject;
    $mail->Body = $userBody;
    
    // Send email to the user
    $mail->send();

    // Success message
    echo json_encode(['status' => 1, 'message' => 'Demo request successfully scheduled']);
    // header("Location: index.php#demorequest");
  } catch (Exception $e) {
    	echo json_encode(['status' => 0, 'message' => "Email could not be sent"]);
    	// header("Location: index.php#demorequest");
  }
}

?>