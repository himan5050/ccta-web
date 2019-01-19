
<div class="container-fluid main_content_body" id="main-body">
	<div class="row">
		<div class="col-md-12">
			<div class="content_body_top">
				<h4>
					<a href="<?php echo site_url('phase'); ?>" class="btn btn-lg btn-default" id="">Masters</a>
					<a href="<?php echo site_url('project'); ?>" class="btn btn-lg list-add-btn" id="">Projects</a>
					<a href="<?php echo site_url('report'); ?>" class="btn btn-lg btn-default" id="">Reports</a>
					<span> <a
						href="<?php echo site_url('project'); ?>" class="active" id="">Project
							Info</a> </span>
				</h4>
			</div>
			<div class="body_content">
				<div class="panel panel-default" id="">
					<div class="panel-heading">
						<h5>Add New Project</h5>
						<?php if (isset($error_msg)) { ?>
							<h5><b style="color: red;"><?php echo $error_msg ?></b></h5>
						<?php } ?>
						<?php if (isset($success_msg)) { ?>
							<h5><b style="color: green;"><?php echo $success_msg ?></b></h5>
						<?php } ?>
					</div>
					<div class="panel-body">
						<div class="row">
								<form method="post" class="form-horizontal" id="project_form"
									action="">
									<div class="col-sm-1">
										<div class="form-group">
											<label class="col-sm-12" for="">Code:</label>
											<div class="col-sm-12">
												<input type="text" name="project_code" class="form-control"
													id="project_code" placeholder="Enter Project Code"
													required="required" onblur="check()">
											</div>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label class="col-sm-12" for="">Project Name:</label>
											<div class="col-sm-12">
												<input type="text" name="project_name" class="form-control"
													id="project_name" placeholder="Enter Project Name"
													required="required" onblur="check()">
											</div>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label class="col-sm-12" for="">Description:</label>
											<div class="col-sm-12">
												<textarea class="form-control" name="description" id=""
													placeholder="Enter Description"></textarea>
											</div>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label class="col-sm-12" for="">Total Project Cost:</label>
											<div class="col-sm-9">
												<input type="text" name="total_project_cost" class="form-control number" id="total_project_cost" value="0" placeholder="0" required="required" onblur="change(0)">
											</div>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label class="col-sm-12" for="">Start Date:</label>
											<div class="col-sm-9">
												<input type='text' name="start_date" class="form-control"
													id='datetimepicker4' required="required" />
											</div>
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label class="col-sm-12" for="">End Date:</label>
											<div class="col-sm-9">
												<input type='text' name="end_date" class="form-control"
													id='datetimepicker5' required="required" />
											</div>
										</div>
									</div>

									<div class="col-sm-1">
										<div class="form-group">
											<label class="col-sm-12" for="">Status:</label>
											<div class="col-sm-12">
												<select class="form-control" name="is_active" id=""
													placeholder="Enter Description">
													<option value="1">Active</option>
													<option value="0">Inactive</option>
												</select>
											</div>
										</div>
									</div>

									<div class="col-sm-12 text-center">
										<input type="submit" name="postSubmit"
											class="btn btn-theme hide" value="Submit" />
										<button type="button" name="postSubmit" onclick="checkform()"
											class="btn btn-theme">Submit</button>
										<button type="Reset" class="btn btn-theme">Reset</button>
									</div>
								</form>
							</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h5>Project List</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<table id="dataTable_exp" class="table table-striped">
									<thead>
										<tr>
											<th>Project Code</th>
											<th>Project Name</th>
											<th>Description</th>
											<th>Project Total Cost</th>
											<th>Start date</th>
											<th>End date</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<?php foreach($project_lists as $project_list): ?>
									<tbody>
										<tr>
											<td><?php echo $project_list['project_code'] ?></td>
											<td><?php echo $project_list['project_name'] ?></td>
											<td style="text-align: justify; text-justify: inter-word;"><?php echo $project_list['description'] ?>
											</td>
											<td>$ <?php echo number_format(preg_replace('/\s+/', '', $project_list['total_project_cost'])) ?></td>
											<td><?php echo $project_list['start_date'] ?></td>
											<td><?php echo $project_list['end_date'] ?></td>
											<td><?php if($project_list['is_active'] != 0) { echo "Active"; ?>
											<?php  } else {?> <?php echo "InActive"; } ?>
											</td>
											<td>
												<!-- <a href="<?php echo site_url('project/view/'.$project_list['project_id']); ?>" class="btn btn-success btn-sm" data-toggle="tooltip" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a> -->
												<a
												href="<?php echo site_url('project/edit/'.$project_list['project_id']); ?>"
												class="btn btn-warning btn-sm" data-toggle="tooltip"
												title="Edit"><i class="fa fa-pencil-square-o"
													aria-hidden="true"></i> </a> <a
												href="<?php echo site_url('project/delete/'.$project_list['project_id']); ?>"
												class="btn btn-danger btn-sm" data-toggle="tooltip"
												title="Delete"><i class="fa fa-trash" aria-hidden="true"
													onclick="return confirm('Are you sure to delete?')"></i> </a>
												<button type="button" class="btn btn-info btn-sm"
													data-toggle="tooltip" title="History"
													onclick="openhistory(<?php echo $project_list['project_id'] ?>)">
													<i class="fa fa-history" aria-hidden="true"></i>
												</button>
											</td>
										</tr>
									</tbody>
									<?php endforeach; ?>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModalHistory" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
				<h4 class="modal-title">Project History</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table id="Fund_dataTable_modal" class="table table-striped">
							<thead>
								<tr>
									<th>Project Code</th>
									<th>Project Name</th>
									<th style="width: 57% !important;">Description</th>
									<th>Project Total Cost</th>
									<th>Start date</th>
									<th>End date</th>
									<th>Date Added</th>
								</tr>
							</thead>
							<tbody id="history">
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>

