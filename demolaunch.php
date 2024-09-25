<?php
	session_start();
	include_once "header.php";
	ob_start();

	// Check code expiration before allowing access
	if (isset($_POST['access_code'])) {
		$access_code = sanitize($_POST['access_code']);
		$role = sanitize($_POST['role']);
		$sql1 = "SELECT * FROM access_codes WHERE access_code = '$access_code' AND expiration_time > NOW()";
		$sql2 = "SELECT * FROM users WHERE role = '$role'";
		$result1 = $conn->query($sql1);
		$result2 = $conn->query($sql2);

		if ($result1->num_rows > 0 && $result2->num_rows > 0) {
			foreach($result2->fetch_array() as $key => $value) {
				$_SESSION['login_'.$key] = $value;
			}
		    header("Location: demo/index.php");
		} else {
		    $errMsg = "Invalid or expired access code.";
		}
	}
?>

<section class="page-section full_page d-flex align-items-center py-5">
	<div class="container">
		<?php if(isset($errMsg)) {
			echo '<div class="col-md-4 text-center text-sm offset-md-4 alert alert-danger alert-dismissible fade show mt-3 msgAlert" role="alert">' . $errMsg . '</div>';
		} ?>
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card p-4">
					<div class="card-body">
						<h2 class="text-center mb-4">Enter Access Code</h2>
						<p class="text-center">This code was sent to your email. Didn't get code? Click <a href="resendcode">here</a></p>
					</div>

					<form id="accessCodeForm" action="demolaunch" method="POST">
						<div class="form-group mb-3 text-center">
							<label class="text-secondary" for="accessCode">Access Code</label>
							<input type="text" name="access_code" class="form-control text-center" id="accessCode" placeholder="Enter your access code" maxlength="6" required>
						</div>
						<div class="form-group d-flex justify-content-center align-items-center mb-3 text-center">
							<div class="mx-1">
								<label class="text-secondary" for="accessCode">Select role:</label>
							</div>
							<div class="mx-1">
								<select class="form-control text-center" name="role">
									<option value="admin">Admin</option>
									<option value="teacher">Teacher</option>
									<option value="student">Student</option>
								</select>
							</div>
						</div>
						<div class="d-grid">
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<?php include_once "footer.php" ?>