<div class="container-fluid main_content_body" id="main-body">
	<div class="row">
		<div class="col-md-12">
			<div class="content_body_top">
				<h4>
					<a href="<?php echo site_url('phase'); ?>"
						class="btn btn-lg btn-default" id="">Masters</a> <a
						href="<?php echo site_url('project'); ?>"
						class="btn btn-lg btn-default" id="">Projects</a> <a
						href="<?php echo site_url('report'); ?>"
						class="btn btn-lg list-add-btn" id="">Reports</a> <span> <a
						href="<?php echo site_url('report'); ?>" id="">Fund Source Report</a>
						<a href="<?php echo site_url('report/fy_report'); ?>"
						class="active" id="">Fiscal Year Report</a> </span>
				</h4>
			</div>
			<div class="body_content">
				<!-- Fund based Financial year breakup -->
				<div class="panel panel-default">
					<form class="form-horizontal" action="" method="post">
						<div class="col-md-12">
							<div class="col-md-2">
								<span>Select Fiscal Year</span>
							</div>
							<div class="col-md-2">
								<?php $fy_year = ( date('m') > 6) ? date('y') + 1 : date('y'); ?>
								<select class="form-control" id="fy" name="fy"
									required="required"">
									<option value="">-- Please Select --</option>
									<option value="priorfy">Prior</option>
									<option value="fytodate">FY<?php echo $fy_year; ?> expended to date</option>
									<option value="fybalance">FY<?php echo $fy_year; ?> remaining</option>
									<?php foreach($years as $year): ?>
									<option value="<?php echo $year['financial_years'] ?>">
									<?php echo $year['financial_years'] ?>
									</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-2">
								<input type="submit" name="postSubmit" class="btn btn-theme"
									value="Submit" style="margin-top: 0px !important;" />
							</div>
						</div>
					</form>
					<?php if(isset($project_wise_costs)) { ?>
					<div class="panel-heading">
						<h5>
						<?php echo $financial_year; ?>
						</h5>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<table id="dataTable_exp1" class="table table-striped">
									<thead>
										<tr>
											<th>Project</th>
											<?php foreach($fundLists as $fundList): ?>
											<th><?php echo $fundList['fund_name'] ?></th>
											<?php endforeach; ?>
											<th>Sum</th>
										</tr>
									</thead>
									<tbody>

									<?php foreach($project_wise_costs as $project_wise_cost): ?>

									<?php if($project_wise_cost['flag'] == 1) { ?>
										<tr style="border: 2px solid red;">
										<?php } else { ?>
										<tr>
										<?php } ?>
											<td><?php echo $project_wise_cost['project_name'] ?></td>
											<?php foreach($fundLists as $fundList): ?>
											<?php if ($project_wise_cost[$fundList['fund_name']]) { ?>
											<td>$ <?php echo number_format(preg_replace('/\s+/', '', $project_wise_cost[$fundList['fund_name']])) ?>
											</td>
											<?php } else {?>
											<td><?php echo "-" ?></td>
											<?php } ?>
											<?php endforeach; ?>
											<?php if ($project_wise_cost['sum']) { ?>
											<td>$ <?php echo number_format(preg_replace('/\s+/', '', $project_wise_cost['sum'])) ?>
											</td>
											<?php } else {?>
											<td><?php echo "-" ?></td>
											<?php } ?>
										</tr>
										<?php endforeach; ?>
									</tbody>
									<tfoot>
										<tr>
											<td><b>Sum</b></td>
											<?php foreach($fundLists as $fundList): ?>
											<?php if ($fund_total[$fundList['fund_name']]['total']) { ?>
											<td>$ <?php echo number_format(preg_replace('/\s+/', '', $fund_total[$fundList['fund_name']]['total'])) ?>
											</td>
											<?php } else {?>
											<td><?php echo "-" ?></td>
											<?php } ?>
											<?php endforeach; ?>
											<td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $grand_total)) ?>
											</b></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<!-- Fund based Financial year breakup -->
			</div>
		</div>
	</div>
</div>
