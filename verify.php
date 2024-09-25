<?php
if (isset($_GET['reference'])) {
	$reference = $_GET['reference'];

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
				echo "Transaction successful. Reference: " . $result['data']['reference'];
				// You can store the transaction details in your database here
			} else {
				// Payment failed
				echo "Transaction failed. Please try again.";
			}
		} else {
			// Something went wrong while verifying the transaction
			echo "Transaction verification failed.";
		}
	} else {
		echo "Curl request failed.";
	}
} else {
	echo "No transaction reference found.";
}
?>