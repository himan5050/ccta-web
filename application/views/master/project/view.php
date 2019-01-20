    <div class="container-fluid main_content_body" id="main-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="content_body_top">
                        <h4>
                            <a href="<?php echo site_url('phase'); ?>" class="btn btn-lg btn-default" id="">Masters</a>
                            <a href="<?php echo site_url('project'); ?>" class="btn btn-lg list-add-btn" id="">Projects</a>
                            <a href="<?php echo site_url('report'); ?>" class="btn btn-lg btn-default" id="">Reports</a>
                            <span>
                                <a href="<?php echo site_url('project'); ?>" class="active" id="">Project Info</a>
                                <a href="<?php echo site_url('project_funds'); ?>" id="">Funds</a>
                                <a href="project_ptoc.html" id="">Phase to Contract Mapping</a>
                                <a href="project_financial.html" id="">Financial Plan</a>
                                <a href="project_reports.html" id="">Reports</a>
                            </span>
                        </h4>
                    </div>
                    <div class="body_content">
                        <div class="panel panel-default" id="">
                            <div class="panel-heading">
                                <h5>View Project</h5>
                                <?php if (isset($error_msg)) { ?>
                                             <h5><b style="color: red;"><?php echo $error_msg ?></b></h5>
                                <?php } ?>
                            <?php if (isset($success_msg)) { ?>
                                             <h5><b style="color: green;"><?php echo $success_msg ?></b></h5>
                            <?php } ?>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form method="post" class="form-horizontal" action="">
                                            <div class="col-md-3">
                                              <div class="form-group">
                                                <label class="col-sm-12" for="">Project Name:</label>
                                                <div class="col-sm-12">
                                                  <p><?php echo !empty($project_name)?$project_name:''; ?></p>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col-md-3">
                                              <div class="form-group">
                                                <label class="col-sm-12" for="">Description:</label>
                                                <div class="col-sm-12">
                                                  <p style="text-align: justify; text-justify: inter-word;"><?php echo !empty($description)?$description:''; ?></p>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col-md-2">
                                              <div class="form-group">
                                                <label class="col-sm-12" for="">Start Date:</label>
                                                <div class="col-sm-12">
                                                  <p><?php echo !empty($start_date)?$start_date:''; ?></p>
                                                </div>
                                              </div>
                                            </div>

                                            <div class="col-md-2">
                                              <div class="form-group">
                                                <label class="col-sm-12" for="">End Date:</label>
                                                <div class="col-sm-12">
                                                  <p><?php echo !empty($end_date)?$end_date:''; ?></p>
                                                </div>
                                              </div>
                                            </div>

                                            <div class="col-md-2">
                                              <div class="form-group">
                                                <label class="col-sm-12" for="">Status:</label>
                                                <div class="col-sm-12">
                                                  <p><?php if($is_active != 0) { echo "Active"; ?> <?php 
} else { ?> <?php echo "InActive"; 
                                                        } ?></p>
                                                </div>
                                              </div>
                                            </div>

                                            <div class="col-md-12 text-center">
                                                <a href="<?php echo site_url('project') ?>" class="btn btn-danger btn-sm" data-toggle="tooltip">Back</a>
                                            </div>
                                        </form>
                                    </div>
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
                                              <th>Project Name</th>
                                              <th style="width:  57% !important;">Description</th>
                                              <th>Start date</th>
                                              <th>End date</th>
                                              <th>Status</th>
                                              <th>Action</th>
                                            </tr>
                                            </thead>
                                            <?php foreach($project_lists as $project_list): ?>
                                                <tbody>
                                                    <tr>
                                                      <td><?php echo $project_list['project_name'] ?></td>
                                                      <td style="text-align: justify; text-justify: inter-word;"><?php echo $project_list['description'] ?></td>
                                                      <td><?php echo $project_list['start_date'] ?></td>
                                                      <td><?php echo $project_list['end_date'] ?></td>
                                                      <td>
                                                        <?php if($project_list['is_active'] != 0) { echo "Active"; ?>
                                                        <?php  } else {?>
                                                            <?php echo "InActive"; 
                                                        } ?>
                                                      </td>
                                                      <td>
                                                        <a href="<?php echo site_url('project/view/'.$project_list['project_id']); ?>" class="btn btn-success btn-sm" data-toggle="tooltip" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                        <a href="<?php echo site_url('project/edit/'.$project_list['project_id']); ?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                        <a href="<?php echo site_url('project/delete/'.$project_list['project_id']); ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-trash" aria-hidden="true" onclick="return confirm('Are you sure to delete?')"></i></a>
                                                        <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="History"><i class="fa fa-history" aria-hidden="true"></i></button>
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
