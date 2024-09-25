<?php
	include_once "header.php";

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	// Load PHPMailer files
	require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
	require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
	require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

	if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Function to send email
    function sendEmail($recipientEmail, $accessCode) {
    	$mail = new PHPMailer(true);
    	try {
        // Server settings
    		$mail->isSMTP();
        $mail->Host = 'mail.kodin.ng';
        $mail->SMTPAuth = true;
        $mail->Username = 'krixus@kodin.ng';
        $mail->Password = '300Spartans@2024';
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
        $msg = 'Access code resent to email address.';
    	}
    	catch (Exception $e) {
      	$msg = "Email could not be sent. Please check your network connection or try again later.";
      }
      return $msg;
    }
    
    // Check if an unexpired access code exists for the provided email
    $sql = "SELECT * FROM access_codes WHERE email = '$email' AND expiration_time > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $access_code = $row['access_code'];
      $msg = sendEmail($email, $access_code);
    } else {
      $msg = "No valid access code found for this email, or the code has expired.";
    }
	}
?>

<section class="page-section full_page d-flex align-items-center py-5">
	<div class="container">
		<?php if(isset($msg)) { 
			echo '<div class="col-md-4 text-center text-sm offset-md-4 alert alert-secondary alert-dismissible fade show mt-3 msgAlert" role="alert">' . $msg . '</div>';
		} ?>
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card p-4">
					<div class="card-body">
						<h2 class="text-center mb-4">Resend Access Code</h2>
						<p class="text-center">Enter your email address to receive your access code again.</p>
					</div>

					<form id="resendCodeForm" action="resendcode" method="POST">
						<div class="form-group mb-3 text-center">
							<input type="email" name="email" class="form-control text-center" id="email" placeholder="Enter your email" required>
						</div>
						<div class="d-grid">
							<button type="submit" class="btn btn-primary">Resend Code</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>


<?php include_once "footer.php"; ?>