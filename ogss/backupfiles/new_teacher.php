<?php
  if(!isset($conn)){ include 'db_connect.php'; }


  // Fetch existing teacher data if in edit mode
  $teacher_id = isset($_GET['id']) ? $_GET['id'] : '';
  $teacher_data = [];
  $class_ids = [];
  $subject_ids = [];
  $existing_username = '';

  if ($teacher_id) {
      // Fetch teacher data including username
      $qry = $conn->query("SELECT t.*, u.username,u.password FROM teachers t LEFT JOIN users u ON t.user_id = u.id WHERE t.id = $teacher_id");
      if ($qry && $qry->num_rows > 0) {
          $teacher_data = $qry->fetch_assoc();
          $existing_username = $teacher_data['username'];
      }

      // Fetch the current class and subject assignments
      $class_result = $conn->query("SELECT class_id FROM teacher_subject_class WHERE teacher_id = $teacher_id");
      while ($row = $class_result->fetch_assoc()) {
          $class_ids[] = $row['class_id'];
      }

      $subject_result = $conn->query("SELECT subject_id FROM teacher_subject_class WHERE teacher_id = $teacher_id");
      while ($row = $subject_result->fetch_assoc()) {
          $subject_ids[] = $row['subject_id'];
      }
  }
  ?>
<div class="col-lg-12">
  <div class="card card-outline card-primary">
      <div class="card-body">
          <form action="" id="manage-teacher">
              <input type="hidden" name="id" value="<?php echo isset($teacher_data['id']) ? $teacher_data['id'] : '' ?>">
              <div class="row">
                  <div class="col-md-6">
                      <div id="msg" class=""></div>
                      <div class="row">
                          <div class="col-md-4 text-dark">
                              <div class="form-group">
                                  <label for="" class="control-label">Teacher Code</label>
                                  <input type="text" class="form-control form-control-sm" name="teacher_code" placeholder="..." value="<?php echo isset($teacher_data['teacher_code']) ? $teacher_data['teacher_code'] : '' ?>" required>
                              </div>
                          </div>
                          <div class="col-md-8 text-dark">
                              <div class="form-group">
                                  <label for="" class="control-label">Username</label>
                                  <input type="hidden" id="existing_username" name="existing_username" placeholder="..." value="<?php echo $existing_username; ?>">
                                  <input type="text" id="username" class="form-control form-control-sm" name="username" placeholder="..." value="<?php echo isset($teacher_data['username']) ? $teacher_data['username'] : '' ?>" required>
                              </div>
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-4 text-dark">
                              <div class="form-group">
                                  <label for="" class="control-label">First Name</label>
                                  <input type="text" class="form-control form-control-sm" name="firstname" placeholder="..." value="<?php echo isset($teacher_data['firstname']) ? $teacher_data['firstname'] : '' ?>" required>
                              </div>
                          </div>
                          <div class="col-md-4 text-dark">
                              <div class="form-group">
                                  <label for="" class="control-label">Middle Name</label>
                                  <input type="text" class="form-control form-control-sm" name="middlename" placeholder="..." value="<?php echo isset($teacher_data['middlename']) ? $teacher_data['middlename'] : '' ?>">
                              </div>
                          </div>
                          <div class="col-md-4 text-dark">
                              <div class="form-group">
                                  <label for="" class="control-label">Last Name</label>
                                  <input type="text" class="form-control form-control-sm" name="lastname" placeholder="..." value="<?php echo isset($teacher_data['lastname']) ? $teacher_data['lastname'] : '' ?>" required>
                              </div>
                          </div> 
                      </div>
                      <div class="row">
                          <div class="col-md-8 text-dark">
                                <div class="form-group">
                                    <label for="" class="control-label">Password</label>
                                    <input type="password" class="form-control form-control-sm" placeholder="<?php echo isset($id) ? 'Leave blank to keep current password' : 'Set a password' ?>" name="password" <?php echo isset($id) ? '' : 'required'; ?>>
                                </div>
                            </div>
                      </div>
                  </div>

                  <div class="col-md-6">
                      <div class="row">
                          <div class="col-md-6 text-dark">
                              <div class="form-group">
                                  <label for="" class="control-label">Class</label>
                                  <select name="class_ids[]" class="form-control select2 select2-sm" required multiple>
                                      <?php
                                      $classes = $conn->query("SELECT id, CONCAT(level, '-', section) AS class FROM classes ORDER BY CONCAT(level, '-', section) ASC");
                                      while ($row = $classes->fetch_assoc()) {
                                          $class = strtoupper($row['class']);
                                          $selected = in_array($row['id'], $class_ids) ? 'selected' : '';
                                          echo "<option value='{$row['id']}' {$selected}>{$class}</option>";
                                      }
                                      ?>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-6 text-dark">
                              <div class="form-group">
                                  <label for="" class="control-label">Subject</label>
                                  <select name="subject_ids[]" class="form-control select2 select2-sm" required multiple>
                                      <?php
                                      $subjects = $conn->query("SELECT id, subject FROM subjects");
                                      while ($row = $subjects->fetch_assoc()) {
                                          $subject = ucwords($row['subject']);
                                          $selected = in_array($row['id'], $subject_ids) ? 'selected' : '';
                                          echo "<option value='{$row['id']}' {$selected}>{$subject}</option>";
                                      }
                                      ?>
                                  </select>
                              </div>
                          </div>
                      </div>
                      <input type="hidden" name="role" value="teacher">
                  </div>
              </div>
          </form>
      </div>
      <div class="card-footer border-top border-info">
          <div class="d-flex w-100 justify-content-center align-items-center">
              <button class="btn btn-flat bg-gradient-primary mx-2" form="manage-teacher">Save</button>
              <a class="btn btn-flat bg-gradient-secondary mx-2" href="./index.php?page=teacher_list">Cancel</a>
          </div>
      </div>
  </div>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Please select",
        width: '100%'
    });
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

    $('#manage-teacher').submit(function(e) {
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
            saveTeacher();
          }
        });
      } else {
        saveTeacher();
      }
    });

    function saveTeacher() {
      $.ajax({
        url: 'ajax.php?action=save_teacher',
        type: 'POST',
        data: new FormData($('#manage-teacher')[0]),
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
          console.log(response);
          let res = JSON.parse(response);
          if (res.status == 1) {
            alert_toast(res.message, "success");
            setTimeout(function() {
              location.href = 'index.php?page=teacher_list';
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