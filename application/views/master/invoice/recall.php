
<div class="container-fluid main_content_body" id="main-body">
	<div class="row">
		<div class="col-md-12">
			<div class="content_body_top">
				<h4>
					<a href="<?php echo site_url('phase'); ?>" class="btn btn-lg list-add-btn" id="">Masters</a> 
					<a href="<?php echo site_url('project'); ?>" class="btn btn-lg btn-default" id="">Projects</a> 
					<a href="<?php echo site_url('report'); ?>" class="btn btn-lg btn-default" id="">Reports</a>
					<span> <a
						href="<?php echo site_url('phase'); ?>">PROJECT PHASEs</a> <a
						href="<?php echo site_url('fund'); ?>" class="active">FUNDS</a> <a
						href="<?php echo site_url('invoice'); ?>">PROCESS INVOICES</a> </span>
				</h4>
			</div>
			<div class="body_content">

				<div class="panel panel-default" id="">
					<div class="panel-heading">
						<h5>Recall Invoice</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<form method="post" class="form-horizontal" action="">
									<div class="row">
										<div class="col-md-2">
											<div class="form-group">
												<label class="col-sm-12" for="">Invoice Number:</label>
												<div class="col-sm-12">
													<input type="text" name="invoice_number"
														class="form-control" id="invoice_number"
														onchange="check()" placeholder="Enter Invoice Number"
														value="<?php echo !empty($post['invoice_number'])?$post['invoice_number']:''; ?>"
														readonly="readonly">
												</div>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="col-sm-12" for="">Project</label>
												<div class="col-sm-12">
													<input type="text" name="project"
														class="form-control" id="project"
														placeholder="Correct Project" onchange="change()"
														value="<?php echo !empty($post['project'])?$post['project']:''; ?>">
												</div>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label class="col-sm-12" for="">Contract</label>
												<div class="col-sm-12">
													<input type="text" name="contract"
														class="form-control" id="contract"
														placeholder="Enter Contract"
														value="<?php echo !empty($post['contract'])?$post['contract']:''; ?>">
												</div>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label class="col-sm-12" for="">Amount</label>
												<div class="col-sm-12">
													<input type="text" name="amount" class="form-control number"
														id="amount" placeholder="Enter Amount"
														value="<?php echo !empty($post['amount'])?$post['amount']:''; ?>">
												</div>
											</div>
										</div>
									</div>

									<div class="row" id="tBody"></div>
									<div class="col-md-12 text-center" style="">
										<input type="submit" name="postSubmit" class="btn btn-theme"
											value="Process" /> <a
											href="<?php echo site_url('invoice') ?>"
											class="btn btn-theme">Back</a>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>


				<div class="panel panel-default">
					<div class="panel-heading">
						<h5>
						<?php echo $title ?>
						</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<table id="dataTable_exp" class="table table-striped">
									<thead>
										<tr>
											<th>Invoice Number</th>
											<th>Contract</th>
											<th>Project</th>
											<th>GL Date</th>
											<th>GL Account</th>
											<th>Description</th>
											<th>Processed Time</th>
											<th>Amount</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
									<?php if(isset($invoices_list)) { ?>
									<?php foreach($invoices_list as $invoice_list): ?>
										<tr>
											<td style="width: 200px;"><?php echo $invoice_list['invoice_number'] ?>
											</td>
											<td><?php echo $invoice_list['contract'] ?></td>
											<td><?php echo $invoice_list['project'] ?></td>
											<td><?php echo $invoice_list['gl_date'] ?></td>
											<td><?php echo $invoice_list['gl_account'] ?></td>
											<td><?php echo $invoice_list['description'] ?></td>
											<td><?php echo $invoice_list['date_processed'] ?></td>
											<td><?php echo $invoice_list['amount'] ?></td>
											<td><?php echo $invoice_list['status'] ?></td>
											<td style="width: 155px;"><a
												href="<?php echo site_url('invoice/recall/'.$invoice_list['invoice_id']); ?>"
												class="btn btn-warning btn-sm" data-toggle="tooltip"
												title="Recall"><i class="fa fa-pencil-square-o"
													aria-hidden="true"></i> </a> <a
												href="<?php echo site_url('invoice/discard/'.$invoice_list['invoice_id']); ?>"
												class="btn btn-danger btn-sm" data-toggle="tooltip"
												title="Discard"><i class="fa fa-trash" aria-hidden="true"
													onclick="return confirm('Are you sure to discard?')"></i> </a>
												<button type="button" class="btn btn-info btn-sm"
													data-toggle="tooltip" title="History"
													onclick="openhistory(<?php echo $invoice_list['invoice_id'] ?>)">
													<i class="fa fa-history" aria-hidden="true"></i>
												</button>
											</td>
										</tr>
										<?php endforeach; ?>
										<?php } ?>
									</tbody>
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
          <h4 class="modal-title">Invoice History</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="dataTable_modal" class="table table-striped">
                <thead>
                  <tr>
                    <th>Invoice Number</th>
                    <th>Contract</th>
                    <th>Project</th>
                    <th>Amount</th>
                    <th>Update Time</th>
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
          url: "<?php echo site_url(); ?>/invoice/history",
          type:"post",
          dataType:"json",
          data:{invoice_id:id },
          success:function(response)
          {
            if(response)
            { var html = "";
              $("#history").empty();
              for (var i = 0; i < response.length; i++) 
              {

                html += '<tr><td>'+ response[i]['invoice_number'] +'</td><td>'+ response[i]['contract'] +'</td><td>'+ response[i]['project'] +'</td><td>$ '+ changeNumber(response[i]['amount']) +'</td><td>'+ response[i]['date_processed'] +'</td></tr>';
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