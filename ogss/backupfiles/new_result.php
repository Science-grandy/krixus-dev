<?php
	if(!isset($conn)) {
		include 'db_connect.php';
	}

	// Fetch current term and session IDs
	$currentTermResult = $conn->query("SELECT id FROM terms ORDER BY id DESC LIMIT 1");
	$currentSessionResult = $conn->query("SELECT id FROM sessions ORDER BY id DESC LIMIT 1");

	if ($currentTermResult->num_rows > 0) {
    $termRow = $currentTermResult->fetch_assoc();
    $term_id = $termRow['id'];
  }
  if ($currentSessionResult->num_rows > 0) {
    $sessionRow = $currentSessionResult->fetch_assoc();
    $session_id = $sessionRow['id'];
  }
  if ($_SESSION['login_role'] == 'teacher') {
	  $loggedInUser = $_SESSION['login_id'];
	  $query = $conn->query("SELECT t.*, tsc.subject_id as subject_id FROM teachers t INNER JOIN teacher_subject_class tsc ON tsc.teacher_id = t.id WHERE user_id='$loggedInUser'");

	  if ($query) {
      $result = $query->fetch_assoc();
      if ($result) {
          $teachers_id = $result['id'];
          $teacher_subject_id = $result['subject_id'];
      }
    }
  }

  // Check if the student id is set to get the students name
  if (isset($s_id)) {
	  $query = $conn->query("SELECT concat(firstname, ' ', lastname, ' ', middlename) as student_name FROM students WHERE id='$s_id'")->fetch_assoc();
	  $student_name = $query['student_name'];
	  $class_id_edit = $class_id;
	  $class_id_edit_name = $class;
  }
?>

