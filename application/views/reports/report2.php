    <div class="container-fluid main_content_body" id="main-body">
      <div class="row">
        <div class="col-md-12">
          <div class="content_body_top">
            <h4>
                <a href="<?php echo site_url('phase'); ?>" class="btn btn-lg btn-default" id="">Masters</a>
                <a href="<?php echo site_url('project'); ?>" class="btn btn-lg list-add-btn" id="">Projects</a>
                <a href="<?php echo site_url('report'); ?>" class="btn btn-lg btn-default" id="">Reports</a>
                <span>
                    <a href="<?php echo site_url('project'); ?>" id="">Project Info</a>
                    <a href="<?php echo site_url('project_funds'); ?>" id="">Funds</a>
                    <a href="<?php echo site_url('project_phase_contract'); ?>" id="">Phase to Contract Mapping</a>
                    <a href="<?php echo site_url('financial_plan'); ?>" id="">Financial Plan</a>
                    <a href="<?php echo site_url('report1'); ?>" id="">Report 1</a>
                    <a href="<?php echo site_url('report2'); ?>" class="active" id="">Report 2</a>
                    <a href="<?php echo site_url('report3'); ?>" id="">Report 3</a>
                </span>
                <span class="project_name">
                  <?php echo $project_name; ?>
                </span>
            </h4>
          </div>
          <div class="body_content">

            <div class="panel panel-default">
              <div class="panel-heading">
                <h5>Estimated Cost</h5>
              </div>

              <div class="panel-body">
                <div class="row">
                  <a class="pull-right btn btn-primary" href="<?php echo site_url()?>/export/report2"><i class="fa fa-file-excel-o"></i>Export Report to Excel</a>
                </div>
                <?php $year = ( date('m') > 6) ? date('y') + 1 : date('y'); ?>
                <div class="row">
                  <div class="col-md-12">
                    <table id="dataTable_exp1" class="table table-striped">
                      <thead>
                        <tr>
                          <th>Project Component</th>
                          <th>Prior</th>
                          <th>FY<?php echo $year ?> expended to date</th>
                          <th>FY<?php echo $year ?> remaining</th>
                          <?php if (!empty($years)): ?>
                          <?php foreach($years as $year): ?>
                            <th><?php echo $year['financial_years'] ?></th>
                          <?php endforeach; ?>
                          <?php endif; ?>
                          <th>Sum</th>
                        </tr>
                      </thead>
                        <tbody>

                          <?php foreach($phase_estimated_costs as $estimated_costs): ?>
                          <?php if ($estimated_costs['sum'] != 0): ?>
                            <?php if($estimated_costs['flag'] == 1) { ?>
                              <tr style="border: 2px solid red;">
                            <?php } else { ?>
                              <tr>
                            <?php } ?>

                                <td><?php echo $estimated_costs['phase_name'] ?></td>

                                <?php if ($estimated_costs['prior']) { ?>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $estimated_costs['prior'])) ?></td>
                                <?php } else {?>
                                  <td><?php echo "-" ?></td>
                                <?php } ?>

                                <?php if ($estimated_costs['expended']) { ?>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $estimated_costs['expended'])) ?></td>
                                <?php } else {?>
                                  <td><?php echo "-" ?></td>
                                <?php } ?>

                                <?php if ($estimated_costs['remaining']) { ?>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $estimated_costs['remaining'])) ?></td>
                                <?php } else {?>
                                  <td><?php echo "-" ?></td>
                                <?php } ?>

                                <?php for($i=0; $i < count($estimated_costs['funds']); $i++ ) {?>

                                  <?php if ($estimated_costs['funds'][$i]) { ?>
                                    <td>$ <?php echo number_format(preg_replace('/\s+/', '', $estimated_costs['funds'][$i])) ?></td>
                                  <?php } else {?>
                                    <td><?php echo "-" ?></td>
                                  <?php } ?>

                                <?php } ?>

                                <?php if ($estimated_costs['sum']) { ?>
                                    <td>$ <?php echo number_format(preg_replace('/\s+/', '', $estimated_costs['sum'])) ?></td>
                                <?php } else {?>
                                  <td><?php echo "-" ?></td>
                                <?php } ?>
                              </tr>
                          <?php endif; ?>
                          <?php endforeach; ?>

                        </tbody>
                        <tfoot>
                          <tr>
                            <td><b>Sum</b></td>
                            <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $totals['prior'])) ?></b></td>
                            <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $totals['expended'])) ?></b></td>
                            <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $totals['remaining'])) ?></b></td>
                            <?php for ($i=0;$i<count($totals['funds']);$i++) { ?>
                                <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $totals['funds'][$i])) ?></b></td>
                            <?php } ?>
                            <td><b>$ <?php echo $totals['prop_base'] ?></b></td>
                          </tr>
                        </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Fund based Financial year breakup -->

              <div class="panel panel-default">
                <form class="form-horizontal" action="" method="post">

                  <div class="col-md-12">
                    <div class="col-md-2">
                      <span>Select Fund Source</span>
                    </div>
                    <div class="col-md-2">
                      <select class="form-control" id="fund_id" name="fund_id" required="required"">
                        <option value="">--  Please Select  --</option>
                      <?php foreach($fundLists as $fundList): ?>
                        <option value="<?php echo $fundList['fund_id'] ?>"> <?php echo $fundList['fund_name'] ?> </option>
                      <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <input type="submit" name="postSubmit" class="btn btn-theme" value="Submit" style="margin-top: 0px !important;" />
                    </div>
                  </div>

                </form>

              <?php if(isset($phase_wise_costs)) { ?>
                <div class="panel-heading">
                  <h5><?php echo $fund_name; ?></h5>
                </div>

                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-12">
                      <table id="dataTable_exp1" class="table table-striped">
                        <thead>
                          <tr>
                            <th>Project Component</th>
                            <th>Prior</th>
                            <th>FY18 expended to date</th>
                            <th>FY18 remaining</th>
                            <?php foreach($years as $year): ?>
                              <th><?php echo $year['financial_years'] ?></th>
                            <?php endforeach; ?>
                            <th>Sum</th>
                          </tr>
                        </thead>
                          <tbody>

                            <?php foreach($phase_wise_costs as $phase_wise_cost): ?>
                            <?php if ($phase_wise_cost['sum'] != 0): ?>

                              <?php if($phase_wise_cost['flag'] == 1) { ?>
                                <tr style="border: 2px solid red;">
                              <?php } else { ?>
                                <tr>
                              <?php } ?>

                                <td><?php echo $phase_wise_cost['phase_name'] ?></td>

                                <?php if ($phase_wise_cost['prior']) { ?>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $phase_wise_cost['prior'])) ?></td>
                                <?php } else {?>
                                  <td><?php echo "-" ?></td>
                                <?php } ?>

                                <?php if ($phase_wise_cost['expended']) { ?>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $phase_wise_cost['expended'])) ?></td>
                                <?php } else {?>
                                  <td><?php echo "-" ?></td>
                                <?php } ?>

                                <?php if ($phase_wise_cost['remaining']) { ?>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $phase_wise_cost['remaining'])) ?></td>
                                <?php } else {?>
                                  <td><?php echo "-" ?></td>
                                <?php } ?>

                                <?php for($i=0; $i < count($phase_wise_cost['funds']); $i++ ) {?>

                                  <?php if ($phase_wise_cost['funds'][$i]) { ?>
                                    <td>$ <?php echo number_format(preg_replace('/\s+/', '', $phase_wise_cost['funds'][$i])) ?></td>
                                  <?php } else {?>
                                    <td><?php echo "-" ?></td>
                                  <?php } ?>

                                <?php } ?>

                                <?php if ($phase_wise_cost['sum']) { ?>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $phase_wise_cost['sum'])) ?></td>
                                <?php } else {?>
                                  <td><?php echo "-" ?></td>
                                <?php } ?>

                              </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>

                          </tbody>
                          <tfoot>
                            <tr>
                              <td><b>Sum</b></td>
                              <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $fund_totals['prior'])) ?></b></td>
                              <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $fund_totals['expended'])) ?></b></td>
                              <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $fund_totals['remaining'])) ?></b></td>
                              <?php for ($i=0;$i<count($fund_totals['funds']);$i++) { ?>
                                  <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $fund_totals['funds'][$i])) ?></b></td>
                              <?php } ?>
                              <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $fund_totals['prop_base'])) ?></b></td>
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
