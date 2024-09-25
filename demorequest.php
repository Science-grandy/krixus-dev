<?php  ?>

<!-- Demo and Scheduling Section -->
<section class="bg-light text-center py-3 py-md-5" id="demorequest">
	<div class="container py-5">
		<div class="col-md-6 offset-md-3">
			<h2>Request Your Krixus Demo</h2>
			<p class="mb-4">Curious about how Krixus can revolutionize your school’s data management? Request a demo session and see it in action.</p>
		</div>

		<div class="col-md-6 offset-md-3 mt-5 mb-2" id="responseMessage"></div>
		<form class="row g-3" method="POST" id="demoRequestForm">
			<div class="col-md-6 offset-md-3">
				<div class="row">
					<div class="col-md-6 mb-3">
						<input type="text" name="name" class="form-control" placeholder="Name" required>
					</div>
					<div class="col-md-6 mb-3">
						<input type="email" name="email" class="form-control" placeholder="Email" required>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 mb-3">
						<input type="text" name="school" class="form-control" placeholder="School Name" required>
					</div>
					<div class="col-md-6 mb-3 d-md-flex text-start justify-content-md-center align-items-center">
						<label class="col-md-6 text-secondary">Choose a meeting date:</label>
						<input type="date" name="meet_date" class="form-control" placeholder="Select date" required>
					</div>
				</div>

				<div class="row">
					<div class="col-12 mb-3">
						<textarea class="form-control" name="comment" rows="4" placeholder="Comments/Questions"></textarea>
					</div>
					<div class="col-12">
						<button type="submit" id="scheduleBtn" class="btn btn-primary">Schedule now</button>
					</div>
				</div>

			</div>
		</form>
	</div>
</section>