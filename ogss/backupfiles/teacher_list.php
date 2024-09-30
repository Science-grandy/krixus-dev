<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary " href="./index.php?page=new_teacher"><i class="fa fa-plus"></i>Add New</a>
			</div>
			<div class="card-tools mx-3">
				<a class="btn btn-block btn-sm btn-secondary btn-flat border-secondary " href="./index.php?page=upload&role=teacher"></i>Upload CSV</a>
			</div>
		</div>
		<div class="card-body table-responsive">
			<table class="table table-hover table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="25%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Teacher Code</th>
						<th>Name</th>
						<th>Classes</th>
						<th>Subjects</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="teacher-list">
					<?php
					$i = 1;
					$qry = $conn->query("
						SELECT 
							t.id, 
							t.teacher_code, 
							concat(t.firstname, ' ', t.middlename, ' ', t.lastname) as name,
							GROUP_CONCAT(DISTINCT concat(c.level, '-', c.section) ORDER BY c.level, c.section ASC SEPARATOR ', ') as classes,
							GROUP_CONCAT(DISTINCT s.subject ORDER BY s.subject ASC SEPARATOR ', ') as subjects
						FROM 
							teachers t
						LEFT JOIN 
							teacher_subject_class tsc ON tsc.teacher_id = t.id
						LEFT JOIN 
							classes c ON tsc.class_id = c.id
						LEFT JOIN 
							subjects s ON tsc.subject_id = s.id
						GROUP BY 
							t.id, t.teacher_code, t.firstname, t.middlename, t.lastname
						ORDER BY 
							t.firstname, t.middlename, t.lastname ASC
					");
					while($row = $qry->fetch_assoc()):
					?>
					<tr>
						<td class="text-center"><?php echo $i++ ?></td>
						<td class=""><?php echo $row['teacher_code'] ?></td>
						<td><?php echo ucwords($row['name']) ?></td>
						<td><?php echo strtoupper($row['classes']) ?></td>
						<td><?php echo ucwords($row['subjects']) ?></td>
						<td class="text-center">
		                    <div class="btn-group">
		                        <a href="index.php?page=edit_teacher&id=<?php echo $row['id'] ?>" class="btn btn-primary btn-flat ">
		                          <i class="fas fa-edit"></i>
		                        </a>
		                        <button type="button" class="btn btn-danger btn-flat delete_teacher" data-id="<?php echo $row['id'] ?>">
		                          <i class="fas fa-trash"></i>
		                        </button>
	                      </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<style>
	table td{
		vertical-align: middle !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('#list').dataTable()

		// Use delegated event listeners
	    $('#teacher-list').on('click', '.view_teacher', function() {
	        uni_modal("Teacher's Details", "view_teacher.php?id=" + $(this).attr('data-id'),"large")
	    });
	    
	    $('#teacher-list').on('click', '.delete_teacher', function() {
	        _conf("Are you sure to delete this teacher?", "delete_teacher", [$(this).attr('data-id')]);
	    });
	})
	function delete_teacher($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_teacher',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>