<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<?php if (!isset($teacher_subject_id) && $_SESSION['login_role'] != 'admin') {
				echo '<div class="alert alert-danger text-center py-2">You have not been assigned to a class and subject. Please meet admin to rectify.</div>';
			} ?>
		   <!-- Form to create new result for students -->
		   <form action="" id="manage-result">
	        <input type="hidden" id="edit_id" name="id" value="<?php echo isset($id) ? $id : '' ?>">
	        <input type="hidden" id="student_name" name="student_name" value="<?php if(isset($student_name)) { echo "$student_name"; } ?>">
	        <input type="hidden" name="term_id" value="<?php echo $term_id ?>">
	        <input type="hidden" name="session_id" value="<?php echo $session_id ?>">
	        <div class="row justify-content-center">
	          <div class="col-md-2">
	          	<div class="form-group">
	          		<label for="classSelect">Select Class:</label>
	          		<?php if (isset($teacher_subject_id)) { ?>
	          			<select class="form-control" id="classSelect" name="class_id" onchange="fetchStudents(this.value, <?php echo $teacher_subject_id ?>)">
	          		<?php } else { ?>
	          			<select class="form-control" id="classSelect" name="class_id" onchange="fetchStudents(this.value)">
	          		<?php } ?>
	          			<?php 
	          			// Get the class details using the relation of the teacher-subject-class, else just get all the classes ordered by their names
	          			if (isset($teachers_id)) {
	          				$query = $conn->query("SELECT DISTINCT c.id, c.level, c.section FROM classes c INNER JOIN teacher_subject_class tsc ON tsc.class_id = c.id WHERE tsc.teacher_id = $teachers_id ORDER BY CONCAT(c.level, '-', c.section) ASC");
	          			} else {
	          				$query = $conn->query("SELECT id, level, section FROM classes ORDER BY CONCAT(level, section) ASC");
	          			}
	          			
	          			$defaultClass = '';
	          			if (isset($query) && $query->num_rows > 0) {
	          				$firstRow = true;
	          				while ($row = $query->fetch_assoc()) {
	          					$class_id = $row['id'];
	          					$class = strtoupper($row['level'].'-'.$row['section']);
	          					if ($firstRow) {
	          						$defaultClass = $class_id;
	          						$firstRow = false;
	          					}
	          			?>
	          			<option value="<?php echo isset($class_id_edit) ? $class_id_edit : $class_id ?>" <?php echo ($class_id == $defaultClass) ? 'selected' : '' ?>><?php echo isset($class_id_edit_name) ? strtoupper($class_id_edit_name) : $class ?></option>
	          			<?php
	          				}
	          			} else {
	          			?>
	          			<option value="">No classes found</option>
	          			<?php
	          			}
	          			?>
	          		</select>
	          	</div>
	          </div>
	          <div class="col-md-4"> 
	            <div id="msg" class=""></div>
	            <div class="form-group">
	                <label for="" class="control-label">Student</label>
	                <select name="student_id" id="student_id" class="form-control select2 select2-sm" required>
	                    <option id="default_option" value="<?= isset($s_id) ? $s_id : '' ?>" selected></option>
	                </select>
	                <small id="smallClass"></small>
	            </div>
	          </div>
	        </div>
	        <hr>
	        <div class="row">
	          <div class="col-md-12">
	            <div class="d-flex justify-content-center align-items-center row">
	            	<div class="form-group col-12 col-md-3">
		                <label for="" class="control-label">Subject</label>
		                <select name="" id="subject_id" class="form-control select2 select2-sm input-sm">
		                  <?php 
		                  	/* Displayed only for teachers */
		                  	if (isset($teachers_id)) {
		                      $subjects_data = $conn->query("SELECT * FROM subjects WHERE id = '$teacher_subject_id' order by subject asc ");
		                      while($row = $subjects_data->fetch_array()):
		                      	$subject_name = $row['subject'];
		                	?>
		                      <option value="<?php echo $row['id'] ?>" data-json='<?php echo json_encode($row) ?>'><?php echo $row['subject_code'].' | '.ucwords($row['subject']) ?></option>
		                  <?php endwhile; } else { ?>
		                  <option></option> 
		                  <!-- For admins -->
		                  <?php 
	                      $classes = $conn->query("SELECT * FROM subjects order by subject asc ");
	                      while($row = $classes->fetch_array()):
		                  ?>
	                      <option value="<?php echo $row['id'] ?>" data-json='<?php echo json_encode($row) ?>'><?php echo $row['subject_code'].' | '.ucwords($row['subject']) ?></option>
		                  <?php endwhile; } ?>
		                </select>
		            </div>
		            <div class="form-group col-6 col-md-2">
		            	<label for="" class="control-label">CA</label>
		            	<input type="text" class="form-control form-control-sm text-left number" id="ca" maxlength="2" placeholder="Continuous Assessment">
		            </div>
		            <div class="form-group col-6 col-md-2">
		            	<label for="" class="control-label">Exam</label>
		            	<input type="text" class="form-control form-control-sm text-left number" id="exam" maxlength="2" placeholder="Examination">
		            </div>
		            <button class="col-12 col-md-1 mb-2 btn btn-sm btn-primary bg-gradient-primary" style="margin-top: 23px" type="button" id="add_total">Add</button>
	            </div>
	        </div>
	    	<hr>
	    	<div class="col-md-8 offset-md-2 table-responsive">
	            <table class="table table-bordered" id="total-list">
	            	<thead>
	            		<tr>
	            			<th>Subject Code</th>
	            			<th>Subject</th>
	            			<th>CA</th>
	            			<th>Exam</th>
	            			<th>Total</th>
	            			<th></th>
	            		</tr>
	            	</thead>
	            	<tbody>
	            		<?php if(isset($id)) { ?>
	            		<?php 
	            			$items=$conn->query("SELECT r.*,s.subject_code,s.subject,s.id as sid FROM result_items r inner join subjects s on s.id = r.subject_id where r.result_id = $id order by s.subject_code asc");

	            			// Query the teachers table to get teachers that teach the particular subject
	            			// Query to get all subject IDs from the results table
	            			$loggedInUser = $_SESSION['login_id'];
	            			$query = $conn->query("SELECT tsc.subject_id FROM teacher_subject_class tsc INNER JOIN teachers t ON t.id = tsc.teacher_id WHERE user_id='$loggedInUser'");
	            			$subject_teacher_ids = [];
	            			while ($row = $query->fetch_array()) {
	            				$subject_teacher_ids[] = $row['subject_id'];
	            			}
	            			while($row = $items->fetch_array()) {
	            		?>	            		
	            		<tr data-id="<?php echo $row['sid'] ?>">
	            			<!-- The delete all input helps to control the deleting of subject results for the current student -->
	            			<td>
	            				<input type="hidden" name="delete_all" value="0">
	            				<input type="hidden" name="subject_id[]" value="<?php echo $row['subject_id'] ?>"><?php echo $row['subject_code'] ?></td>
	            			<td><?php echo ucwords($row['subject']) ?></td>
	            			<td class="text-center"><input type="hidden" name="ca[]" value="<?php echo $row['c_assess'] ?>"><?php echo $row['c_assess'] ?></td>
	            			<td class="text-center"><input type="hidden" name="exam[]" value="<?php echo $row['exam'] ?>"><?php echo $row['exam'] ?></td>
	            			<td class="text-center"><input type="hidden" name="total[]" value="<?php echo $row['total'] ?>"><?php echo $row['total'] ?></td>
	            			<!-- Show the delete button only for subject teachers -->
	            			<?php
	            				if (isset($subject_teacher_ids) && (count($subject_teacher_ids) > 0)) {
	            					$element_class = in_array($row['subject_id'], $subject_teacher_ids) ? 'd-block' : 'd-none';
	            			?>
	            			<td class="text-center"><button class="mx-auto btn btn-sm btn-danger <?php echo $element_class ?>" type="button" id="delete_subject"><i class="fa fa-times"></i></button></td>
		            		<?php } else { ?>
		            			<td class="text-center"><button class="mx-auto btn btn-sm btn-danger" type="button" id="delete_subject"><i class="fa fa-times"></i></button></td>
		            		<?php } ?>
	            		</tr>
	            		<?php } ?>
	            		<script>
	            			$(document).ready(function(){
	            				calc_ave()
	            			})
	            		</script>
	            		<?php } ?>

	            	</tbody>
	            	<tfoot>
	            		<tr>
	            			<th colspan="4">Student Average</th>
	            			<th id="average" class="text-center"></th>
	            			<th></th>
	            		</tr>
	            	</tfoot>
	            </table>
	            <input type="hidden" name="student_average" value="<?php echo isset($student_average) ? $student_average : '' ?>">
	          </div>
	        </div>
	      </form>
  	</div>
  	<div class="card-footer border-top border-info">
  		<div class="d-flex w-100 justify-content-center align-items-center">
  			<button class="btn btn-flat  bg-gradient-primary mx-2" form="manage-result">Save</button>
  			<a class="btn btn-flat bg-gradient-secondary mx-2" href="./index.php?page=results">Cancel</a>
  		</div>
  	</div>
	</div>
