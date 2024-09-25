<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
?>
<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form action="" id="csvUploadForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="control-label" for="csvFile">Select <?php if($_GET['role']=='student') {echo "Student's"; } if($_GET['role']=='teacher') {echo "Teacher's"; } ?> CSV File:</label>
                    <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv" required>
                </div>
                <div id="csvPreview" class="mt-3 table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr id="csvPreviewHeader"></tr>
                        </thead>
                        <tbody id="csvPreviewBody"></tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
                <input type="hidden" name="role" value="<?php echo $_GET['role'] ?>">
            </form>
            <div id="response" class="mt-3"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#csvFile').on('change', function (e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var lines = e.target.result.split('\n').slice(0, 5); // Get the first 5 lines
                    displayCSVPreview(lines);
                };
                reader.readAsText(file);
            }
        });

        function displayCSVPreview(lines) {
            var headers = lines[0].split(',');
            var csvPreviewHeader = $('#csvPreviewHeader');
            var csvPreviewBody = $('#csvPreviewBody');

            csvPreviewHeader.empty();
            csvPreviewBody.empty();

            // Display headers
            headers.forEach(function(header) {
                csvPreviewHeader.append('<th>' + header.trim() + '</th>');
            });

            // Display rows
            for (var i = 1; i < lines.length; i++) {
                var row = lines[i].split(',');
                var rowHtml = '<tr>';
                row.forEach(function(cell) {
                    rowHtml += '<td>' + cell.trim() + '</td>';
                });
                rowHtml += '</tr>';
                csvPreviewBody.append(rowHtml);
            }
        }

        $('#csvUploadForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'ajax.php?action=upload_csv',
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == 1) {
                        $('#response').html('<div class="alert alert-success">' + response.message + '</div>');
                    } else {
                        $('#response').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function (xhr, status, error) {
                    $('#response').html('<div class="alert alert-danger">An error occurred: ' + error + '</div>');
                }
            });
        });
    });
</script>
