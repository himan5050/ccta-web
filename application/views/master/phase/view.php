<div class="container-fluid main_content_body" id="main-body">
      <div class="row">
        <div class="col-md-12">
          <div class="content_body_top">
            <h4>
              <a href="<?php echo site_url('phase'); ?>" class="btn btn-lg list-add-btn" id="">Masters</a>
              <a href="<?php echo site_url('project'); ?>" class="btn btn-lg btn-default" id="">Projects</a>
              <a href="<?php echo site_url('report'); ?>" class="btn btn-lg btn-default" id="">Reports</a>
              <span>
                <a href="<?php echo site_url('phase'); ?>" class="active">PROJECT PHASES</a>
                <a href="<?php echo site_url('fund'); ?>">FUNDS</a>
                <a href="<?php echo site_url('invoice'); ?>">PROCESS INVOICES</a>
              </span>
            </h4>
          </div>
          <div class="body_content">
            <div class="panel panel-default" id="">
                <div class="panel-heading">
                  <h5>View Phase</h5>
                  <?php if (isset($error_msg)) { ?>
						<h5><b style="color: red;"><?php echo $error_msg ?></b></h5>
						<?php } ?>
						<?php if (isset($success_msg)) { ?>
						<h5><b style="color: green;"><?php echo $success_msg ?></b></h5>
						<?php } ?>
                </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label class="col-sm-12"><b>Phase Code:</b></label>
                            <div class="col-sm-12" style="border-top: 3px solid #000;">
                              <span><?php echo $phase_code ?></span>
                            </div>
                          </div>
                        </div>
                          <div class="col-md-3">
                          <div class="form-group">
                            <label class="col-sm-12"><b>Phase Name:</b></label>
                            <div class="col-sm-12" style="border-top: 3px solid #000;">
                              <span><?php echo $phase_name ?></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="col-sm-12" for=""><b>Description:</b></label>
                            <div class="col-sm-12" style="border-top: 3px solid #000;">
                              <p style="text-align: justify; text-justify: inter-word;"><?php echo $phase_description ?></p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row" id="tBody"></div>
                        <div class="col-md-12 text-center" style="margin-top: 15px;">
                            <a href="<?php echo site_url('phase') ?>" class="btn btn-danger btn-sm" data-toggle="tooltip">Back</a>
                        </div>
                    </div>
                  </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                  <h5>Phase List</h5>
              </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-12">
                      <table id="dataTable_exp" class="table table-striped">
                              <thead>
                              <tr>
                                <th>Phase Code</th>
                                <th>Phase Name</th>
                                <th>Description</th>
                                <th>Weight</th>
                                <th style="width: 90px !important;">Action</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php foreach($phase_lists as $phase_list): ?>
                                  <tr>
                                    <td><?php echo $phase_list['phase_code'] ?></td>
                                    <td><?php echo $phase_list['phase_name'] ?></td>
                                    <td style="text-align: justify; text-justify: inter-word;"><?php echo $phase_list['phase_description'] ?></td>
                                    <td><?php echo $phase_list['weight'] ?></td>
                                    <td>
                                      <a href="<?php echo site_url('phase/view/'.$phase_list['phase_id']); ?>" class="btn btn-success btn-sm" data-toggle="tooltip" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                      <a href="<?php echo site_url('phase/edit/'.$phase_list['phase_id']); ?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                      <a href="<?php echo site_url('phase/delete/'.$phase_list['phase_id']); ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-trash" aria-hidden="true" onclick="return confirm('Are you sure to delete?')"></i></a>
                                    </td>
                                  </tr>
                              <?php endforeach; ?>
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
