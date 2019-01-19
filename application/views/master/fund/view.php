    <div class="container-fluid main_content_body" id="main-body">
      <div class="row">
        <div class="col-md-12">
          <div class="content_body_top">
            <h4> 
              <a href="<?php echo site_url('phase'); ?>" class="btn btn-lg list-add-btn" id="">Masters</a>
              <a href="<?php echo site_url('project'); ?>" class="btn btn-lg btn-default" id="">Projects</a>
              <a href="<?php echo site_url('report'); ?>" class="btn btn-lg btn-default" id="">Reports</a>
              <span>
                <a href="<?php echo site_url('phase'); ?>">PROJECT PHASES</a>
                <a href="<?php echo site_url('fund'); ?>" class="active">FUNDS</a>
                <a href="<?php echo site_url('invoice'); ?>">PROCESS INVOICES</a>
              </span>
            </h4>
          </div>
          <div class="body_content">
            
            <div class="panel panel-default" id="">
                <div class="panel-heading">
                  <h5>View Fund Source</h5>
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
                              <span><?php echo $fund_name ?></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label class="col-sm-12" for="">Allocated Amount (<i class="fa fa-usd" aria-hidden="true"></i>):</label>
                            <div class="col-sm-12">
                              <span><?php echo $allocated_amount ?></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label class="col-sm-12" for="">Available Amount (<i class="fa fa-usd" aria-hidden="true"></i>):</label>
                            <div class="col-sm-12">
                              <span><?php echo $available_amount ?></span>
                            </div>
                          </div>
                        </div>
                      
                          <div class="col-md-2">
                          <div class="form-group">
                            <label class="col-sm-12" for="">Status:</label>
                            <div class="col-sm-12">
                              <span><?php if($is_active != 0) { echo "Active"; ?> <?php } else { ?> <?php echo "InActive"; } ?> </span>
                            </div>
                          </div>
                        </div>

                      </div>
                      <div class="row" id="tBody"></div>
                        <div class="col-md-12 text-center" style="">
                         <a href="<?php echo site_url('fund') ?>" class="btn btn-danger btn-sm" data-toggle="tooltip">Back</a>
                      </div>
                    </form>
                    </div>
                  </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                  <h5>Fund List</h5>
              </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-12">
                      <table id="dataTable_exp" class="table table-striped">
                        <thead>
                          <tr>
                            <th>Fund Source</th>
                            <th>Allocated Amount</th>
                            <th>Available Amount</th>
                            <th>Status</th>
                            <th >Action</th>
                          </tr>
                        </thead>
                          <tbody>
                            <?php foreach($fund_lists as $fund_list): ?>                           
                            <tr>
                              <td><?php echo $fund_list['fund_name'] ?></td>
                              <td><?php echo $fund_list['allocated_amount'] ?></td>
                              <td><?php echo $fund_list['available_amount'] ?></td>
                              <td>
                                <?php if($fund_list['is_active'] != 0) { echo "Active"; ?>
                                <?php  } else {?>
                                <?php echo "InActive"; } ?>                                  
                              </td>
                              <td>
                                <a href="<?php echo site_url('fund/view/'.$fund_list['fund_id']); ?>" class="btn btn-success btn-sm" data-toggle="tooltip" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                <a href="<?php echo site_url('fund/edit/'.$fund_list['fund_id']); ?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                <a href="<?php echo site_url('fund/delete/'.$fund_list['fund_id']); ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-trash" aria-hidden="true" onclick="return confirm('Are you sure to delete?')"></i></a>
                                <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="History"><i class="fa fa-history" aria-hidden="true"></i></button>
                              </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                              <td><b>Total</b></td>
                              <td><?php echo $total_allocated_amount ?></td>
                              <td><?php echo $total_available_amount ?></td>
                              <td></td>
                              <td></td>
                            </tr>
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

