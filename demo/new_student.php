<?php
  if(!isset($conn)){ include 'db_connect.php'; }

  if (isset($username)) {
    $existing_username = $username;
  } else {
    $existing_username = '';
  }
  
?>

<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form class="modern-form" action="" id="manage-student">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="row">
          <div class="col-md-6">
            <div id="msg" class=""></div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <input id="reg-no" type="text" class="form-control" name="reg_no" value="<?php echo isset($reg_no) ? $reg_no : '' ?>" placeholder=" " required>
                  <label for="reg-no">Reg No.</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="hidden" id="existing_username" name="existing_username" value="<?php echo $existing_username; ?>">
                  <input type="text" id="username" class="form-control" name="username" value="<?php echo isset($username) ? $username : '' ?>" placeholder=" " required>
                  <label for="username">Username</label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <input type="text" id="firstname" class="form-control" name="firstname" value="<?php echo isset($firstname) ? $firstname : '' ?>" placeholder=" " required>
                  <label for="firstname">First Name</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <input type="text" id="middlename" class="form-control" name="middlename" value="<?php echo isset($middlename) ? $middlename : '' ?>" placeholder=" ">
                  <label for="middlename">Middle Name</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <input type="text" id="lastname" class="form-control" name="lastname" value="<?php echo isset($lastname) ? $lastname : '' ?>" placeholder=" " required>
                  <label for="lastname">Last Name</label>
                </div>
              </div> 
            </div>

            <div class="row">
              <div class="col-md-8">
                <div class="form-group">
                  <input type="password" id="password" class="form-control" placeholder="<?php echo isset($id) ? 'Leave blank to keep current password' : ' ' ?>" name="password" <?php echo isset($id) ? '' : 'required'; ?>>
                  <label for="password">Set password</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <select id="gender" name="gender" class="form-select touched" required>
                    <option value="female">Female</option>
                    <option value="male">Male</option>
                  </select>
                  <label for="gender">Gender</label>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <div class="form-group">
                <textarea name="address" id="address" cols="30" rows="4" class="form-control" placeholder=" "><?php echo isset($address) ? $address : '' ?></textarea>
                <label for="address">Address</label>
              </div>
            </div>
            <div class="form-group">
              <div class="form-group">
                <select id="applied-class" name="class_id" class="form-control select2 select2-sm" placeholder=" " required>
                  <option></option> 
                  <?php 
                    $classes = $conn->query("SELECT * FROM classes order by level asc,section asc ");
                    while($row = $classes->fetch_array()):
                  ?>
                  <option value="<?php echo $row['id'] ?>" <?php echo isset($class_id) && $class_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['level'].'-'.$row['section']) ?></option>
                  <?php endwhile; ?>
                </select>
                <label for="applied-class">Class</label>
              </div>
            </div>
          </div>
          <input type="hidden" name="role" value="student">
        </div>
      </form>
  	</div>
  	<div class="card-footer border-top border-info">
  		<div class="d-flex w-100 justify-content-center align-items-center">
  			<button class="btn btn-flat  bg-gradient-primary mx-2" form="manage-student">Save</button>
  			<a class="btn btn-flat bg-gradient-secondary mx-2" href="./index.php?page=student_list">Cancel</a>
  		</div>
  	</div>
	</div>
</div>
<script>
$(document).ready(function() {
  // Function to check username availability
  function checkUsername(username) {
    return $.ajax({
      url: 'ajax.php?action=check_username',
      type: 'POST',
      data: { username: username },
      dataType: 'json'
    });
  }

  $('#username').on('blur', function() {
    let username = $(this).val();
    let existingUsername = $('#existing_username').val();
    if (username == existingUsername) {
      $('#msg').html('<div></div>');
    }
    if (username && username !== existingUsername) {
      checkUsername(username).done(function(response) {
        if (response.status == 0) {
          $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + response.message + '</div>');
        } else {
          $('#msg').html('<div class="alert alert-success"><i class="fa fa-check"></i> ' + response.message + '</div>');
        }
      });
    }
  });

  $('#manage-student').submit(function(e) {
    e.preventDefault();
    start_load();

    let username = $('#username').val();
    let existingUsername = $('#existing_username').val();
    if (username && username !== existingUsername) {
      checkUsername(username).done(function(response) {
        if (response.status == 0) {
          $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + response.message + '</div>');
          end_load();
        } else {
          saveStudent();
        }
      });
    } else {
      saveStudent();
    }
  });

  function saveStudent() {
    $.ajax({
      url: 'ajax.php?action=save_student',
      type: 'POST',
      data: new FormData($('#manage-student')[0]),
      cache: false,
      contentType: false,
      processData: false,
      success: function(response) {
        console.log(response);
        let res = JSON.parse(response);
        if (res.status == 1) {
          alert_toast(res.message, "success");
          setTimeout(function() {
            location.href = 'index.php?page=student_list';
          }, 2000);
        } else {
          $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + res.message + '</div>');
        }
        end_load();
      },
      error: function(xhr, status, error) {
        $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> An unexpected error occurred: ' + error + '</div>');
        end_load();
      }
    });
  }
});



function displayImgCover(input,_this) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#cover').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}
</script>