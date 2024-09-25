<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
	   	include 'db_connect.php';
	    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}
	function sanitize_input($input) {
	    if (is_array($input)) {
	        // If $input is an array, sanitize each element recursively
	        foreach ($input as $key => &$value) {
	            $value = $this->sanitize_input($value); 
	        }
	    } else {
	        // If $input is not an array, sanitize it as a string
	        $input = trim($input);
	        $input = stripslashes($input);
	        $input = htmlspecialchars($input);
	    }
	    return $input;
	}
	function login(){
		extract($_POST);
			// Original query
			// $qry = $this->db->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where username = '".$username."' and password = '".md5($password)."' and type= 1");

			$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."'");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			return 1;
		}else{
			return 0;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../demolaunch.php");
	}
	function login2(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM students where reg_no = '".$reg_no."' and user_id = '".$_SESSION['login_id']."'");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['rs_'.$key] = $value;
			}
				return 1;
		}else{
			return 0;
		}
	}
	function save_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!empty($cpass) && !empty($password)){
					$data .= ", password=md5('$password') ";

		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			return 1;
		}
	}
	function signup(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
				if($k =='password'){
					if(empty($v))
						continue;
					$v = md5($v);

				}
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");

		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			if(empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if(!in_array($key, array('id','cpass','password')) && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
					$_SESSION['login_id'] = $id;
			return 1;
		}
	}

	function update_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','table')) && !is_numeric($k)){
				if($k =='password')
					$v = md5($v);
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if($_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			foreach ($_POST as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if($_FILES['img']['tmp_name'] != '')
			$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function check_username() {
	    extract($_POST);
	    
	    $username = $this->sanitize_input($username);
	    
	    $userResult = $this->db->query("SELECT id FROM users WHERE username = '$username'");
	    
	    if ($userResult->num_rows > 0) {
	        echo json_encode(["status" => 0, "message" => "Username already exists"]);
	    } else {
	        echo json_encode(["status" => 1, "message" => "Username available"]);
	    }
	}
	function save_system_settings(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k => $v){
			if(!is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if($_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", cover_img = '$fname' ";

		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set $data where id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set $data");
		}
		if($save){
			foreach($_POST as $k => $v){
				if(!is_numeric($k)){
					$_SESSION['system'][$k] = $v;
				}
			}
			if($_FILES['cover']['tmp_name'] != ''){
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	function save_image(){
		extract($_FILES['file']);
		if(!empty($tmp_name)){
			$fname = strtotime(date("Y-m-d H:i"))."_".(str_replace(" ","-",$name));
			$move = move_uploaded_file($tmp_name,'../assets/uploads/'. $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path =explode('/',$_SERVER['PHP_SELF']);
			$currentPath = '/'.$path[1]; 
			if($move){
				return $protocol.'://'.$hostName.$currentPath.'/assets/uploads/'.$fname;
			}
		}
	}
	function save_class(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM classes where level ='$level' and section = '$section' and id != '$id' ");
		if($chk->num_rows > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO classes set $data");
		}else{
			$save = $this->db->query("UPDATE classes set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_class(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM classes where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_subject(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM subjects where subject_code ='$subject_code' and id != '$id' ");
		if($chk->num_rows > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO subjects set $data");
		}else{
			$save = $this->db->query("UPDATE subjects set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_subject(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM subjects where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_student() {
	    extract($_POST);

	    // Sanitize inputs
	    foreach($_POST as $k => $v){
	        $$k = $this->sanitize_input($v);
	    }

	    // Hash the password
	    $hashPassword = md5($password);

	    // Begin a transaction to ensure atomicity
	    $this->db->begin_transaction();

	    try {
	        // Check if student exists
	        $studentResult = $this->db->query("SELECT user_id FROM students WHERE id = '$id'");
	        $user_id = null;

	        if ($studentResult->num_rows > 0) {
	            // Student exists, update user and student information
	            $studentRow = $studentResult->fetch_assoc();
	            $user_id = $studentRow['user_id'];

	            $updateUser = $this->db->query("UPDATE users SET password = '$hashPassword', username = '$username', role = '$role' WHERE id = '$user_id'");
	            $updateStudent = $this->db->query("UPDATE students SET reg_no = '$reg_no', firstname = '$firstname', middlename = '$middlename', lastname = '$lastname', gender = '$gender', address = '$address', class_id = '$class_id' WHERE id = '$id'");
	        } else {
	            // Student does not exist, insert new user and student
	            $saveUser = $this->db->query("INSERT INTO users (username, password, role) VALUES ('$username', '$hashPassword', '$role')");
	            $user_id = $this->db->insert_id;
	            $saveStudent = $this->db->query("INSERT INTO students (reg_no, user_id, firstname, middlename, lastname, gender, address, class_id) VALUES ('$reg_no', '$user_id', '$firstname', '$middlename', '$lastname', '$gender', '$address', '$class_id')");
	        }

	        // Commit the transaction
	        $this->db->commit();
	        return json_encode(['status' => 1, 'message' => 'Data successfully saved']);
	    } catch (Exception $e) {
	        // Rollback the transaction if something goes wrong
	        $this->db->rollback();
	        return json_encode(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage()]);
	    }
	}
	function delete_student(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM students where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_teacher() {
	    extract($_POST);

	    // Sanitize inputs
	    foreach($_POST as $k => $v){
	        $$k = $this->sanitize_input($v);
	    }

	    // Hash the password
	    $hashPassword = md5($password);

	    // Begin a transaction to ensure atomicity
	    $this->db->begin_transaction();

	    try {
	        // Check if teacher exists
	        $teacherResult = $this->db->query("SELECT user_id FROM teachers WHERE id = '$id'");
	        $user_id = null;

	        // Clear existing assignments only if teacher exists
	        if ($teacherResult->num_rows > 0) {
	            // Teachers exists, update user and teacher information
	            $teacherRow = $teacherResult->fetch_assoc();
	            $user_id = $teacherRow['user_id'];

	            // Update user
	            $updateUser = $this->db->query("UPDATE users SET password = '$hashPassword', username = '$username', role = '$role' WHERE id = '$user_id'");

	            // Update teacher
	            $updateTeacher = $this->db->query("UPDATE teachers SET firstname = '$firstname', middlename = '$middlename', lastname = '$lastname', teacher_code = '$teacher_code' WHERE id = '$id'");

	            // Clear existing assignments
	            $this->db->query("DELETE FROM teacher_subject_class WHERE teacher_id = $id");

	            // Insert new assignments
	            foreach ($subject_ids as $subject_id) {
	                foreach ($class_ids as $class_id) {
	                    $this->db->query("INSERT INTO teacher_subject_class (teacher_id, subject_id, class_id) VALUES ($id, $subject_id, $class_id)");
	                }
	            }
	        } else {
	            // Teacher does not exist, insert new user and teacher
	            $saveUser = $this->db->query("INSERT INTO users (username, password, role) VALUES ('$username', '$hashPassword', '$role')");
	            $user_id = $this->db->insert_id;

	            // Insert teacher
	            $saveTeacher = $this->db->query("INSERT INTO teachers (firstname, middlename, lastname, teacher_code, user_id) VALUES ('$firstname', '$middlename', '$lastname', '$teacher_code', '$user_id')");
	            $teacher_id = $this->db->insert_id;

	            // Insert assignments
	            foreach ($subject_ids as $subject_id) {
	                foreach ($class_ids as $class_id) {
	                    $this->db->query("INSERT INTO teacher_subject_class (teacher_id, subject_id, class_id) VALUES ($teacher_id, $subject_id, $class_id)");
	                }
	            }
	        }

	        // Commit the transaction
	        $this->db->commit();
	        return json_encode(['status' => 1, 'message' => 'Data successfully saved']);
	    } catch (Exception $e) {
	        // Rollback the transaction if something goes wrong
	        $this->db->rollback();
	        return json_encode(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage()]);
	    }
	}
	function delete_teacher(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM teachers where id = $id");
		if($delete){
			return 1;
		}
	}
	function get_results_by_class() {
	    $class = $_POST['class'];
	    // Get the results ranked by the students average or positions
	    $qry = $this->db->query("SELECT r.*, concat(s.firstname,' ',s.middlename,' ',s.lastname) as name, s.reg_no, concat(c.level,'-',c.section) as class FROM results r inner join classes c on c.id = r.class_id inner join students s on s.id = r.student_id WHERE concat(c.level,'-',c.section) = '$class' order by student_average desc");
	    $data = [];
	    while ($row = $qry->fetch_assoc()) {
	        $row['subjects'] = $this->db->query("SELECT * FROM result_items where result_id = " . $row['id'])->num_rows;
	        $data[] = $row;
	    }
	    echo json_encode($data);
	    exit;
	}
	function save_result() {
	    extract($_POST); // Extract $_POST variables

	    // Sanitize inputs (assuming sanitize_input() is defined correctly)
	    $student_id = $this->sanitize_input($student_id);
	    $student_average = $this->sanitize_input($student_average);
	    $class_id = $this->sanitize_input($class_id);
	    $subject_id = array_map([$this, 'sanitize_input'], $subject_id); // Sanitize each subject_id in array
	    $delete_all = isset($delete_all) ? $this->sanitize_input($delete_all) : 0; // Check for delete_all flag

	    // Check if the result already exists for the student and class
	    $chk = $this->db->query("SELECT * FROM results WHERE student_id = '$student_id' AND class_id = '$class_id'");
	    if ($chk === false) {
	        error_log("Error: " . $this->db->error);
	        echo json_encode(['status' => 0, 'message' => 'Database error.']);
	        return;
	    }

	    if ($chk->num_rows > 0) {
	        // If result exists, fetch the existing result id
	        $existing_result = $chk->fetch_assoc();
	        $result_id = $existing_result['id'];

	        // Update existing result
	        $stmt = $this->db->prepare("UPDATE results SET student_average = ? WHERE id = ?");
	        if ($stmt === false) {
	            error_log("Prepare failed: (" . $this->db->errno . ") " . $this->db->error);
	            echo json_encode(['status' => 0, 'message' => 'Prepare statement failed.']);
	            return;
	        }
	        $stmt->bind_param("si", $student_average, $result_id);
	        if (!$stmt->execute()) {
	            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
	            echo json_encode(['status' => 0, 'message' => 'Execute statement failed.']);
	            return;
	        }
	        $stmt->close();
	    } else {
	        // Insert new result
	        $stmt = $this->db->prepare("INSERT INTO results (student_id, session_id, term_id, student_average, class_id) VALUES (?, ?, ?, ?, ?)");
	        if ($stmt === false) {
	            error_log("Prepare failed: (" . $this->db->errno . ") " . $this->db->error);
	            echo json_encode(['status' => 0, 'message' => 'Prepare statement failed.']);
	            return;
	        }
	        $stmt->bind_param("iiisi", $student_id, $session_id, $term_id, $student_average, $class_id);
	        if (!$stmt->execute()) {
	            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
	            echo json_encode(['status' => 0, 'message' => 'Execute statement failed.']);
	            return;
	        }
	        $result_id = $stmt->insert_id; // Get the inserted ID
	        $stmt->close();
	    }

	    // If delete_all flag is set, delete all result items for the current result_id
	    if ($delete_all) {
	        $stmt = $this->db->prepare("DELETE FROM result_items WHERE result_id = ?");
	        if ($stmt === false) {
	            error_log("Prepare failed: (" . $this->db->errno . ") " . $this->db->error);
	            echo json_encode(['status' => 0, 'message' => 'Prepare statement failed.']);
	            return;
	        }
	        $stmt->bind_param("i", $result_id);
	        if (!$stmt->execute()) {
	            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
	            echo json_encode(['status' => 0, 'message' => 'Execute statement failed.']);
	            return;
	        }
	        $stmt->close();
	    } else {
	        // Delete specific result_items for the current result_id and subject_id
	        $stmt = $this->db->prepare("DELETE FROM result_items WHERE result_id = ? AND subject_id = ?");
	        if ($stmt === false) {
	            error_log("Prepare failed: (" . $this->db->errno . ") " . $this->db->error);
	            echo json_encode(['status' => 0, 'message' => 'Prepare statement failed.']);
	            return;
	        }
	        foreach ($subject_id as $v) {
	            $stmt->bind_param("ii", $result_id, $v);
	            if (!$stmt->execute()) {
	                error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
	                echo json_encode(['status' => 0, 'message' => 'Execute statement failed.']);
	                return;
	            }
	        }
	        $stmt->close();
	    }

	    // Insert new result_items for each subject
	    $stmt = $this->db->prepare("INSERT INTO result_items (result_id, subject_id, c_assess, exam, total) VALUES (?, ?, ?, ?, ?)");
	    if ($stmt === false) {
	        error_log("Prepare failed: (" . $this->db->errno . ") " . $this->db->error);
	        echo json_encode(['status' => 0, 'message' => 'Prepare statement failed.']);
	        return;
	    }
	    foreach ($subject_id as $k => $v) {
	        $subject_id_value = $v;
	        $ca_value = $this->sanitize_input($ca[$k]); // Sanitize ca value
	        $exam_value = $this->sanitize_input($exam[$k]); // Sanitize exam value
	        $total_value = $this->sanitize_input($total[$k]); // Sanitize total value

	        $stmt->bind_param("iiiii", $result_id, $subject_id_value, $ca_value, $exam_value, $total_value);
	        if (!$stmt->execute()) {
	            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
	            echo json_encode(['status' => 0, 'message' => 'Execute statement failed.']);
	            return;
	        }
	    }
	    $stmt->close();

	    echo json_encode(['status' => 1, 'message' => 'Data successfully saved.']);
	}
	function delete_result(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM results where id = $id");
		if($delete){
			return 1;
		}
	}
	function fetch_students() {
		// Retrieve the POST parameters
		$class_id = isset($_POST['class_id']) ? $this->sanitize_input($_POST['class_id']) : '';
		$subject_id = isset($_POST['subject_id']) ? $this->sanitize_input($_POST['subject_id']) : '';

		$response = [];

		if ($class_id) {
		    if ($_SESSION['login_role'] == 'teacher') {
		        $students_query = "SELECT s.id, s.reg_no, s.class_id, CONCAT(c.level, '-', c.section) AS class, CONCAT(s.firstname, ' ', s.lastname, ' ', s.middlename) AS name FROM students s INNER JOIN classes c ON c.id = s.class_id LEFT JOIN results r ON r.student_id = s.id LEFT JOIN result_items rs ON rs.result_id = r.id AND rs.subject_id = '$subject_id' WHERE s.class_id = '$class_id' AND rs.id IS NULL ORDER BY CONCAT(s.firstname, ' ', s.lastname, ' ', s.middlename) ASC";
		    } else {
		        $students_query = "SELECT s.id, s.reg_no, s.class_id, CONCAT(c.level, '-', c.section) AS class, CONCAT(s.firstname, ' ', s.lastname, ' ', s.middlename) AS name FROM students s INNER JOIN classes c ON c.id = s.class_id LEFT JOIN results r ON r.student_id = s.id LEFT JOIN result_items rs ON rs.result_id = r.id AND rs.subject_id = '$subject_id' WHERE s.class_id = '$class_id' AND rs.id IS NULL ORDER BY CONCAT(s.firstname, ' ', s.lastname, ' ', s.middlename) ASC";
		    }
		    $query = $this->db->query($students_query);
		    if ($query) {
		        $data = [];
		        while ($row = $query->fetch_assoc()) {
		            $data[] = $row;
		        }
		        $response = ['status' => 1, 'data' => $data];
		    } else {
		        $response = ['status' => 0, 'message' => 'Error executing query: ' . $this->db->error];
		    }
		} else {
		    $response = ['status' => 0, 'message' => 'Invalid request!'];
		}

		echo json_encode($response);
	}
	function uploadCSV() {
	    try {
	        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	            if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == UPLOAD_ERR_OK) {
	                $fileTmpPath = $_FILES['csvFile']['tmp_name'];

	                if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
	                    // Skip the first row (header row)
	                    fgetcsv($handle, 1000, ",");

	                    // Begin transaction
	                    $this->db->begin_transaction();
	                    $role = $_POST['role'];

	                    if ($role == 'student') {
	                        $accountStmt = $this->db->prepare("INSERT INTO students (firstname, lastname, middlename, reg_no, class_id, user_id) VALUES (?, ?, ?, ?, ?, ?)");
	                        $checkStmt = $this->db->prepare("SELECT id FROM students WHERE reg_no = ?");
	                    } elseif ($role == 'teacher') {
	                        $accountStmt = $this->db->prepare("INSERT INTO teachers (firstname, lastname, middlename, teacher_code, user_id) VALUES (?, ?, ?, ?, ?)");
	                        $checkStmt = $this->db->prepare("SELECT id FROM teachers WHERE teacher_code = ?");
	                    } else {
	                        throw new Exception("Invalid role specified.");
	                    }

	                    $userStmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");

	                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	                        if ($role == 'student') {
	                            $firstname = $data[0];
	                            $lastname = $data[1];
	                            $middlename = $data[2];
	                            $reg_no = $data[3];
	                            $class = $data[4];
	                            $username = strtolower($data[5]);
	                            $password = md5($data[6]);
	                        } elseif ($role == 'teacher') {
	                            $firstname = $data[0];
	                            $lastname = $data[1];
	                            $middlename = $data[2];
	                            $code = $data[3];
	                            $username = strtolower($data[4]);
	                            $password = md5($data[5]);
	                        }

	                        $class_id = $this->getClassId($class);

	                        if ($role == 'student') {
	                            $checkStmt->bind_param("s", $reg_no);
	                        }
	                        if ($role == 'teacher') {
	                            $checkStmt->bind_param("s", $code);
	                        }
	                        $checkStmt->execute();
	                        $checkStmt->store_result();

	                        if ($checkStmt->num_rows == 0) {
	                            $userStmt->bind_param("sss", $username, $password, $role);
	                            $userStmt->execute();
	                            $user_id = $userStmt->insert_id;

	                            if ($role == 'student') {
	                                $accountStmt->bind_param("ssssii", $firstname, $lastname, $middlename, $reg_no, $class_id, $user_id);
	                            }
	                            if ($role == 'teacher') {
	                                $accountStmt->bind_param("ssssi", $firstname, $lastname, $middlename, $code, $user_id);
	                            }
	                            $accountStmt->execute();
	                        }
	                    }

	                    $this->db->commit();
	                    $response['status'] = 1;
	                    $response['message'] = "File data successfully imported to the database.";

	                    fclose($handle);
	                    $accountStmt->close();
	                    $userStmt->close();
	                    $checkStmt->close();
	                } else {
	                    throw new Exception("Failed to open the file.");
	                }
	            } else {
	                throw new Exception("No file uploaded or an error occurred during file upload.");
	            }
	        } else {
	            throw new Exception("Invalid request method.");
	        }
	    } catch (Exception $e) {
	        if ($this->db->in_transaction) {
	            $this->db->rollback();
	        }
	        $response = array('status' => 0, 'message' => 'Failed to import data: ' . $e->getMessage());
	    }
	    echo json_encode($response);
	}

    function getClassId($class) {
        // Assuming your classes table has columns 'level' and 'section'
        list($level, $section) = explode('-', $class);
        $stmt = $this->db->prepare("SELECT id FROM classes WHERE level = ? AND section = ?");
        $stmt->bind_param("ss", $level, $section);
        $stmt->execute();
        $stmt->bind_result($class_id);
        $stmt->fetch();
        $stmt->close();
        return $class_id;
    }
	function publishResult($result_id) {
        $sql = "UPDATE results SET status = 'published' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $result_id);
        if ($stmt->execute()) {
            $response = array('status' => 1, 'message' => 'Result successfully published.');
        } else {
            $response = array('status' => 0, 'message' => 'Failed to publish result.');
        }
        echo json_encode($response);
    }

    function unpublishResult($result_id) {
        $sql = "UPDATE results SET status = 'unpublished' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $result_id);
        if ($stmt->execute()) {
            $response = array('status' => 1, 'message' => 'Result successfully unpublished.');
        } else {
            $response = array('status' => 0, 'message' => 'Failed to unpublish result.');
        }
		echo json_encode($response);
    }

    function getResults($student_id) {
        $sql = "SELECT * FROM results WHERE student_id = ? AND status = 'published'";
        $stmt = $this->id->prepare($sql);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);
        return $results;
    }
    function get_students_by_class() {
        $class = $this->sanitize_input($_POST['class']);
        
        $result_ids = $this->db->query("SELECT student_id FROM results");
        $result_student_ids = [];
        while ($row = $result_ids->fetch_array()) {
            $result_student_ids[] = $row['student_id'];
        }

        $students = $this->db->query("SELECT s.*, concat(c.level, '-', c.section) as class, concat(firstname, ' ', middlename, ' ', lastname) as name FROM students s INNER JOIN classes c ON c.id = s.class_id WHERE concat(c.level, '-', c.section) = '$class' ORDER BY concat(firstname, ' ', middlename, ' ', lastname) ASC");
        
        // Debugging output
        error_log("Students found: " . $students->num_rows);

        $options = '<option value="">Select a student</option>';
        while ($row = $students->fetch_array()) {
            $disabled = in_array($row['id'], $result_student_ids) ? 'disabled' : '';
            $options .= '<option value="'.$row['id'].'" data-class_id="'.$row['class_id'].'" data-class="'.$row['class'].'" '.$disabled.'>'.$row['reg_no'].' | '. strtoupper($row['class']) .' | '.ucwords($row['name']).'</option>';
        }
        return $options;
    }
    function publish_all_results($class, $status) {
        $class = $this->sanitize_input($class);
        $status = $this->sanitize_input($status);

        $query = $this->db->query("UPDATE results r INNER JOIN students s ON r.student_id = s.id INNER JOIN classes c ON s.class_id = c.id SET r.status = '$status' WHERE concat(c.level, '-', c.section) = '$class'");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function fetch_subjects($class_id) {
	    $class_id = $this->sanitize_input($class_id);

	    // Initialize subjects array
	    $subjects = [];

	    // SQL query to fetch distinct subjects for the given class_id
	    $sql = "
	        SELECT DISTINCT s.id, s.subject as subject_name 
	        FROM subjects s 
	        LEFT JOIN result_items rs ON rs.subject_id = s.id 
	        LEFT JOIN results r ON r.id = rs.result_id 
	        WHERE r.class_id = $class_id
	    ";

	    // Execute the SQL query
	    $result = $this->db->query($sql);

	    // Check if the query executed successfully
	    if ($result) {
	        // Fetch all subjects
	        while ($row = $result->fetch_assoc()) {
	            $subjects[] = $row;
	        }

	        // Check if subjects were found and return JSON response
	        if (!empty($subjects)) {
	            return json_encode(['status' => '1', 'subjects' => $subjects]);
	        } else {
	            return json_encode(['status' => '0', 'message' => 'No subjects found!']);
	        }
	    } else {
	        // Return error message if the query fails
	        return json_encode(['status' => '0', 'message' => 'Error in SQL query: ' . $this->db->error]);
	    }
	}


    function fetch_broadsheet() {
        $class_id = $this->sanitize_input($_POST['class_id']);
	    $cut_off_mark = 40;

	    // Initialize subjects array
	    $subjects = [];

	    // SQL query to fetch distinct subjects for the given class_id
	    $sql = "SELECT DISTINCT s.id, s.subject as subject_name FROM subjects s LEFT JOIN result_items rs ON rs.subject_id = s.id LEFT JOIN results r ON r.id = rs.result_id WHERE r.class_id = $class_id";

	    // Execute the SQL query
	    $result = $this->db->query($sql);

	    // Check if the query executed successfully
	    if ($result) {
	        // Fetch all subjects
	        while ($row = $result->fetch_assoc()) {
	            $subjects[] = $row;
	        }
	    }

	    $select_parts = [
	        "s.id", 
	        "s.firstname", 
	        "s.lastname", 
	        "s.reg_no", 
	        "r.student_average"
	    ];
	    
	    foreach ($subjects as $subject) {
	        $subject_name = strtolower(str_replace([' ', '_', '.', '(', ')'], '', $subject['subject_name']));
	        
	        $select_parts[] = "MAX(CASE WHEN ss.subject = '{$subject['subject_name']}' THEN rs.c_assess ELSE NULL END) AS `{$subject_name}_ca`";
	        $select_parts[] = "MAX(CASE WHEN ss.subject = '{$subject['subject_name']}' THEN rs.exam ELSE NULL END) AS `{$subject_name}_exam`";
	        $select_parts[] = "MAX(CASE WHEN ss.subject = '{$subject['subject_name']}' THEN rs.total ELSE NULL END) AS `{$subject_name}_total`";
	        $total_expr[] = "MAX(CASE WHEN ss.subject = '{$subject['subject_name']}' THEN rs.total ELSE 0 END)";
	    }

	 	$total_expr_str = implode(' + ', $total_expr);
	    $select_parts[] = "COALESCE($total_expr_str, 0) AS total";
	    $select_parts[] = "CASE WHEN COALESCE($total_expr_str, 0) >= $cut_off_mark THEN 'Passed' ELSE 'Failed' END AS remark";

	    $sql = "SELECT " . implode(", ", $select_parts) . " 
	            FROM students s 
	            LEFT JOIN results r ON r.student_id = s.id 
	            LEFT JOIN result_items rs ON rs.result_id = r.id
	            LEFT JOIN subjects ss ON ss.id = rs.subject_id
	            WHERE s.class_id = '$class_id' 
	            GROUP BY s.id, s.firstname, s.lastname, s.reg_no 
	            ORDER BY r.student_average DESC, CONCAT(s.firstname, ' ', s.lastname) ASC";

		    // Debug: Print the generated SQL query
	        error_log($sql); // This will log the query to your server's error log

		    $data = [];
		    $result = $this->db->query($sql);  

		    if ($result) {
	        if ($result->num_rows > 0) {
	            $position = 1;
	            $previous_average = null;
	            $tie_count = 0;

	            while ($row = $result->fetch_assoc()) {
	            	// This condition helps to assign positions
	                if ($previous_average !== null && $row['student_average'] == $previous_average) {
	                    $row['position'] = $position;
	                } else {
	                    $position += $tie_count;
	                    $row['position'] = $position;
	                    $previous_average = $row['student_average'];
	                    $tie_count = 1;
	                }
	                $previous_average = $row['student_average'];
	                $data[] = $row;
	            }
	            echo json_encode(['status' => '1', 'data' => $data]);

	        } else {
	            echo json_encode(['status' => '0', 'message' => 'No results found!']);
	            exit;
	        }
	    } else {
	        echo json_encode(['status' => '0', 'message' => 'Error in SQL query: ' . $this->db->error]);
	        exit;
	    }
	    exit;

    }

}

?>