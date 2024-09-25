<?php
include_once "header.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files
require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

function generateAccessCode() {
	return rand(100000, 999999);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$recipientEmail = $_POST['email'];
	$school_name = $_POST['school_name'];
	$access_code = generateAccessCode();
  $expiration_time = date("Y-m-d H:i:s", strtotime('+1 day'));

  // Check if the email already exists in the database
  $sql = "SELECT * FROM access_codes WHERE email = '$recipientEmail'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // Email exists, update the record with a new code and expiration time
    $updateSql = "UPDATE access_codes SET access_code = '$access_code', expiration_time = '$expiration_time' WHERE email = '$recipientEmail'";
    if ($conn->query($updateSql) === TRUE) {
        $msg = sendEmail($recipientEmail, $access_code);
    } else {
        $msg = "Error updating record: " . $conn->error;
    }
  } else {
    // Email does not exist, insert a new record
    $insertSql = "INSERT INTO access_codes (email, school_name, access_code, expiration_time) VALUES ('$recipientEmail', '$school_name', '$access_code', '$expiration_time')";
    if ($conn->query($insertSql) === TRUE) {
        $msg = sendEmail($recipientEmail, $access_code);
    } else {
        $msg = "Error inserting record: " . $conn->error;
    }
  }
  $conn->close();
}

// Function to send email
function sendEmail($recipientEmail, $accessCode) {
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

    // Recipients
    $mail->setFrom('krixus@kodin.ng', 'Krixus Demo');
    $mail->addAddress($recipientEmail);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Krixus Demo Access Code';
    $mail->Body    = "<p>Your access code is <strong>$accessCode</strong>. It will expire in 24 hours.<br>Click <a href='https://krixus.kodin.ng/demolaunch'>here</a> to access Krixus demo using the access code provided above.</p>";

    $mail->send();
    $msg = 'Access code created and email sent';
	}
	catch (Exception $e) {
  	$msg = "Email could not be sent. Please check your network connection or try again later.";
  }
  return $msg;
}

?>

<section class="page-section full_page d-flex align-items-center py-5 py-md-0">
	<div class="container">
		<?php if(isset($msg)) { 
			echo '<div class="col-md-4 text-center text-sm offset-md-4 alert alert-secondary alert-dismissible fade show mt-3 msgAlert" role="alert">' . $msg . '</div>';
		} ?>
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card p-4">
					<h3 class="text-center mb-4">Generate Access Code</h3>
					<form id="accessCodeForm" method="POST" action="generatecode">
						<div class="form-group mb-3">
							<input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
						</div>
						<div class="form-group mb-3">
							<input type="text" class="form-control" id="school_name" name="school_name" placeholder="Enter your school name" required>
						</div>
						<div class="d-grid">
							<button type="submit" class="btn btn-primary">Generate Access Code</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>


<?php include_once "footer.php" ?>