</div>
<style>
	#add_total {
		margin-top: 15px;
	}
</style>
<script>
	document.addEventListener('DOMContentLoaded', function() {
	    var classSelect = document.getElementById('classSelect');
	    var studentName = document.getElementById('student_name');
	    <?php if (isset($teacher_subject_id)) { ?>
	    	var teacher_subject_id = <?php echo $teacher_subject_id ?>;
	    <?php } else { ?>
	    	var teacher_subject_id = "";
	    <?php } ?>

	    if (classSelect.value) {
	    	if (studentName.value != "") {
	    		$('#default_option').text(studentName.value);
	    	}
	    	else {
	        	fetchStudents(classSelect.value, teacher_subject_id);
	    	}
	    }
	});
	function fetchStudents(class_id, subject_id) {
	    var studentSelect = document.getElementById('student_id');
	    studentSelect.innerHTML = '<option></option>'; // Clear current options
	    if (class_id) {
	    start_load();
        $.ajax({
            url: 'ajax.php?action=fetch_students',
            type: 'POST',
            data: { class_id: class_id, subject_id: subject_id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    const students = response.data;
                    students.forEach(function(student) {
                        var option = document.createElement('option');
                        var class_name = student.class.toUpperCase();
                        option.value = student.id;
                        option.text = `${student.reg_no} | ${class_name} : ${student.name}`;
                        option.setAttribute('data-class_id', student.class_id);
                        option.setAttribute('data-class', class_name);
                        studentSelect.appendChild(option); // Corrected selector
                    });
                } else {
                    console.error('Error:', response.message);
                }
                end_load();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching students:', error);
                end_load();
            }
        });
    }
	}

	$('#student_id').change(function(){
		var class_id = $('#student_id option[value="'+$(this).val()+'"]').attr('data-class_id');
		var _class = $('#student_id option[value="'+$(this).val()+'"]').attr('data-class');
		$('[name="class_id"]').val(class_id)
		$('#class').text("Current Class: "+_class);
	})

	$('#add_total').click(function(){
		var subject_id = $('#subject_id').val()
		var ca = $('#ca').val();
		var exam = $('#exam').val();
		if(subject_id == '' && ca == '' && exam == ''){
			alert_toast("Please select subject & enter ca/exam","error");
			return false;
		}
		else if(ca > 30 || exam > 70) {
			alert_toast("CA or Exam value is higher than allowed!","error");
			return false;
		}
		var total = parseInt(ca) + parseInt(exam);
		var sData = $('#subject_id option[value="'+subject_id+'"]').attr('data-json')
			sData = JSON.parse(sData)
		if($('#total-list tr[data-id="'+subject_id+'"]').length > 0){
			alert_toast("Subject already on the list.","error");
			return false;
		}
		var tr = $('<tr data-id="'+subject_id+'"></tr>')
		tr.append('<td><input type="hidden" name="subject_id[]" value="'+subject_id+'">'+sData.subject_code+'</td>')
		tr.append('<td>'+sData.subject+'</td>')
		tr.append('<td class="text-center"><input type="hidden" name="ca[]" value="'+ca+'">'+ca+'</td>')
		tr.append('<td class="text-center"><input type="hidden" name="exam[]" value="'+exam+'">'+exam+'</td>')
		tr.append('<td class="text-center"><input type="hidden" name="total[]" value="'+total+'">'+total+'</td>')
		tr.append('<td class="text-center"><button class="btn btn-sm btn-danger" type="button" id="delete_subject"><i class="fa fa-times"></i></button></td>')
		$('#total-list tbody').append(tr)
		$('#subject_id').val('').trigger('change')
		$('#ca').val('')
		$('#exam').val('')
		calc_ave()

	})
	function calc_ave(){
		var total = 0;
		var i = 0;
		$('#total-list [name="total[]"]').each(function(){
			i++;
			total = total + parseFloat($(this).val())
		})
		$('#average').text(parseFloat(total/i).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2}))
		$('[name="student_average"]').val(parseFloat(total/i).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2}))
	}

	$(document).on('click', '#delete_subject', function() {
	    $('input[name="delete_all"]').val(1); // Set the delete_all flag to 1
	    $(this).closest('tr').remove(); // Remove the closest table row
	    calc_ave(); // Recalculate the average
	});

	$('#manage-result').submit(function(e){
    e.preventDefault();

    if ($('[name="student_average"]').val() == "" || $('[name="student_average"]').val() == "NaN") {
        return 0;
    } else {
        start_load();
        $.ajax({
          url: 'ajax.php?action=save_result',
          data: new FormData($(this)[0]),
          cache: false,
          contentType: false,
          processData: false,
          method: 'POST',
          success: function(resp) {
              var response = JSON.parse(resp);
              if (response.status == 1) {
                  alert_toast(response.message, "success");
                  setTimeout(function(){
                      location.href = 'index.php?page=new_result';
                  }, 2000);
              } else if (response.status == 2) {
                  $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Student Code already exists.</div>');
              } else {
                  $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + response.message + '</div>');
              }
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