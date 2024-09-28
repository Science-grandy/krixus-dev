<?php
	include_once "header.php";
?>

<section class="page-section d-flex align-items-center py-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-6">
				<div class="card p-4">
					<div class="card-body">
						<h2 class="text-center mb-4">Checkout</h2>
						<form id="checkoutForm" method="POST">
							<div class="form-group mb-3">
								<label for="name">Name</label>
								<input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" required>
							</div>
							<div class="form-group mb-3">
								<label for="name">Name of School</label>
								<input type="text" name="school-name" id="name" class="form-control" placeholder="Enter your name" required>
							</div>
							<div class="form-group mb-3">
								<label for="email">Email</label>
								<input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group mb-3">
										<label for="product">Selected Plan</label>
										<p id="plan" class="form-control"></p>
										<input type="hidden" name="plan" value="">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group mb-3">
										<label for="product">Duration</label>
										<p id="duration" class="form-control"></p>
										<input type="hidden" name="duration" value="">
									</div>
								</div>
							</div>
							<div class="form-group mb-3">
								<label>Price</label>
								<p id="price" class="form-control"></p>
								<input type="hidden" name="price" id="price" value="">
							</div>
							<div class="d-grid">
								<button id="checkout_btn" type="submit" class="btn btn-primary">Checkout</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
	// Prices array with different billing cycles and plans
	const prices = {
		quarterly: ["₦40,000", "₦90,000", "₦120,000", "₦150,000"],
		"semi-annual": ["₦70,000", "₦170,000", "₦230,000", "₦290,000"],
		annual: ["₦105,000", "₦255,000", "₦345,000", "₦435,000"]
	};
	function capitalize(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	}

	$("#checkoutForm").submit(payWithPaystack);

	// Helper function to hash the price (same as on the subscription page)
	async function hashPrice(price) {
		const encoder = new TextEncoder();
		const data = encoder.encode(price);
		const hashBuffer = await crypto.subtle.digest('SHA-256', data);
		const hashArray = Array.from(new Uint8Array(hashBuffer));
		const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
		return hashHex;
	}

	// Function to get URL parameters
	function getQueryParam(param) {
		const urlParams = new URLSearchParams(window.location.search);
		return urlParams.get(param);
	}

	(async function() {
    // Retrieve values from the URL
		const plan = getQueryParam('opt');
		const duration = getQueryParam('d');
		const hashedPrice = getQueryParam('p');

    // Determine index based on plan name
		let planIndex;
		switch (plan) {
		case 'essential':
			planIndex = 0;
			break;
		case 'growth':
			planIndex = 1;
			break;
		case 'premium':
			planIndex = 2;
			break;
		case 'enterprise':
			planIndex = 3;
			break;
		default:
			console.log("Invalid plan");
			return;
		}

		const originalPrice = prices[duration][planIndex];
		const hashedOriginalPrice = await hashPrice(originalPrice);

		if (hashedPrice === hashedOriginalPrice) {
			$("#price").text(originalPrice);
			$("#plan").text(capitalize(plan));
			$("#duration").text(capitalize(duration));

			// Assign values to hidden input fields
			$("input[name='plan']").val(plan);
			$("input[name='duration']").val(duration);
			$("input[name='price']").val(originalPrice);
		} else {
			console.log('Price has been tampered with!');
		}
	})();

	function payWithPaystack(e) {
		e.preventDefault();

    // Save user data to the database first
    var form = $('#checkoutForm');
    var formData = form.serialize();

    // Use $.ajax to save the form data
    $.ajax({
    	type: 'POST',
    	url: 'save_user_data.php',
    	data: formData,
    	success: function(response) {
    		var res = JSON.parse(response);
    		if (res.status === 'success') {
				var originalPrice = $("#price").text();
				var price = originalPrice.replace('₦', '').replace(/,/g, '');
    			var handler = new PaystackPop();
				handler.newTransaction({
					key: 'pk_test_7994319236962aaaf8d47507b2774256787ed6a0',
					email: $('#email').val(),
					amount: price * 100,
					currency: "NGN",
					ref: 'PSK_' + Math.floor((Math.random() * 1000000000) + 1), // Unique reference
					callback: function(response) {
						// Payment successful
						window.location.href = 'verify.php?reference=' + response.reference + '&id=' + res.id;
					},
					onClose: function() {
						alert('Payment was not completed. Please try again.');
					}
				});
    		} else {
    			alert('Failed to save data. Please try again.');
    		}
    	},
    	error: function() {
    		alert('An error occurred while saving data. Please try again.');
    	}
    });
  }

</script>

<?php include_once "footer.php" ?>