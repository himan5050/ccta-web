
<div class="container-fluid main_content_body" id="main-body">
	<div class="row">
		<div class="col-md-12">
			<div class="content_body_top">
				<h4>
					<a href="<?php echo site_url('phase'); ?>" class="btn btn-lg btn-default" id="">Masters</a>
					<a href="<?php echo site_url('project'); ?>" class="btn btn-lg list-add-btn" id="">Projects</a>
					<a href="<?php echo site_url('project'); ?>" class="btn btn-lg" id="">Reports</a>
					<span> <a
						href="<?php echo site_url('project'); ?>" id="">Project Info</a> <a
						href="<?php echo site_url('project_funds'); ?>" class="active"
						id="">Funds</a> <a
						href="<?php echo site_url('project_phase_contract'); ?>" id="">Phase
							to Contract Mapping</a> <a
						href="<?php echo site_url('financial_plan'); ?>">Financial Plan</a>
						<a href="<?php echo site_url('report1'); ?>" id="">Report 1</a> <a
						href="<?php echo site_url('report2'); ?>" id="">Report 2</a> <a
						href="<?php echo site_url('report3'); ?>" id="">Report 3</a> </span>
					<span class="project_name"> <?php echo $project_name; ?> </span>
				</h4>
			</div>
			<div class="body_content">

				<div class="panel panel-default" id="">
					<div class="panel-heading">
						<h5>
							Edit Fund to Project :
							<?php echo $project_name; ?>
						</h5>
						<?php if (isset($error_msg)) { ?>
						<h5>
							<b style="color: red;"><?php echo $error_msg ?> </b>
						</h5>
						<?php } ?>
						<?php if (isset($success_msg)) { ?>
						<h5>
							<b style="color: green;"><?php echo $success_msg ?> </b>
						</h5>
						<?php } ?>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<form method="post" class="form-horizontal" action="">
									<div class="row">
										<div class="col-md-2">
											<div class="form-group">
												<label class="col-sm-12" for="">Fund Source:</label>
												<div class="col-sm-12">
													<input type="text" name="fund_name" class="form-control"
														id="notes" placeholder="Enter Fund Name"
														value="<?php echo $post['fund_name'] ?>"
														readonly="readonly">
												</div>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label class="col-sm-12" for="">Allocated Amount (<i
													class="fa fa-usd" aria-hidden="true"></i>):</label>
												<div class="col-sm-12">
													<input type="text" name="allocated_amount"
														class="form-control number" id="allocated_amount"
														placeholder="No Allocated Amount" onchange="change()"
														value="<?php echo !empty($post['allocated_amount'])?$post['allocated_amount']:''; ?>">
												</div>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label class="col-sm-12" for="">Programming Action</label>
												<div class="col-sm-12">
													<input type="text" name="programming_action"
														class="form-control" id="programming_action"
														placeholder="Enter Programming Action"
														value="<?php echo $post['programming_action'] ?>">
												</div>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label class="col-sm-12" for="">Programming Notes</label>
												<div class="col-sm-12">
													<input type="text" name="notes" class="form-control"
														id="notes" placeholder="Enter Programming Notes"
														value="<?php echo $post['notes'] ?>">
												</div>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label class="col-sm-12" for="">Status:</label>
												<div class="col-sm-12">
													<select class="form-control" name="is_active">
													<?php if($post['is_active'] != 0 ) { ?>
													<?php echo "<option value='0'>InActive</option><option value='1' selected='selected'>Active</option>"; ?>
													<?php }else{?>
													<?php echo "<option value='0' selected='selected'>InActive</option><option value='1'>Active</option>"; }?>
													</select>
												</div>
											</div>
										</div>
									</div>

									<div class="row" id="tBody"></div>
									<div class="col-md-12 text-center" style="">
										<input type="submit" name="postSubmit" class="btn btn-theme"
											value="Submit" />
										<button type="Reset" class="btn btn-theme">Reset</button>
										<a href="<?php echo site_url('fund') ?>" class="btn btn-theme">Back</a>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h5>Fund Allocation List</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<table id="dataTable_exp" class="table table-striped">
									<thead>
										<tr>
											<th>Fund Code</th>
											<th>Fund Source</th>
											<th>Allocated Amount</th>
											<th>Available Amount</th>
											<th>Programming Action</th>
											<th>Programming Notes</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
									<?php foreach($fund_lists as $fund_list): ?>
										<tr>
											<td><?php echo $fund_list['fund_code'] ?></td>
											<td><?php echo $fund_list['fund_name'] ?></td>
											<td>$ <?php echo number_format(preg_replace('/\s+/', '', $fund_list['allocated_amount'])) ?>
											</td>
											<td>$ <?php echo number_format(preg_replace('/\s+/', '', $fund_list['available_amount'])) ?>
											</td>
											<td><?php echo $fund_list['programming_action'] ?></td>
											<td><?php echo $fund_list['notes'] ?></td>
											<td><?php if($fund_list['is_active'] != 0) { echo "Active"; ?>
											<?php  } else {?> <?php echo "InActive"; } ?>
											</td>
											<td><a
												href="<?php echo site_url('project_funds/edit/'.$fund_list['project_fund_id']); ?>"
												class="btn btn-warning btn-sm" data-toggle="tooltip"
												title="Edit"><i class="fa fa-pencil-square-o"
													aria-hidden="true"></i> </a> <a
												href="<?php echo site_url('project_funds/delete/'.$fund_list['project_fund_id']); ?>"
												class="btn btn-danger btn-sm" data-toggle="tooltip"
												title="Delete"><i class="fa fa-trash" aria-hidden="true"
													onclick="return confirm('Are you sure to delete?')"></i> </a>
												<button type="button"
													class="btn btn-info btn-sm details-control"
													data-toggle="tooltip" title="History"
													onclick="openhistory(<?php echo $fund_list['project_fund_id'] ?>)">
													<i class="fa fa-history" aria-hidden="true"></i>
												</button>
											</td>
										</tr>
										<?php endforeach; ?>
									</tbody>
									<tfoot>
										<tr>
											<td><b>Budget</b></td>
											<td>$ <?php echo number_format(preg_replace('/\s+/', '', $budget)) ?>
											</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr>
											<td><b>UnFunded</b></td>
											<td>$ <?php echo number_format(preg_replace('/\s+/', '', $unfunded) )?>
											</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr>
											<td><b>Project Total Cost</b></td>
											<td>$ <?php echo number_format(preg_replace('/\s+/', '', $total_project_cost)) ?>
											</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
									</tfoot>
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
				<h4 class="modal-title">Fund History</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table id="dataTable_modal" class="table table-striped">
							<thead>
								<tr>
									<th>Fund Source</th>
									<th>Allocated Amount</th>
									<th>Available Amount</th>
									<th>Programming Action</th>
									<th>Programming Document Notes</th>
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
        url: "<?php echo site_url(); ?>/project_funds/history",
        type:"post",
        dataType:"json",
        data:{project_fund_id:id },
        success:function(response)
        {
          if(response)
          { var html = "";
            $("#history").empty();
            for (var i = 0; i < response.length; i++)
            {

              html += '<tr><td>'+ response[i]['fund_name'] +'</td><td>$ '+ changeNumber(response[i]['allocated_amount']) +'</td><td>$ '+ changeNumber(response[i]['available_amount']) +'</td><td>'+ response[i]['programming_action'] +'</td><td>'+ response[i]['notes'] +'</td><td>'+ response[i]['date_added'] +'</td></tr>';
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

    function changeNumber(i)
    {
      var z =  i.replace(/\s/g, '');
      return z.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

  </script>

<!-- Modal End -->

<script type="text/javascript">
  function change()
  {
    $entered_amount = parseInt($("#allocated_amount").val());

    $available_amount = parseInt(<?php echo $post['available_amount'] ?>);

    $allocated_amount = parseInt(<?php echo $post['allocated_amount'] ?>);

    if(isNaN($entered_amount))
    {
      alert("Allocated Amount Can't be empty");
      $("#available_amount").val($available_amount);
      $("#allocated_amount").val($allocated_amount);
    }
    else
    {
      /*if ($entered_amount <= $allocated_amount)
      {
        alert("Entered Amount should be greater than allocated amount");
        $("#allocated_amount").val($allocated_amount);
        $("#available_amount").val($available_amount);
      }
      else
      {
        $total = ($entered_amount - $allocated_amount) + $available_amount;
        $("#available_amount").val("");
        $("#available_amount").val($total);
      } */
    }
  }
</script>

<script type="text/javascript">
  function check()
  {
    var x = $("#fund_name").val();

    var z = "<?php echo $post['fund_name']; ?>";

    if(x)
    {
      if (z != x)
      {
        $.ajax(
          {
            url: "<?php echo site_url(); ?>/fund/check",
            type:"post",
            dataType:"json",
            data:{ fund_name:x },
            success:function(response)
            {
              if(response == 1)
              {
                alert("Fund Source already exist!!");
                $("#fund_name").val(z);
              }
            }
          }
        );
      }
    }
  }
</script>

<script type="text/javascript">
  $('input.number').keyup(function(event) {

  // skip for arrow keys
  if(event.which >= 37 && event.which <= 40) return;

  // format number
  $(this).val(function(index, value) {
    return value
    .replace(/\D/g, "")
    /*.replace(/\B(?=(\d{3})+(?!\d))/g, ",")*/
    ;
  });
});
</script>
