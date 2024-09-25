<?php
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
include 'db_connect.php';

// Query the database for the student role
if ($_SESSION['login_role'] == 'student') {
	$qry = $conn->query("SELECT r.*,concat(s.firstname,' ',s.middlename,' ',s.lastname) as name,s.reg_no,concat(c.level,'-',c.section) as class,s.gender FROM results r INNER JOIN classes c on c.id = r.class_id INNER JOIN students s on s.id = r.student_id WHERE r.status = 'published' AND s.user_id = ".$_SESSION['login_id'])->fetch_array();
}
// Query the datbase for the teacher role
elseif ($_SESSION['login_role'] == 'teacher') {
	// Fetch the teacher's class id
	$teacher_class = $conn->query("SELECT tsc.class_id FROM teacher_subject_class tsc INNER JOIN teachers t ON t.id=tsc.teacher_id WHERE t.user_id = ".$_SESSION['login_id'])->fetch_assoc();
	$class_id = $teacher_class['class_id'];
	 
	$qry = $conn->query("SELECT r.*, concat(s.firstname, ' ', s.middlename, ' ', s.lastname) as name, s.reg_no, concat(c.level, '-', c.section) as class, s.gender FROM results r INNER JOIN classes c ON c.id = r.class_id INNER JOIN students s ON s.id = r.student_id WHERE r.id = ".$_GET['id']." ORDER BY concat(s.firstname, ' ', s.middlename, ' ', s.lastname) ASC")->fetch_array();
}
// Query the database for admins
else {
	$qry = $conn->query("SELECT r.*,concat(s.firstname,' ',s.middlename,' ',s.lastname) as name,s.reg_no,concat(c.level,'-',c.section) as class,s.gender FROM results r inner join classes c on c.id = r.class_id inner join students s on s.id = r.student_id where r.id = ".$_GET['id'])->fetch_array();
}

