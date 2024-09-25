<?php include 'db_connect.php'; ?>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <?php if (!isset($_SESSION['rs_id'])): ?>
        <div class="card-header">
            <div class="card-tools mx-2">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_result">
                    <i class="fa fa-plus"></i> Add New
                </a>
            </div>
            <?php if ($_SESSION['login_role'] == 'admin') { ?>
            <div class="card-tools mx-2">
                <a id="unpublishAllBtn" class="btn btn-block btn-sm btn-danger btn-flat">Unpublish All</a>
            </div>
            <div class="card-tools mx-2">
                <a id="publishAllBtn" class="btn btn-block btn-sm btn-success btn-flat">Publish All</a>
            </div>
            <?php } ?>
        </div>
        <?php endif; ?>

        <?php
        $teacher_qry = $conn->query("SELECT id FROM teachers WHERE user_id = " . $_SESSION['login_id'])->fetch_assoc();
        if ($teacher_qry) {
            $teachers_id = $teacher_qry['id'];
        }
        ?>

        <div class="card-body">
            <div class="form-group">
                <label for="classSelect">Select Class:</label>
                <select class="form-control" id="classSelect">
                    <?php
                    if ($_SESSION['login_role'] == 'teacher') {
                        if ($teachers_id) {
                            $query = $conn->query("SELECT DISTINCT c.id, c.level, c.section FROM classes c INNER JOIN teacher_subject_class tsc ON tsc.class_id = c.id WHERE tsc.teacher_id = $teachers_id ORDER BY CONCAT(c.level, '-', c.section) ASC");
                        } else {
                            $query = $conn->query("SELECT id, level, section FROM classes ORDER BY CONCAT(level, section) ASC");
                        }

                        $defaultClass = '';
                        if (isset($query) && $query->num_rows > 0) {
                            $firstRow = true;
                            while ($row = $query->fetch_assoc()) {
                                $class = strtoupper($row['level'] . '-' . $row['section']);
                                if ($firstRow) {
                                    $defaultClass = $class;
                                    $firstRow = false;
                                }
                                ?>
                                <option value="<?php echo $class; ?>"><?php echo $class; ?></option>
                                <?php
                            }
                        } else {
                            ?>
                            <option value="">No classes found</option>
                            <?php
                        }
                    } else {
                        $query = $conn->query("SELECT level, section FROM classes ORDER BY CONCAT(level, section) ASC");
                        $defaultClass = '';
                        if (isset($query) && $query->num_rows > 0) {
                            $firstRow = true;
                            while ($row = $query->fetch_assoc()) {
                                $class = strtoupper($row['level'] . '-' . $row['section']);
                                if ($firstRow) {
                                    $defaultClass = $class;
                                    $firstRow = false;
                                }
                                ?>
                                <option value="<?php echo $class; ?>"><?php echo $class; ?></option>
                                <?php
                            }
                        } else {
                            ?>
                            <option value="">No classes found</option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2 my-4">
                <div class="card-tools">
                    <?php if ($_SESSION['login_role'] == "admin") {
                        echo '<a id="broadsheet" href="index.php?page=broadsheet" class="btn btn-block btn-sm btn-secondary btn-flat">View Broadsheet</a>';
                    } ?>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="25%">
                        <col width="20%">
                        <col width="10%">
                        <col width="15%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Reg No.</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Subjects</th>
                            <th>Student Average</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Initial data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Function to load results by class
    function loadResultsByClass(classVal) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=get_results_by_class',
            type: 'POST',
            data: { class: classVal },
            dataType: 'json',
            success: function(response) {
                var tbody = $('#list tbody');
                tbody.empty(); // Clear the existing rows

                if (response && response.length > 0) {
                    $.each(response, function(i, row) {

                        <?php if ($_SESSION['login_role'] == 'admin') { ?>
                        var status = row.status === 'published' 
                            ? '<button class="btn btn-sm btn-danger unpublish-result" data-id="' + row.id + '">Unpublish</button>'
                            : '<button class="btn btn-sm btn-success publish-result" data-id="' + row.id + '">Publish</button>';
                        <?php } else { ?>
                        var status = row.status === 'published' ? '<p class="text-sm text-success">Published</p>' : '<p class="text-sm text-danger">Unpublished</p>';
                        <?php } ?>

                        var tr = $('<tr/>');
                        tr.append('<th class="text-center">' + (i + 1) + '</th>');
                        tr.append('<td>' + row.reg_no + '</td>');
                        tr.append('<td>' + row.name + '</td>');
                        tr.append('<td>' + (row.class).toUpperCase() + '</td>');
                        tr.append('<td class="text-center">' + row.subjects + '</td>');
                        tr.append('<td class="text-center">' + row.student_average + '</td>');
                        tr.append('<td class="text-center">' + status + '</td>');
                        tr.append('<td class="text-center">' + getActions(row.id, row.student_id) + '</td>');
                        tbody.append(tr);
                    });
                } else {
                    tbody.append('<tr><td colspan="8" class="text-center">No results found for the selected class</td></tr>');
                }

                reattachEventListeners(); // Reattach event listeners after loading new content
                end_load();
            },
            error: function(xhr, status, error) {
                alert_toast("An unexpected error occurred: " + error, 'error');
                end_load();
            }
        });
    }

    // Function to generate actions column
    function getActions(resultId, studentId) {
        var actions = '';
        <?php if (isset($_SESSION['login_id'])): ?>
        actions += '<div class="btn-group">';
        actions += '<a href="./index.php?page=edit_result&id=' + studentId + '&edit=true" class="btn btn-primary btn-flat"><i class="fas fa-edit"></i></a>';
        actions += '<button data-id="' + resultId + '" type="button" class="btn btn-secondary btn-flat view_result"><i class="fas fa-eye"></i></button>';
        <?php if ($_SESSION['login_role'] == 'admin') { ?>
        actions += '<button type="button" class="btn btn-danger btn-flat delete_result" data-id="' + resultId + '"><i class="fas fa-trash"></i></button>';
        <?php } ?>
        actions += '</div>';
        <?php elseif (isset($_SESSION['rs_id'])): ?>
        actions += '<button data-id="' + resultId + '" type="button" class="btn btn-info btn-flat view_result"><i class="fas fa-eye"></i> View Result</button>';
        <?php endif; ?>
        return actions;
    }

    // Load initial results for the default class
    var defaultClass = '<?php echo $defaultClass; ?>';
    var storedClass = localStorage.getItem('selectedClass');
    if (storedClass) {
        defaultClass = storedClass;
    }
    $('#classSelect').val(defaultClass);
    loadResultsByClass(defaultClass);

    // Event listener for class selection change
    $('#classSelect').on('change', function() {
        var selectedClass = $(this).val();
        loadResultsByClass(selectedClass);
        localStorage.setItem('selectedClass', selectedClass);
    });

    // Function to reattach event listeners after loading new content
    function reattachEventListeners() {
        $('.view_result').off('click').on('click', function() {
            var resultId = $(this).attr('data-id');
            uni_modal("<div style='display: flex; justify-content:center; align-items:center; gap: 3px; width: 100%'><img src='assets/uploads/logo.svg' width='50'><br><b><?php echo $school_name ?> - RESULT SHEET</b></div>", "view_result.php?id=" + resultId, 'large');
        });

        $('.delete_result').off('click').on('click', function() {
            var resultId = $(this).attr('data-id');
            _conf("Are you sure to delete this result?", "delete_result", [resultId]);
        });

        $('.publish-result').off('click').on('click', function() {
            var resultId = $(this).attr('data-id');
            $.ajax({
                url: 'ajax.php?action=publish',
                type: 'POST',
                data: { id: resultId },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.status == 1) {
                        alert_toast(res.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        alert_toast(res.message, 'error');
                    }
                }
            });
        });

        $('.unpublish-result').off('click').on('click', function() {
            var resultId = $(this).attr('data-id');
            $.ajax({
                url: 'ajax.php?action=unpublish',
                type: 'POST',
                data: { id: resultId },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.status == 1) {
                        alert_toast("Result unpublished successfully", 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        alert_toast("Failed to unpublish result", 'error');
                    }
                }
            });
        });
    }

    $(document).on('click', '#publishAllBtn', function() {
        var selectedClass = $('#classSelect').val();
        if (selectedClass) {
            $.ajax({
                url: 'ajax.php?action=publish_all_results',
                method: 'POST',
                dataType: 'json',
                data: { class: selectedClass, status: 'published' },
                success: function(response) {
                    alert_toast(response.message);
                    if (response.status == 1) {
                        setTimeout(function() {
                            loadResultsByClass(selectedClass);  // Reload results table
                        }, 500);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + error);
                }
            });
        } else {
            alert('Please select a class first.');
        }
    });

    $(document).on('click', '#unpublishAllBtn', function() {
        var selectedClass = $('#classSelect').val();
        if (selectedClass) {
            $.ajax({
                url: 'ajax.php?action=publish_all_results',
                method: 'POST',
                dataType: 'json',
                data: { class: selectedClass, status: 'unpublished' },
                success: function(response) {
                    alert_toast(response.message);
                    if (response.status == 1) {
                        setTimeout(function() {
                            loadResultsByClass(selectedClass);  // Reload results table
                        }, 500);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + error);
                }
            });
        } else {
            alert('Please select a class first.');
        }
    });

    // Initial reattach of event listeners
    reattachEventListeners();
});

function delete_result(id) {
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_result',
        method: 'POST',
        data: { id: id },
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Data successfully deleted", 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        }
    });
}
</script>
