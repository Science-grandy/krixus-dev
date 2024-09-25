<?php
	include_once "header.php";
?>

<!-- Page Header Section -->
  <section class="page-top-section">
    <div class="container">
      <div class="row heading align-items-center text-center m-auto">
        <div class="col-12 page-text">
          <h1 class="font-weight-bold">Our subscription plans</h1>
          <p>Get started with Krixus with affordable prices that work with your school's needs and budget.</p>
        </div>
        <div class="col-md-4 offset-md-4">
        	<label class="text-secondary">Choose your duration</label>
        	<select class="form-control text-center text-secondary" id="billingCycle">
        		<option class="text-secondary" value="quarterly">Quarterly</option>
        		<option class="text-secondary" value="semi-annual">Semi-Annually</option>
        		<option class="text-secondary" value="annual">Annually</option>
        	</select>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section class="bg-white page-section">
    <div class="container pb-5">
    	<div class="d-md-flex justify-content-center align-items-top">
	      <div class="card me-md-2 badge-1">
	        <div class="card-body">
	            <h4 class="card-subtitle mb-2">Essential</h4>
	            <ul class="card-text">
	            	<li>Access to result portal</li>
	            	<li>Student data management<br>(up to 50)</li>
	            	<li>Basic Reporting</li>
	            	<li>Email support</li>
	            </ul>
	            <div class="d-flex align-items-center">
            		<a class="card-link selectPlan btn btn-outline-primary btn-sm me-auto" data-opt="essential">Select plan</a>
	            	<p class="plan-price mt-3" id="price1">₦40,000</p>
	            </div>
	        </div>
	      </div>
	      <div class="card me-md-2 badge-2">
	        <div class="card-body">
	            <h4 class="card-subtitle mb-2">Growth</h4>
	            <ul class="card-text">
	            	<li>Access to result portal</li>
	            	<li>Student data management<br>(up to 200)</li>
	            	<li>Enhanced Reporting</li>
	            	<li>Email support</li>
	            </ul>
	            <div class="d-flex align-items-center">
            		<a class="card-link selectPlan btn btn-outline-primary btn-sm me-auto" data-opt="growth">Select plan</a>
	            	<p class="plan-price mt-3" id="price2">₦90,000</p>
	            </div>
	        </div>
	      </div>
	      <div class="card me-md-2 badge-3">
	        <div class="card-body">
	            <h4 class="card-subtitle mb-2">Premium</h4>
	            <ul class="card-text">
	            	<li>Access to result portal</li>
	            	<li>Student data management<br>(up to 500)</li>
	            	<li>Enhanced Reporting</li>
	            	<li>Dedicated Account Manager</li>
	            	<li>Email support</li>
	            </ul>
	            <div class="d-flex align-items-center">
            		<a class="card-link selectPlan btn btn-outline-primary btn-sm me-auto" data-opt="premium">Select plan</a>
	            	<p class="plan-price mt-3" id="price3">₦120,000</p>
	            </div>
	        </div>
	      </div>
	      <div class="card me-md-2 badge-4">
	        <div class="card-body">
	            <h4 class="card-subtitle mb-2">Enterprise</h4>
	            <ul class="card-text">
	            	<li>Access to result portal</li>
	            	<li>Student data management<br>(Unlimited)</li>
	            	<li>Enhanced Reporting</li>
	            	<li>Dedicated Account Manager</li>
	            	<li>On-site Training and Support</li>
	            	<li>Email support</li>
	            </ul>
	            <div class="d-flex align-items-center">
            		<a class="card-link selectPlan btn btn-outline-primary btn-sm me-auto" data-opt="enterprise">Select plan</a>
	            	<p class="plan-price mt-3" id="price4">₦150,000</p>
	            </div>
	        </div>
	      </div>
    	</div>
	  </div>
	</section>

	<?php include_once "demorequest.php" ?>

<?php include_once "footer.php" ?>