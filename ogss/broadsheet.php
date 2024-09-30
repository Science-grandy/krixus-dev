<?php include 'db_connect.php'; ?>

<div class="card-body">
	<div class="form-group">
		<label for="classSelect">Select Class:</label>
		<select class="form-control mb-4" id="classSelect">
			<?php
			$query = $conn->query("SELECT id, level, section FROM classes ORDER BY CONCAT(level, section) ASC");
			$defaultClass = '';
			if (isset($query) && $query->num_rows > 0) {
				$firstRow = true;
				while ($row = $query->fetch_assoc()) {
					$class = strtoupper($row['level'] . '-' . $row['section']);
					$class_id = $row['id'];
					if ($firstRow) {
						$defaultClass = $class_id;
						$firstRow = false;
					}
					?>
					<option value="<?php echo $class_id; ?>"><?php echo $class; ?></option>
					<?php
				}
			} else {
				?>
				<option value="">No classes found</option>
				<?php
			}
			?>
		</select>

		<div class="col-md-2 my-4 px-0">
			<button id="printBroadsheet" class="btn btn-sm btn-primary" onclick="printBroadsheet()">Print Broadsheet</button>
		</div>

		<div class="table-responsive">
			<table class="table table-hover table-bordered" id="broadsheet">
				<thead>
					<!-- Rows will be populated dynamically -->
				</thead>
				<tbody>
					<!-- Rows will be populated dynamically -->
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		// Load initial results for the default class
		var defaultClass = '<?php echo $defaultClass; ?>';
		$('#classSelect').val(defaultClass);
		fetchBroadsheetData(defaultClass);

		$('#classSelect').change(function() {
			const class_id = $(this).val();
			fetchBroadsheetData(class_id);
		});

		function fetchBroadsheetData(class_id) {
			start_load();
			$.ajax({
				url: 'ajax.php?action=fetch_subjects',
				method: 'POST',
				data: { class_id: class_id },
				dataType: 'json',
				success: function(response) {
					if (response.status === '1') {
						const subjects = response.subjects;
						populateTableHeaders(subjects);
						fetchBroadsheetDetails(class_id, subjects);
					} else {
						alert_toast('No subjects found for the selected class.', 'error');
					}
					end_load();
				},
				error: function() {
					alert_toast('Error retrieving subjects.', 'error');
					end_load();
				}
			});
		}

		function populateTableHeaders(subjects) {
	        // Main headers
			let mainHeaders = '<tr>';
			mainHeaders += '<th rowspan="2">S/N</th>';
			mainHeaders += '<th rowspan="2">Name</th>';
			mainHeaders += '<th rowspan="2">Reg No</th>';

	        // Subjects
			subjects.forEach(subject => {
				mainHeaders += `<th class="text-center s_end" colspan="3">${subject.subject_name}</th>`;
			});

			mainHeaders += '<th rowspan="2">Sum Total</th>';
			mainHeaders += '<th rowspan="2">Percentage(%)</th>';
			mainHeaders += '<th rowspan="2">Position</th>';
			mainHeaders += '<th rowspan="2">Remark</th>';
			mainHeaders += '</tr>';

	        // Sub-headers for subjects
			let subHeaders = '<tr>';
			subjects.forEach(() => {
				subHeaders += '<th>CA</th>';
				subHeaders += '<th>Exam</th>';
				subHeaders += '<th class="s_end">Total</th>';
			});
			subHeaders += '</tr>';

	        // Append the headers to the table
			$('#broadsheet thead').html(mainHeaders + subHeaders);
		}

		function fetchBroadsheetDetails(class_id, subjects) {
			$.ajax({
				url: 'ajax.php?action=fetch_broadsheet',
				method: 'POST',
				data: { class_id: class_id },
				dataType: 'json',
				success: function(response) {
					if (response.status === '1') {
						populateBroadsheetTable(response.data, subjects);
					} else {
						alert_toast('No results found!', 'error');
					}
				},
				error: function() {
					alert_toast('Error retrieving data.', 'error');
				}
			});
		}

		function populateBroadsheetTable(data, subjects) {
			let rows = '';
			let sn = 1;

			data.forEach(student => {
				const { firstname, lastname, reg_no, total, student_average, position, remark } = student;

				let row = `
				<tr>
				<td>${sn++}</td>
				<td>${firstname} ${lastname}</td>
				<td>${reg_no}</td>
				`;

				subjects.forEach(subject => {
					const subject_name = subject.subject_name.toLowerCase().replace(/[\s_.()]/g, '');
					const ca = student[`${subject_name}_ca`] || '-';
					const exam = student[`${subject_name}_exam`] || '-';
					const total_score = student[`${subject_name}_total`] || '-';

					row += `<td>${ca}</td><td>${exam}</td><td class='s_end'>${total_score}</td>`;
				});

				row += `
				<td>${total}</td>
				<td>${student_average != null ? student_average : '0%'}</td>
				<td>${position}</td>
				<td>${remark}</td>
				</tr>
				`;

				rows += row;
			});

			$('#broadsheet tbody').html(rows);
		}
	});

</script>
<style>
@media print {
	@page {
		size: auto;
		margin: 0;
	}
	body {
		visibility: hidden;
	}
	#classSelect {
		visibility: visible;
		position: absolute;
		left: 0;
		top: 0;
	}
	#broadsheet, #broadsheet * {
		visibility: visible;
	}
	#broadsheet {
		position: absolute;
		left: 0;
		top: 40px;
	}
	/* Hide unnecessary elements */
	#printBroadsheet {
		display: none;
	}
	table {
		width: 100%;
		border-collapse: collapse;
	}
	table, th, td {
		border: 1px solid #dee2e6;
	}
	th, td {
		padding: 8px;
		text-align: center;
	}
	td {
		font-weight: bold;
	}
	th.s_end, td.s_end {
		border-right: 7px solid #dee2e6 !important;
	}
}
table .s_end {
  border-right: 3px solid #dee2e6;
}
</style>