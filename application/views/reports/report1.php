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
                    <a href="<?php echo site_url('report1'); ?>" class="active" id="">Report 1</a>
                    <a href="<?php echo site_url('report2'); ?>" id="">Report 2</a>
                    <a href="<?php echo site_url('report3'); ?>" id="">Report 3</a>
                </span>
                <span class="project_name">
                  <?php echo $project_name; ?>
                </span>
            </h4>
          </div>
          <div class="body_content">
            <div class="panel panel-default">
              <!-- <div class="panel-heading">
                <h5>Estimated Cost</h5>
              </div> -->

              <div class="panel-body">
                <div class="row">
                  <a class="pull-right btn btn-primary" href="<?php echo site_url()?>/export/report1"><i class="fa fa-file-excel-o"></i>Export Report to Excel</a>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <table id="dataTable_exp1" class="table table-striped">
                      <thead>
                        <tr>
                          <th>Project Component</th>
                          <th>Costs</th>

                          <?php foreach($funds as $fund): ?>
                            <th><?php echo $fund['fund_name'] ?></th>
                          <?php endforeach; ?>

                        </tr>
                      </thead>
                        <tbody>

                          <?php foreach($phase_estimated_costs as $estimated_costs): ?>
                          <?php if ($estimated_costs['amount']): ?>
                            <tr>
                              <td><?php echo $estimated_costs['phase_name'] ?></td>

                              <?php if ($estimated_costs['amount']) { ?>
                                <td>$ <?php echo number_format(preg_replace('/\s+/', '', $estimated_costs['amount'])) ?></td>
                              <?php } else {?>
                                <td><?php echo "-" ?></td>
                              <?php } ?>

                              <?php for($i=0; $i < count($estimated_costs['funds']); $i++) {?>

                                <?php if ($estimated_costs['funds'][$i]) { ?>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $estimated_costs['funds'][$i])) ?></td>
                                <?php } else { ?>
                                  <td><?php echo "-" ?></td>
                                <?php } ?>
                              <?php } ?>
                            </tr>
                          <?php endif; ?>
                          <?php endforeach; ?>

                        </tbody>
                        <tfoot>
                          <tr>
                            <td><b>Sum</b></td>
                            <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $phaseTotal)) ?></b></td>
                            <?php if (!empty($total_funds)): ?>
                            <?php foreach($total_funds as $fund): ?>
                              <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $fund['amount'])) ?></b></td>
                            <?php endforeach; ?>
                            <?php endif; ?>
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