if (!$qry) {
?>
<div class="container table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>No result available</th>
			</tr>
		</thead>
	</table>
</div>
<?php 
}
else {
	foreach($qry as $k => $v){
		$$k = $v;
	}

	// Function to add ordinal suffix to positions
	function ordinal_suffix($number) {
	    if ($number % 100 >= 11 && $number % 100 <= 13) {
	        return $number . 'th';
	    }
	    switch ($number % 10) {
	        case 1:  return $number . 'st';
	        case 2:  return $number . 'nd';
	        case 3:  return $number . 'rd';
	        default: return $number . 'th';
	    }
	}

	// Step 1: Fetch the current student's results
	if ($_SESSION['login_role'] == 'student') {
	    $student_id_query = $conn->query("SELECT id FROM students WHERE user_id = " . $_SESSION['login_id']);
	    $student_id = $student_id_query->fetch_assoc()['id'];
	    $student_results = $conn->query("SELECT r.*, s.subject_code, s.subject, s.parent_id, r.total, r.c_assess, r.exam, ss.name as session_name, t.name as term_name FROM result_items r INNER JOIN subjects s ON s.id = r.subject_id INNER JOIN results res ON res.id = r.result_id INNER JOIN sessions ss ON ss.id = res.session_id INNER JOIN terms t ON t.id = res.term_id WHERE res.student_id = $student_id ORDER BY s.subject_code ASC");
	} else {
	    $student_results = $conn->query("SELECT r.*, s.subject_code, s.subject, s.parent_id, r.total, r.c_assess, r.exam, ss.name as session_name, t.name as term_name FROM result_items r INNER JOIN subjects s ON s.id = r.subject_id INNER JOIN results res ON res.id = r.result_id INNER JOIN sessions ss ON ss.id = res.session_id INNER JOIN terms t ON t.id = res.term_id WHERE r.result_id = $id ORDER BY s.subject_code ASC");
	}

	// Step 2: Fetch all results for the class for ranking and statistics
	$class_id_query = $conn->query("SELECT class_id FROM results WHERE id = $id");
	$class_id = $class_id_query->fetch_assoc()['class_id'];

	$class_results = $conn->query("SELECT r.subject_id, r.total, s.subject_code FROM result_items r INNER JOIN results res ON r.result_id = res.id INNER JOIN subjects s ON s.id = r.subject_id WHERE res.class_id = $class_id");

	// function process_parent_subjects($conn, $student_id) {
	//     // Fetch the parent subjects and their child subjects' scores
	//     $query = "SELECT ps.id as parent_id, ps.subject as parent_subject, ps.subject_code as parent_code, cs.subject_code, cs.subject, r.c_assess, r.exam, r.total FROM subjects ps LEFT JOIN subjects cs ON cs.parent_id = ps.id LEFT JOIN result_items r ON r.subject_id = cs.id LEFT JOIN results res ON res.id = r.result_id WHERE res.student_id = $student_id";

	//     $result = $conn->query($query);
	//     $parent_scores = [];
	//     $child_count = []; // To store the count of child subjects for each parent

	//     // Sum the child subjects' scores and store them in the parent subject's record
	//     while ($row = $result->fetch_assoc()) {
	//         $parent_id = $row['parent_id'];

	//         if (!isset($parent_scores[$parent_id])) {
	//             $parent_scores[$parent_id] = [
	//                 'subject' => $row['parent_subject'],
	//                 'subject_code' => $row['parent_code'],
	//                 'c_assess' => 0,
	//                 'exam' => 0,
	//                 'total' => 0
	//             ];
	//             $child_count[$parent_id] = 0; // Initialize child count for this parent
	//         }
	//         $parent_scores[$parent_id]['c_assess'] += $row['c_assess'];
	//         $parent_scores[$parent_id]['exam'] += $row['exam'];
	//         $parent_scores[$parent_id]['total'] += $row['total'];
	//         $child_count[$parent_id]++; // Increment child count for this parent
	//     }

	//     // Calculate the average scores for each parent subject
	//     foreach ($parent_scores as $parent_id => $scores) {
	//         $count = $child_count[$parent_id];
	//         if ($count > 0) { // Avoid division by zero
	//             $parent_scores[$parent_id]['c_assess'] /= $count;
	//             $parent_scores[$parent_id]['exam'] /= $count;
	//             $parent_scores[$parent_id]['total'] /= $count;
	//         }
	//     }

	//     return $parent_scores;
	// }

	// Process parent subjects
	// $parent_subjects = process_parent_subjects($conn, $student_id);

	// Initialize arrays
	$scores = [];
	$class_scores = [];
	$subject_details = [];

	// Fetch and store class results
	while ($row = $class_results->fetch_assoc()) {
	    $subject_code = $row['subject_code'];
	    $class_scores[$subject_code][] = $row['total'];
	}

	// This variable gets the number of subjects the student offered
	$count_sub = 0;

	// Step 3: Process student results and calculate statistics
	while ($row = $student_results->fetch_assoc()) {
	    $session_name = $row['session_name'];
	    $term_name = $row['term_name'];
	    $subject_code = $row['subject_code'];
	    $parent_id = $row['parent_id'];
	    
	    // Increment the subject count for the student
	    if (!is_null($subject_code)) {
	        $count_sub++;
	    }

	    // Process individual subject scores
	    $scores[$subject_code]['subject_code'] = $subject_code;
	    $scores[$subject_code]['subject'] = $row['subject'];
	    $scores[$subject_code]['c_assess'] = $row['c_assess'];
	    $scores[$subject_code]['exam'] = $row['exam'];
	    $scores[$subject_code]['total'] = $row['total'];

	    // Calculate highest, lowest, average
	    if (!isset($scores[$subject_code]['highest_score'])) {
	        $scores[$subject_code]['highest_score'] = max($class_scores[$subject_code]);
	        $scores[$subject_code]['lowest_score'] = min($class_scores[$subject_code]);
	        $scores[$subject_code]['class_average'] = array_sum($class_scores[$subject_code]) / count($class_scores[$subject_code]);
	    }
	}

	// Add parent subject scores to the $scores array
	// foreach ($parent_subjects as $parent_code => $totals) {
	//     $scores[$parent_code] = $totals;
	// }

	// Calculate rank for each subject
	foreach ($class_scores as $subject_code => $totals) {
	    arsort($totals);
	    $rank = 1;
	    foreach ($totals as $total) {
	        foreach ($scores as $code => $data) {
	            if ($code == $subject_code && $data['total'] == $total) {
	                $scores[$subject_code]['rank'] = $rank;
	            }
	        }
	        $rank++;
	    }
	}

	// Calculate overall positions for each student in the class
	$averages_query = $conn->query("SELECT student_id, student_average FROM results WHERE class_id = $class_id ORDER BY student_average DESC");

	$positions = [];
	$student_averages = [];
	while ($row = $averages_query->fetch_assoc()) {
	    $student_averages[$row['student_id']] = $row['student_average'];
	}

	// Sort students by average score
	arsort($student_averages);

	// Assign positions with ordinal suffix
	$rank = 1;
	$position_counter = 1; // Counter for actual ranking position
	$prev_average = null;
	foreach ($student_averages as $student_rank_id => $average) {
	    if ($average !== $prev_average) {
	        $positions[$student_rank_id] = ordinal_suffix($rank);
	        $prev_average = $average;
	        $position_counter = $rank; // Update position counter for the next unique average
	    } else {
	        $positions[$student_rank_id] = ordinal_suffix($position_counter); // Same rank for tie
	    }
	    $rank++;
	}

	// Function to calculate the grades and description
	function get_grade($score) {
	    if ($score >= 90 && $score <= 100) {
	        return ['grade' => 'A1', 'description' => 'Excellent'];
	    } elseif ($score >= 80 && $score <= 89) {
	        return ['grade' => 'B2', 'description' => 'Very Good'];
	    } elseif ($score >= 75 && $score <= 79) {
	        return ['grade' => 'B3', 'description' => 'Good'];
	    } elseif ($score >= 70 && $score <= 74) {
	        return ['grade' => 'C4', 'description' => 'Upper Credit'];
	    } elseif ($score >= 65 && $score <= 69) {
	        return ['grade' => 'C5', 'description' => 'Credit'];
	    } elseif ($score >= 60 && $score <= 64) {
	        return ['grade' => 'C6', 'description' => 'Lower Credit'];
	    } elseif ($score >= 55 && $score <= 59) {
	        return ['grade' => 'D7', 'description' => 'Pass'];
	    } elseif ($score >= 45 && $score <= 54) {
	        return ['grade' => 'E8', 'description' => 'Lower Pass'];
	    } elseif ($score >= 0 && $score <= 44) {
	        return ['grade' => 'F9', 'description' => 'Fail'];
	    } else {
	        return ['grade' => 'Invalid', 'description' => 'Invalid score'];
	    }
	}

	// Get the total number of students in the class
	$class_total_query = $conn->query("SELECT COUNT(*) AS total_students FROM students WHERE class_id = $class_id");
	$class_total = $class_total_query->fetch_assoc()['total_students'];

	$student_rank_id_query = $conn->query("SELECT student_id FROM results WHERE id=$id");
	$student_rank_id = $student_rank_id_query->fetch_assoc()['student_id'];


 ?>
<div class="container" id="printable">
	<table class="t_header" width="100%">
		<tr>
			<td width="40%">Reg Number: <b><?php echo $reg_no ?></b></td>
			<td width="20%">Class: <b><?php echo strtoupper($class) ?></b></td>
			<td width="40%">Position: <b><?php echo $positions[$student_rank_id] ?></b></td>
		</tr>
		<tr>
			<td width="40%">Student Name: <b><?php echo ucwords($name) ?></b></td>
			<td width="20%">Gender: <b><?php echo ucwords($gender) ?></b></td>
			<td width="40%">Session: <b><?php echo ucwords($term_name.' - '.$session_name) ?></b></td>
		</tr>
	</table>
	<hr>
	<div class="table-responsive">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Subject Code</th>
					<th>Subject</th>
					<th>CA</th>
					<th>Exam</th>
					<th>Total</th>
					<th>Grade</th>
					<th>Remark</th>
					<th>Rank</th>
					<th>No. of Students<br>in subject class</th>
					<th>Highest<br>in class</th>
					<th>Lowest<br>in class</th>
					<th>Class<br>Average</th>
				</tr>
			</thead>
			<tbody>
				<?php
				// Output the results for the current student
				foreach ($scores as $subject_code => $data) {
				   $total = $data['total'];
				   $details = get_grade($total);
				?>
			   <tr>
			      <td><?php echo $data['subject_code'] ?></td>
			      <td><?php echo ucwords($data['subject']) ?></td>
			      <td class="text-center"><?php echo number_format($data['c_assess']) ?></td>
			      <td class="text-center"><?php echo number_format($data['exam']) ?></td>
			      <td class="text-center"><?php echo number_format($total) ?></td>
			      <td class="text-center"><?php echo $details['grade'] ?></td>
			      <td class="text-center"><?php echo $details['description'] ?></td>
			      <td class="text-center"><?php echo isset($data['rank']) ? $data['rank'] : '-' ?></td>
			      <td class="text-center"><?php echo $class_total ?></td>
			      <td class="text-center"><?php echo $data['highest_score'] ?></td>
			      <td class="text-center"><?php echo $data['lowest_score'] ?></td>
			      <td class="text-center"><?php echo round($data['class_average'], 2) ?></td>
			   </tr>
				<?php
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3">Student Average</th>
					<th class="text-center"><?php  echo number_format($student_average,2) ?></th>
					<th colspan="2">Result Status</th>
					<?php
						if($student_average >= 40) {
							echo '<th class="text-center bg-success">'.strtoupper('passed').'</th>';
						}
						else {
							echo '<th class="text-center bg-danger">'.strtoupper('failed').'</th>';
						}
					?>
				</tr>
			</tfoot>
		</table>
	</div>

	<!-- This section automates the form teacher, and principal's responses to the student's result -->
	<?php 
		$p_comment = "";
		$ct_comment = "";
		switch (true) {
			case ($student_average >= 90):
				$p_comment = "Excellent Performance. Bravo!!!";
				$ct_comment = "Outstanding performance! Keep up the excellent work.";
				break;
			case ($student_average >= 80):
				$p_comment = "Fantastic! You did very well.";
				$ct_comment = "Very good effort! You're on the right track to excellence.";
				break;
			case ($student_average >= 75):
				$p_comment = "Amazing! Keep it up.";
				$ct_comment = "Good job! Your consistent efforts are paying off.";
				break;
			case ($student_average >= 70):
				$p_comment = "Keep it up kiddo!";
				$ct_comment = "Good attempt! There's still need for further improvement.";
				break;
			case ($student_average >= 65):
				$p_comment = "More work, and you'll be a star!";
				$ct_comment = "Credit-worthy performance! Try harder next time.";
				break;
			case ($student_average >= 55):
				$p_comment = "You can do better! Well done.";
				$ct_comment = "Good effort! A little more focus will yield even better results.";
				break;
			case ($student_average >= 45):
				$p_comment = "Study harder! You've got the brains.";
				$ct_comment = "You passed, but there’s potential for significant improvement.";
				break;
			case ($student_average >= 0 && $student_average <= 44):
				$p_comment = "Don't give up! You're smart.";
				$ct_comment = "Poor result, more effort is needed.";
				break;
			default:
				$p_comment = "You did okay!";
				$ct_comment = "Let’s work together to improve your understanding and achieve better results.";
				break;
		}

		$total_query = $conn->query("SELECT SUM(total) as total_score FROM result_items r INNER JOIN results rs ON rs.id = r.result_id  WHERE rs.student_id = '$student_id' GROUP BY '$student_id'")->fetch_assoc();
		$total_score = $total_query['total_score'];
	?>

	<div class="table-responsive">
		<table class="table mt-5 t_offical">
			<thead>
				<tr>
					<th>Teacher's Comment</th>
					<th>Principal's Comment</th>
					<th>Principal's Signature</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $ct_comment ?></td>
					<td><?php echo $p_comment ?></td>
					<td style="position: relative;"><img src="./assets/uploads/principalsign.svg" width="80" style="position: absolute; bottom: -30px; left: 15px;"></td>
				</tr>
			</tbody>
		</table>
		<table width="100%" class="result_notes" style="margin-top: 20px; padding: 20px;">
			<tr>
				<th width="53%">Result Analysis (Criteria for passing)</th>
				<th width="47%">Notice</th>
			</tr>
			<tr>
				<td width="53%">Minimum subject to offer is 19, you offered <strong><?php echo $count_sub ?></strong></td>
				<td width="47%">Vacation Date: Friday 24th July, 2024</td>
			</tr>
			<tr>
				<td width="53%">Promotion score is 40%, you scored <strong><?php echo $student_average ?>%</strong></td>
				<td width="47%">Resumption Date: Monday 9th September, 2024</td>
			</tr>
			<tr>
				<td width="53%">Maximum marks obtainable: <?php echo ($count_sub * 100) ?> | Total marks obtained: <strong><?php echo $total_score ?></strong></td>
			</tr>
		</table>
	</div>
</div>
<div class="modal-footer display p-0 mx-5">
        <button type="button" class="btn btn-success" id="print"><i class="fa fa-print"></i> Print</button>
        <?php if($_SESSION['login_role'] != 'student') {	
        ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

<?php 
	}
}
?>
</div>
<style>
	#uni_modal .modal-footer{
		display: none;
	}
	#uni_modal .modal-footer.display{
		display: flex;
	}
</style>
<noscript>
	<style>
		table.table, table.t_header, .result_notes {
			width:100%;
			border-collapse: collapse;
			font-size: 11px;
			text-align: left;
			font-family: 'Mulish', sans-serif;
		}
		table.table tr,table.table th, table.table td{
			padding: 2px 5px;
			border:1px solid rgba(0, 0, 0, .4);
		}
		@media print {
         body {
            background-image: url('assets/uploads/result_background.jpg') !important;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
         }
      }
	</style>
	<div style="font-family: 'Mulish', sans-serif; display: flex; justify-content:center; align-items:center; gap: 3px; width: 100%; margin-bottom: 20px;">
		<img src='assets/uploads/logo.svg' width='40'><br>
		<b><?php echo $school_name ?></b><br><br>
	</div>
</noscript>
<script>
	$('#print').click(function(){
		start_load()
		var ns = $('noscript').clone()
		var content = $('#printable').clone()
		ns.append(content)
		var nw = window.open('','','height=700,width=900')
		nw.document.write(ns.html())
		nw.document.close()
		nw.print()
		setTimeout(function(){
			nw.close()
			end_load()
		},750)
	})
</script>