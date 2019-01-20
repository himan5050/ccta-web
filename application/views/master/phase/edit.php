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
                  <h5>Edit Phase</h5>
                    <?php if (isset($error_msg)) { ?>
                        <h5><b style="color: red;"><?php echo $error_msg ?></b></h5>
                    <?php } ?>
        <?php if (isset($success_msg)) { ?>
                        <h5><b style="color: green;"><?php echo $success_msg ?></b></h5>
        <?php } ?>
                </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                      <form method="post" class="form-horizontal" action="">
                        <div class="row">
                        <div class="col-md-2">
                          <div class="form-group">
                            <label class="col-sm-12" for="">Phase Code:</label>
                            <div class="col-sm-10">
                              <input type="text" name="phase_code" class="form-control" id="phase_code" readonly="readonly" onchange="check()" placeholder="Enter Phase Code" value="<?php echo !empty($post['phase_code'])?$post['phase_code']:''; ?>">
                            </div>
                          </div>
                        </div>
                          <div class="col-md-2">
                          <div class="form-group">
                            <label class="col-sm-12" for="">Phase Name:</label>
                            <div class="col-sm-10">
                              <input type="text" name="phase_name" class="form-control" id="phase_name" required="required" onchange="check()" placeholder="Enter Phase Name" value="<?php echo !empty($post['phase_name'])?$post['phase_name']:''; ?>">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label class="col-sm-11" for="">Description:</label>
                            <div class="col-sm-11">
                              <textarea name="phase_description" class="form-control" id="" placeholder="Enter Description"><?php echo !empty($post['phase_description'])?$post['phase_description']:''; ?></textarea>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label class="col-sm-10" for="">Weight:</label>
                            <div class="col-sm-8">
                              <select class="form-control" id="weight0" name="weight" required="required" onchange="check1(this)" >
                                <option value="">0</option>
                                <?php for($i=1;$i<50;$i++): ?>
                                    <option value="<?php echo $i ?>" > <?php echo $i ?> </option>
                                <?php endfor;; ?>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row" id="tBody"></div>

                        <div class="col-md-12 text-center" style="margin-top: 15px;">
                          <input type="submit" name="postSubmit" class="btn btn-theme" value="Submit"/>
                          <button type="Reset" class="btn btn-theme">Reset</button>
                          <a href="<?php echo site_url('phase') ?>" class="btn btn-theme">Back</a>
                      </div>
                    </form>
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

<script type="text/javascript">
  function check()
  {
    var x = $("#phase_name").val();

    var z = "<?php echo $post['phase_name']; ?>";

    if(x)
    {
      if (z != x)
      {
        $.ajax(
          {
            url: "<?php echo site_url(); ?>/phase/check",
            type:"post",
            dataType:"json",
            data:{ phase_name:x },
            success:function(response)
            {
              if(response == 1)
              {
                alert("Phase already exist!!");
                $("#phase_name").val(z);
              }
            }
          }
        );
      }
    }
  }
</script>
