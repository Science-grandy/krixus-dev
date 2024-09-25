<?php

?>
<!-- Footer Section -->
<footer class="container-fluid px-md-5 py-md-5 py-3">
	<div class="row border-top mt-2 pt-2">
		<div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-start align-items-center">
			<a href="#" class="me-2 text-body-secondary text-decoration-none lh-1">
				<img src="assets/img/logo.svg" width="24" alt="Krixus logo">
			</a>
			<p class="mb-0 text-body-secondary">© 2024 Krixus. All rights reserved.</p>
		</div>

		<ul class="nav col-12 col-md-6 justify-content-center justify-content-md-end align-items-center list-unstyled d-flex">
			<li class="mx-1">
				<a href="#" class="me-3">Privacy Policy</a>
			</li>
			<li class="mx-1">
				<a href="#">Contact Us</a>
			</li>
		</ul>
	</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script src="https://js.paystack.co/v2/inline.js"></script>
<script src="assets/js/script.js"></script>
<script type="text/javascript">
	function payWithPaystack() {
		var handler = PaystackPop.setup({
			key: 'pk_test_7994319236962aaaf8d47507b2774256787ed6a0',
			email: document.getElementById('email-address').value,
			amount: document.getElementById('amount').value * 100,
			currency: "NGN",
			ref: 'PSK_' + Math.floor((Math.random() * 1000000000) + 1),
			callback: function(response) {
	          // Handle response after payment
				window.location.href = 'verify.php?reference=' + response.reference;
			},
			onClose: function() {
				alert('Transaction was not completed, window closed.');
			}
		});
		handler.openIframe();
	}
</script>
</body>
</html>