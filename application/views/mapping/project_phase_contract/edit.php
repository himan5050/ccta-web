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
                                <a href="<?php echo site_url('project_phase_contract'); ?>" class="active" id="">Phase to Contract Mapping</a>
                                <a href="<?php echo site_url('financial_plan'); ?>" id="">Financial Plan</a>
                                <a href="<?php echo site_url('report1'); ?>" id="">Report 1</a>
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
                        <div class="panel-heading">
                          <h5>Edit Phase to Contract Mapping</h5>
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
                              <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
                                <div class="row">
                                  <input type="hidden" id="project_phase_contract_id" value="<?php echo $post['project_phase_contract_id'] ?>" >
                                  <div class="col-md-2">
                                    <div class="form-group">
                                      <label class="col-sm-12" for="">Phase Name:</label>
                                      <div class="col-sm-12">
                                        <select disabled class="form-control" id="phase" name="phase_id" onchange="check1(this)">
                                            <option value="">--  Please Select  --</option>
                                            <?php foreach($phases as $phase): ?>
                                            <option value="<?php echo $phase['phase_id'] ?>" <?php if($phase['phase_id'] == $post['phase_id']) { echo 'selected="selected"';
} ?> > <?php echo $phase['phase_name'] ?> </option>
                                            <?php endforeach; ?>
                                          </select>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="col-md-1">
                                    <div class="form-group">
                                      <label class="col-sm-12" for="">Contract:</label>
                                      <div class="col-sm-12">
                                        <input type="text" class="form-control" id="contract" placeholder=""  name="contract" onblur="check(this)" value="<?php echo !empty($post['contract'])?$post['contract']:''; ?>">
                                      </div>
                                    </div>
                                  </div>

                                  <div class="col-md-1">
                                    <div class="form-group">
                                      <label class="col-sm-12" for="">Amount:</label>
                                      <div class="col-sm-12">
                                        <input type="text" required class="form-control" id="amount" required="required" placeholder=""  name="amount" onblur="check(this)" value="<?php echo !empty($post['amount'])?$post['amount']:''; ?>">
                                      </div>
                                    </div>
                                  </div>

                                  <div class="col-md-2">
                                    <div class="form-group">
                                      <label class="col-sm-12" for="">Description:</label>
                                      <div class="col-sm-12">
                                        <textarea class="form-control" id="" name="description" placeholder=""><?php echo $post['description']; ?></textarea>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="col-md-2">
                                    <div class="form-group">
                                      <label class="col-sm-12" for="">Start Date:</label>
                                      <div class="col-sm-12">
                                        <input type='text' name="start_date" class="form-control" id='datetimepicker4' required="required" value="<?php echo !empty($post['contract_start_date'])?$post['contract_start_date']:''; ?>" />
                                      </div>
                                    </div>
                                  </div>

                                  <div class="col-md-2">
                                    <div class="form-group">
                                      <label class="col-sm-12" for="">End Date:</label>
                                      <div class="col-sm-12">
                                        <input type='text' name="end_date" class="form-control" id='datetimepicker5' required="required" value="<?php echo !empty($post['contract_end_date'])?$post['contract_end_date']:''; ?>"/>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="col-md-1">
                                    <div class="form-group">
                                      <label class="col-sm-12" for="">Attachment:</label>
                                      <div class="col-sm-12">
                                        <span><a href="<?php echo base_url(); ?>uploads/<?php echo $post['attachment']; ?>" target="_blank"> <?php echo $post['attachment'] ?></a></span>
                                        <input type="file" name="attachment" class="form-control" id="" placeholder="" value="">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="row" id="tBody"></div>
                                <div class="col-md-12 text-center">
                                  <input type="submit" name="postSubmit" class="btn btn-theme" value="Submit"/>
                                  <button type="Reset" class="btn btn-theme">Reset</button>
                                  <a href="<?php echo site_url('project_phase_contract') ?>" class="btn btn-theme">Cancel</a>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="panel panel-default">
                          <div class="panel-heading">
                            <h5>Phase to Contract Mapping List</h5>
                          </div>
                          <div class="panel-body">
                            <div class="row">
                              <div class="col-md-12">
                                <table id="dataTable_exp_1" class="table table-striped">
                                  <thead>
                                    <tr>
                                      <th>Phase Name</th>
                                      <th>Contract</th>
                                      <th>Amount</th>
                                      <th>Description</th>
                                      <th>Attachment</th>
                                      <th>Start Date</th>
                                      <th>End Date</th>
                                      <th>Action</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php if(isset($project_phase_contract_lists)) { ?>
                                        <?php foreach($project_phase_contract_lists as $project_phase_contract_list): ?>
                                    <tr>
                                      <td><?php echo $project_phase_contract_list['phase_id'] ?></td>
                                      <td><?php echo $project_phase_contract_list['contract'] ?><b><sup style="color:red; font-size: 14px;"><?php if (($project_phase_contract_list['status'] == 'Expiring') || ($project_phase_contract_list['status'] == 'Expired')) { echo $project_phase_contract_list['status']; 
} ?></sup></b></td>
                                      <td style="width:200px;">$ <?php echo number_format(preg_replace('/\s+/', '', $project_phase_contract_list['amount']), 0) ?></td>
                                      <td><?php echo $project_phase_contract_list['description'] ?></td>
                                      <td><a href="<?php echo base_url(); ?>uploads/<?php echo $project_phase_contract_list['attachment']; ?>" target="_blank"> <?php echo $project_phase_contract_list['attachment'] ?></a></td>
                                      <td>
                                            <?php if ($project_phase_contract_list['status'] == 'Closed') { ?>
                                                <?php echo 'Closed'; ?>
                                            <?php } else { ?>
                                                <?php echo $project_phase_contract_list['start_date'] ?>
                                            <?php } ?>
                                      </td>
                                      <td>
                                            <?php if ($project_phase_contract_list['status'] == 'Closed') { ?>
                                                <?php echo 'Closed'; ?>
                                            <?php } else { ?>
                                                <?php echo $project_phase_contract_list['end_date'] ?>
                                            <?php } ?>
                                      </td>
                                      <td style="width:200px;">
                                            <?php if ($project_phase_contract_list['status'] == 'Closed') { ?>
                                          <a href="<?php echo site_url('project_phase_contract/edit/'.$project_phase_contract_list['project_phase_contract_id']); ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Renew"><i class="fa fa-check" aria-hidden="true"></i></a>
                                            <?php } else { ?>
                                          <a href="<?php echo site_url('project_phase_contract/disable/'.$project_phase_contract_list['project_phase_contract_id']); ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Close"><i class="fa fa-close" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        <a href="<?php echo site_url('project_phase_contract/edit/'.$project_phase_contract_list['project_phase_contract_id']); ?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                        <a href="<?php echo site_url('project_phase_contract/delete/'.$project_phase_contract_list['project_phase_contract_id']); ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-trash" aria-hidden="true" onclick="return confirm('Are you sure to delete?')"></i></a>
                                        <button type="button" class="btn btn-info btn-sm details
                                        -control" data-toggle="tooltip" title="History" onclick="openhistory(<?php echo $project_phase_contract_list['project_phase_contract_id'] ?>)"><i class="fa fa-history" aria-hidden="true"></i></button>
                                      </td>
                                    </tr>
                                        <?php endforeach; ?>
                                    <?php } else {
                                    } ?>
                                  </tbody>
                                  <tfoot>
                                    <tr>
                                      <td></td>
                                      <td><b>Total funds allocated to contracts</b></td>
                                      <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $total_contracts_amount), 0) ?></b></td>
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
          <h4 class="modal-title">Phase to Contract History</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="dataTable_modal" class="table table-striped">
                <thead>
                  <tr>
                    <th>Phase Name</th>
                    <th>Contract</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Attachment</th>
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

  $(document).ready(function() {
    var table = $("#dataTable_exp_1").dataTable({"ordering": false});

    $('#dataTable_exp_1_wrapper').find(".row").eq(0).find(".col-sm-6").removeClass("col-sm-6").addClass("col-sm-2");

    var dt_filter = '<div class="col-md-8" style="padding:0;"><form class="form-horizontal" action="" method="post" id="formfilters">'+
            '<div class="col-md-4"><div class="row" style="margin:0;"> <label class="col-sm-6 " style="font-weight:500;">Project Phase:</label> <div class="col-sm-6" style="padding:0;"> <select class="form-control input-sm" name="phasefilter" id="phasefilter" style="width:100%;" onchange="populateContractfilter(this)"><option value="">--select--</option><?php foreach($phases as $phase): ?><option value="<?php echo $phase['phase_id'] ?>"><?php echo $phase['phase_name'] ?></option><?php 
           endforeach; ?></select></div></div></div>'+
            '<div class="col-md-3" style="padding:0;"><div class="row" style="margin:0;"> <label class="col-sm-5 " style="font-weight:500;">Contract:</label> <div class="col-sm-7" style="padding:0;"><select class="form-control input-sm" name="contractfilter" id="contractfilter" disabled="true" style="width:100%;" ><option>--None--</option></select></div></div></div>'+
            '<div class="col-md-3" style="padding:0;"><div class="row" style="margin:0;"> <label class="col-sm-5 " style="font-weight:500;">Amount:</label> <div class="col-sm-7" style="padding:0;"><input type="text" class="form-control amount input-sm" id="amountfilter" placeholder="" name="amountfilter" size="15" onblur="check(this)"></div></div></div>'+
            '<div class="col-md-2"><input type="submit" name="postFilter" class="btn btn-sm btn-success" value="Apply"/></form></div>'+

              '</div>';
    $('#dataTable_exp_1_wrapper').find(".row").eq(0).find(".col-sm-2").eq(0).after(dt_filter);
    $('#dataTable_exp_1_wrapper').find(".row").eq(0).css("margin-bottom", "10px");

  } );

  //Ajax call to populate Filter Contract.
  function populateContractfilter(i)
  {
    var phase_id = i.value;
    var a = i.id;

    if(phase_id)
    {
      $.ajax(
        {
          url: "<?php echo site_url(); ?>/financial_plan/contractlist",
          type:"post",
          dataType:"json",
          data:{ phase_id:phase_id },
          success:function(response)
          {
            $("#contractfilter").empty();

            if(response && response != '')
              {
                html = '<option value="">--Select--</option>';
                for(i = 0; i < response.length; i++)
                {
                  html += '<option value="'+ response[i]["contract"] +'">'+ response[i]["contract"] +'</option>';
                }
              }
              else
              {
                html = '<option value="">--Empty--</option>';
              }

            $("#contractfilter").removeAttr('disabled');
            $("#contractfilter").prop('required',false);
            $("#contractfilter").append(html);
          }
        }
      );
    }
    else
    {
      $("#contractfilter").empty();
      $("#contractfilter").append('<option value="">--Select--</option>');
      $("#contractfilter").prop('disabled','disabled');
      $("#contractfilter").prop('required',false);
    }
  }

    function openhistory(id)
    {
      $.ajax(
        {
          url: "<?php echo site_url(); ?>/project_phase_contract/history",
          type:"post",
          dataType:"json",
          data:{phase_contract_id:id },
          success:function(response)
          {
            if(response)
            { var html = "";
              $("#history").empty();
              for (var i = 0; i < response.length; i++)
              {
                html += '<tr><td>'+ response[i]['phase_name'] +'</td><td>'+ response[i]['contract'] +'</td><td>'+ response[i]['amount'] +'</td><td>'+ response[i]['description'] +'</td><td><a href="<?php echo base_url(); ?>uploads/'+ response[i]['attachment'] +'"target="_blank">'+ response[i]['attachment'] +'</td><td>'+ response[i]['date_added'] +'</td></tr>';
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

<script type="text/javascript">
  function check(i)
  {
    var x = i.value;

    var z = i.id;

    var y = $("#phase").val();

    var id = $('#project_phase_contract_id').val();

    if(x && y)
    {
      $.ajax(
        {
          url: "<?php echo site_url(); ?>/project_phase_contract/checkedit",
          type:"post",
          dataType:"json",
          data:{ contract_name : x, phase_id : y, id : id  },
          success:function(response)
          {
            if(response == 1)
            {
              alert("Phase Contract Combination already exist!!");
              $("#"+z).val("");
            }
          }
        }
      );
    }
  }

  function check1(i)
  {
    var x = i.value;

    var z = i.id;

    var y = $("#contract_name").val();

    var id = $('#project_phase_contract_id').val();

    if(x && y)
    {
      $.ajax(
        {
          url: "<?php echo site_url(); ?>/project_phase_contract/checkedit",
          type:"post",
          dataType:"json",
          data:{ contract_name : y, phase_id : x, id : id  },
          success:function(response)
          {
            if(response == 1)
            {
              alert("Phase Contract Combination already exist!!");
              $("#"+z).val("");
            }
          }
        }
      );
    }
  }

</script>