<script type="text/javascript">

  function openhistory(id)
  {
    $.ajax(
      {
        url: "<?php echo site_url(); ?>/project/history",
        type:"post",
        dataType:"json",
        data:{project_id:id },
        success:function(response)
        {
          if(response)
          { var html = "";
            $("#history").empty();
            for (var i = 0; i < response.length; i++)
            {
              html += '<tr><td>'+ response[i]['project_code'] +'</td><td>'+ response[i]['project_name'] +'</td><td>'+ response[i]['description'] +'</td><td>'+ response[i]['total_project_cost'] +'</td><td>'+ response[i]['start_date'] +'</td><td>'+ response[i]['end_date'] +'</td><td>'+ response[i]['date_added'] +'</td></tr>';
            }

            $("#history").append(html);
            $("#myModalHistory").modal();
          }
          else
          {
            alert("No History Exists");
          }
        }
      }
    );
  }

</script>

<!-- Modal End -->

<!-- <script type="text/javascript">
$(document).ready( function () {
    $('#Fund_dataTable_modal').DataTable();
} );

</script>
 -->
<script type="text/javascript">
  function check()
  {
    var x = $("#project_name").val();

    if(x)
    {
      $.ajax(
        {
          url: "<?php echo site_url(); ?>/project/check",
          type:"post",
          dataType:"json",
          data:{ project_name:x },
          success:function(response)
          {
            if(response == 1)
            {
              alert("Project Name already exist!!");
              $("#project_name").val("");
            }
          }
        }
      );
    }
  }

</script>

<script type="text/javascript">

  function checkform()
  {
    var fieldDateFirst = document.getElementById('datetimepicker4').value;
    var fieldDateSecound = document.getElementById('datetimepicker5').value;

    fieldDateFirst = fieldDateFirst.split("/");
    var Date1 = new Date();
    Date1.setFullYear(fieldDateFirst[2],fieldDateFirst[0]-1,fieldDateFirst[1]);

    fieldDateSecound = fieldDateSecound.split("/");
    var Date2 = new Date();
    Date2.setFullYear(fieldDateSecound[2],fieldDateSecound[0]-1,fieldDateSecound[1]);


    if (Date1 < Date2)
    {
      $('#project_form').find('[type="submit"]').trigger('click');
    }
    else
    {
      alert("Last date should be greater than first date");
      return false;
    }
  }

</script>
