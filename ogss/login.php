<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('db_connect.php');
ob_start();
// check if the system setting is set on global session
if(!isset($_SESSION['system'])){
  $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
  foreach($system as $k => $v){
    $_SESSION['system'][$k] = $v;
  }
}
ob_end_flush();
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login | <?php echo $school_name ?></title>
 	

<?php include_once('header.php'); ?>
<?php
if(isset($_SESSION['login_id']))
header("location:index.php?page=home");

?>

</head>
<style>
	body{
		width: 100%;
    height: calc(100%);
    position: fixed;
    top:0;
    bottom: 0;
    left: 0;
    align-items:center !important;
    /*background: #007bff;*/
	}
	#main{
		width:100%;
		height: calc(100%);
		display: flex;
	}

</style>

<body class="bg-dark">


  <main id="main" >
  	
  		<div class="align-self-center w-100">
		<h4 class="text-white text-center"><b><?php echo $school_name ?> - Login</b></h4>
  		<div id="login-center" class="bg-dark row justify-content-center">
  			<div class="card col-md-4">
  				<div class="card-body">
  					<form id="login-form" >
  						<div class="form-group">
  							<label for="username" class="control-label text-dark">Username</label>
  							<input type="text" id="username" name="username" class="form-control form-control-sm">
  						</div>
  						<div class="form-group">
  							<label for="password" class="control-label text-dark">Password</label>
  							<input type="password" id="password" name="password" class="form-control form-control-sm">
  						</div>
  						<div class="w-100 d-flex justify-content-center align-items-center">
                <button class="btn-sm btn-block btn-wave col-md-4 btn-primary m-0 mr-1" id="log_in">Login</button>
                <!-- <button class="btn-sm btn-block btn-wave col-md-4 btn-success m-0" type="button" id="view_result">View Result</button> -->
              </div>
  					</form>
  				</div>
  			</div>
  		</div>
  		</div>
  </main>

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

</body>
<?php include 'footer.php' ?>
<script>
  // show the modal when the view result button is clicked
  $('#view_result').click(function(){
    $('#view_student_results').modal('show')
  })

  // Start the submit function when the login button is clicked
	$('#login-form').submit(function(e){
		e.preventDefault()
		$('#log_in').attr('disabled',true).html('Logging in...');

    // Remove the error message if it exceeds 1
		if($(this).find('.alert-danger').length > 0 )
			$(this).find('.alert-danger').remove();
		$.ajax({
			url:'ajax.php?action=login',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)
		$('#login-form button[type="button"]').removeAttr('disabled').html('Login');

			},
			success:function(resp){
				if(resp == 1){
          location.href ='index.php?page=home';
				}else{
					$('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
					$('#log_in').removeAttr('disabled').html('Login');
				}
			}
		})
	})
	$('.number').on('input keyup keypress',function(){
        var val = $(this).val()
        val = val.replace(/[^0-9 \,]/, '');
        val = val.toLocaleString('en-US')
        $(this).val(val)
    })
</script>	
</html>