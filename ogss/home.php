<?php
  include('db_connect.php');
  include_once('header.php');

  $id = $_SESSION['login_id'];
?>
<!-- Admin controls -->
<?php if($_SESSION['login_role'] == 'admin'): ?>
        <div class="row">
          <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Students</span>
                <span class="info-box-number">
                  <?php echo $conn->query("SELECT * FROM students")->num_rows; ?>
                </span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Teachers</span>
                <span class="info-box-number">
                  <?php echo $conn->query("SELECT * FROM teachers")->num_rows; ?>
                </span>
              </div>
            </div>
          </div>
           <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-th-list"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Classes</span>
                <span class="info-box-number">
                  <?php echo $conn->query("SELECT * FROM classes")->num_rows; ?>
                </span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-book"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Subject</span>
                <span class="info-box-number">
                  <?php echo $conn->query("SELECT * FROM subjects")->num_rows; ?>
                </span>
              </div>
            </div>
          </div>
      </div>

<!-- Student controls -->
<?php elseif($_SESSION['login_role'] == 'student'): ?>
  <div class="container-fluid">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          Welcome <b><?php echo ucwords($_SESSION['login_username']) ?></b>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card col-md-4">
        <div class="card-body">
          <form id="vsr-frm">
            <div class="form-group">
              <label for="reg_no" class="control-label text-dark">Student Reg No:</label>
              <input type="text" id="reg_no" name="reg_no" class="form-control form-control-sm">
            </div>
            <button type="button" class="btn btn-primary" id='submit' onclick="$('#vsr-frm').submit()">View Result</button>
          </form>
        </div>
      </div>
    </div>
  </div>

<!-- Teacher controls -->
<?php elseif($_SESSION['login_role'] == 'teacher'): ?>
<div class="container-fluid">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        Welcome <?php echo $_SESSION['login_username'] ?>!
      </div>
    </div>
  </div>
</div>

<?php endif; ?>


<script type="text/javascript">
  $('#vsr-frm').submit(function(e){
    e.preventDefault()
   start_load()
    if($(this).find('.alert-danger').length > 0 )
      $(this).find('.alert-danger').remove();
    $.ajax({
      url:'ajax.php?action=login2',
      method:'POST',
      data:$(this).serialize(),
      error:err=>{
        console.log(err)
        end_load()
      },
      success:function(resp){
        if(resp == 1){
          location.href ='./index.php?page=view_result';
        }else{
          $('#vsr-frm').prepend('<div class="alert alert-danger">Student ID # is incorrect.</div>')
           end_load()
        }
      }
    })
  })
</script>