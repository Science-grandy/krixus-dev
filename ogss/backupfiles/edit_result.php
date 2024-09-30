<?php
if (!isset($conn)) {
    include 'db_connect.php';
}
if (isset($_GET['id'])) {
    $s_id = sanitize_input($_GET['id']);
    if ($_SESSION['login_role'] == 'teacher') {
        $qry = $conn->query("SELECT r.*, concat(s.firstname,' ',s.middlename,' ',s.lastname) as name, s.reg_no, concat(c.level,'-',c.section) as class, tsc.teacher_id FROM results r INNER JOIN classes c ON c.id = r.class_id INNER JOIN students s ON s.id = r.student_id INNER JOIN (SELECT DISTINCT class_id, teacher_id, subject_id FROM teacher_subject_class) tsc ON tsc.class_id = r.class_id WHERE r.student_id = $s_id")->fetch_array();
    }
    else {
        $qry = $conn->query("SELECT r.*, concat(s.firstname,' ',s.middlename,' ',s.lastname) as name, s.reg_no, concat(c.level,'-',c.section) as class FROM results r INNER JOIN classes c ON c.id = r.class_id INNER JOIN students s ON s.id = r.student_id WHERE r.student_id = $s_id")->fetch_array();
    }

    foreach($qry as $k => $v) {
        $$k = $v;
    }
}

include 'new_result.php';
?>
