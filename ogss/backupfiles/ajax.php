<?php
ob_start();
date_default_timezone_set("Africa/Lagos");

$action = isset($_GET['action']) ? $_GET['action'] : '';
include 'admin_class.php';
$crud = new Action();

if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}

if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'update_user'){
	$save = $crud->update_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'check_username'){
	$save = $crud->check_username();
	if($save)
		echo $save;
}
if($action == 'save_class'){
	$save = $crud->save_class();
	if($save)
		echo $save;
}
if($action == 'delete_class'){
	$save = $crud->delete_class();
	if($save)
		echo $save;
}
if($action == 'save_subject'){
	$save = $crud->save_subject();
	if($save)
		echo $save;
}
if($action == 'delete_subject'){
	$save = $crud->delete_subject();
	if($save)
		echo $save;
}
if($action == 'save_student'){
	$save = $crud->save_student();
	if($save)
		echo $save;
}
if($action == 'delete_student'){
	$save = $crud->delete_student();
	if($save)
		echo $save;
}
if($action == 'save_teacher'){
	$save = $crud->save_teacher();
	if($save)
		echo $save;
}
if($action == 'delete_teacher'){
	$save = $crud->delete_teacher();
	if($save)
		echo $save;
}
if($action == 'get_results_by_class'){
	$save = $crud->get_results_by_class();
	if($save)
		echo $save;
}
if($action == 'save_result'){
	$save = $crud->save_result();
	if($save)
		echo $save;
}
if($action == 'delete_result'){
	$save = $crud->delete_result();
	if($save)
		echo $save;
}
if($action == 'fetch_students'){
	$save = $crud->fetch_students();
	if($save)
		echo $save;
}
if($action == 'upload_csv') {
    $save = $crud->uploadCSV();
    if ($save) {
        echo $save;
    }
}
if($action == 'publish') {
	$result_id = $_POST['id'];
	$save = $crud->publishResult($result_id);
	if ($save) {
		echo $save;
	}
}
if($action == 'unpublish') {
	$result_id = $_POST['id'];
	$save = $crud->unpublishResult($result_id);
	if ($save) {
		echo $save;
	}
}
if($action == 'get_students_by_class') {
	$save = $crud->get_students_by_class();
	if($save) {
		echo $save;
	}
}
if ($action == 'publish_all_results') {
    $class = $_POST['class'];
    $status = $_POST['status'];
    $result = $crud->publish_all_results($class, $status);
    if ($result) {
        echo json_encode(['status' => 1, 'message' => 'Results successfully updated.']);
    } else {
        echo json_encode(['status' => 0, 'message' => 'Failed to update results.']);
    }
    exit;
}
if ($action == 'fetch_subjects') {
	$class_id = sanitize_input($_POST['class_id']);
	$save = $crud->fetch_subjects($class_id);
	if ($save) {
		echo $save;
	}
}
if ($action == 'fetch_broadsheet') {
	$save = $crud->fetch_broadsheet();
	if ($save) {
		echo $save;
	}
}

ob_end_flush();
?>
