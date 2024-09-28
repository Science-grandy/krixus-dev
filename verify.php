<?php
	// Database connection
	include_once "header.php";

	$msg = "";

	if (isset($_GET['reference'])) {
		$reference = $_GET['reference'];
		$id = sanitize($_GET['id']);

		// Verify the transaction
		$url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			[
			"Authorization: Bearer sk_test_6f9700e665349ccbc55553a5c9f82889c69bbf31",
		]
	);
		$request = curl_exec($ch);
		curl_close($ch);

		if ($request) {
			$result = json_decode($request, true);

			if ($result['status']) {
				if ($result['data']['status'] == 'success') {
					// Payment was successful
					$msg = "Transaction successful. Reference: " . $result['data']['reference'];
					$sql = "UPDATE subscribed_users SET trans_status = ? WHERE id = $id";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("s", $msg);
				} else {
					// Payment failed
					$msg = "Transaction failed. Please try again.";
					$sql = "UPDATE subscribed_users SET trans_status = ? WHERE id = $id";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("s", $msg);
				}

				$stmt->execute();
				
			} else {
				// Something went wrong while verifying the transaction
				$msg = "Transaction verification failed.";
			}
		} else {
			$msg = "Curl request failed.";
		}
	} else {
		$msg = "No transaction reference found.";
	}
?>

<section class="page-section d-flex align-items-center py-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-6">
				<div class="card p-4">
					<div class="card-body text-center">
						<h2 class="text-center mb-4">Verification</h2>
						<?php
						if (isset($_GET['reference'])) {
							$reference = $_GET['reference'];

							// Verify transaction logic...

							if ($result['data']['status'] == 'success') {
							echo "<h4>Transaction Successful</h4>";
							echo "<p>Reference: <strong>{$result['data']['reference']}</strong></p>";
							} else {
							echo "<h4 style='color: red;'>Transaction Failed</h4>";
							}
						} else {
							echo "<h4 style='color: red;'>No transaction reference found.</h4>";
						}
						?>
						<a href="index" class="btn-home mt-3">Go Back to Home</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php include_once "footer.php" ?>