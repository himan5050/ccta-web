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
                  <a href="<?php echo site_url('project_funds'); ?>"  id="">Funds</a>
                  <a href="<?php echo site_url('project_phase_contract'); ?>"  id="">Phase to Contract Mapping</a>
                  <a href="<?php echo site_url('financial_plan'); ?>" class="active"id="">Financial Plan</a>
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
            <div class="panel panel-default" id="">
                <div class="panel-heading">
                  <h5>Financial Plan</h5>
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
            <?php $year = ( date('m') > 6) ? date('y') + 1 : date('y'); ?>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-12">
                      <form class="form-horizontal" action="" method="post" id="financeform">
                        <div class="table-responsive">
                          <table class="table noborder_table">
                             <thead>
                              <tr>
                                <th>Project Phases:</th>
                                <th>Contract:</th>
                                <th>Fund Source:</th>
                                <!-- <th>Current Base (<i class="fa fa-usd" aria-hidden="true"></i>):</th> -->
                                <th>Base Line (<i class="fa fa-usd" aria-hidden="true"></i>):</th>
                                <th>EAC (<i class="fa fa-usd" aria-hidden="true"></i>):</th>
                                <th>Base Line (<i class="fa fa-usd" aria-hidden="true"></i>) - EAC (<i class="fa fa-usd" aria-hidden="true"></i>):</th>
                                <th>Prior FYs Expended to date </th>
                                <th>FY<?php echo $year ?> Expended to date</th>
                                <th>FY<?php echo $year ?> Balance</th>
                                <th>Financial Year:</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td style="width: 153px;">
                                  <select class="form-control phase" id="phase0" name="phase" required="required" onchange="populateContract(this)">
                                      <option value="">--Select--</option>
                                        <?php foreach($phases as $phase): ?>
                                        <option value="<?php echo $phase['phase_id'] ?>"><?php echo $phase['phase_name'] ?></option>
                                        <?php endforeach; ?>
                                  </select>
                                </td>

                                <td style="width: 153px;">
                                    <select class="form-control contract" name="contract" id="contract0" disabled="true">
                                      <option>--None--</option>
                                    </select>
                                </td>

                                <td style="width: 153px;">
                                  <select class="form-control fund" id="fund" disabled="true" name="fund" required="required" onchange="check(this)">
                                    <option value="">--Select--</option>
                                    <?php foreach($funds as $fund): ?>
                                        <option value="<?php echo $fund['fund_id'] ?>"><?php echo $fund['fund_name'] ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                </td>

                                <!-- <td>
                                  <input type="text" name="curr_base" class="form-control number" value="0" id="curr_base" placeholder="" required="required" onblur="verify(this)">
                                </td> -->

                                <td>
                                  <input type="text" name="prop_base" class="form-control number" value="0" id="prop_base" placeholder="" required="required" onblur="verify2(this)">
                                </td>

                                <td>
                                  <input type="text" name="eac" class="form-control number" value="0" id="eac" placeholder="" required="required" onblur="verify1(this)">
                                </td>

                                <td>
                                  <input type="text" name="unfunded" class="form-control number" value="0" id="unfunded" placeholder="" readonly="readonly">
                                </td>

                                <td>
                                  <input type="text" name="priorfy" class="form-control number" value="0" id="priorfy" placeholder="" readonly="readonly" onblur="verify3(this)">
                                </td>

                                <td>
                                  <input type="text" name="fytodate" class="form-control number" value="0" id="fytodate" placeholder="" readonly="readonly" onblur="verify3(this)">
                                </td>

                                <td>
                                  <input type="text" name="fybalance" class="form-control number" value="0" id="fybalance" placeholder="" readonly="readonly" onblur="verify3(this)">
                                </td>

                                <td id="yearicon" style="display: none;">
                                   <button type="button" class="btn  btn-primary" onclick="openyearmodal()" ><i class="fa fa-calendar" aria-hidden="true"></i></button>
                                </td>

                              </tr>
                            </tbody>
                            <!-- <tfoot id="tBody1">

                            </tfoot> -->
                          </table>
                      </div>

                        <div class="col-md-12 text-center">
                          <input type="submit" name="postSubmit" class="btn btn-theme hide" value="Submit"/>
                          <button type="button" onclick="checkform()" class="btn btn-theme">Submit</button>
                          <button type="Reset" class="btn btn-theme">Reset</button>
                      </div>

                      <div id="mod">
                       <!-- Modal content start-->
                            <div id="yearModal" class="custom_modal modal fade" role="dialog">
                              <div class="modal-dialog">

                                <div class="modal-content">
                                  <div class="modal-header">
                                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                                    <h4 class="modal-title">Financial Year</h4>
                                  </div>
                                  <div class="row">
                                    <div class="col-md-12">
                                      <div class="col-md-3"><span><b>Rem. Prop Base Amount</b></span></div>
                                      <div class="col-md-3"><b><i class="fa fa-usd" aria-hidden="true"><span id="modaleac">0</span></i></b></div>
                                      <div class="col-md-3"><span><b>Rem. to allocate</b></span></div>
                                      <div class="col-md-3"><b><i class="fa fa-usd" aria-hidden="true"><span id="remainingeac">0</span></i></b></div>
                                    </div>
                                  </div>
                                  <div class="modal-body">
                                    <div class="row">
                                      <div class="col-md-10 col-md-offset-1">
                                          <div class="row">
                                            <div class="col-md-12" >
                                              <div class="col-md-5">
                                                <div class="form-group">
                                                <label class="col-sm-12" for="">Year:</label>
                                                </div>
                                              </div>
                                              <div class="col-md-5">
                                                <div class="form-group">
                                                  <label class="col-sm-12" for="">Amount (<i class="fa fa-usd" aria-hidden="true"></i>) :</label>
                                                </div>
                                              </div>
                                              <div class="col-md-2">
                                                <div class="form-group">
                                                  <button type="button" class="btn btn-primary" style="margin-left: 13px;" onclick="addMore()"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="row" id="tBody">

                                          </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-default" onclick="verifymodal()">Save</button>
                                    <button type="button" class="btn btn-default" onclick="resetmodal()">Cancel</button>
                                  </div>
                                </div>

                              </div>
                            </div>
                          <!-- Modal content End-->
                      </div>
                    </form>
                    </div>
                  </div>
                </div>
            </div>
            <input type="hidden" name="count" id="count" value="1">

            <div class="panel panel-default">
              <div class="panel-heading">
                <h5>Financial Plan List</h5>
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-12">
                    <table id="dataTable_exp" class="table table-striped">
                            <thead>
                              <tr>
                                <th>Project Phases</th>
                                <th>Contract</th>
                                <th>Fund Source</th>
                                <!-- <th>Current Base</th> -->
                                <th>Base Line</th>
                                <th>EAC</th>
                                <th>Base Line - EAC</th>
                                <th><b style="display:block; width:100px;">Prior FYs Expended to date</b></th>
                                <th><b style="display:block; width:100px;">FY<?php echo $year ?> Expended to date</th>
                                <th><b style="display:block; width:100px;">FY<?php echo $year ?> Balance</th>
                              <!--  <th>Start Date</th>
                                <th>End Date</th> -->
                                <th>Financial Year</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($financial_plan_lists)) { ?>
                                    <?php foreach($financial_plan_lists as $financial_plan_list): ?>
                                <tr>
                                  <td style="width:200px;"><?php echo $financial_plan_list['project_phase'] ?></td>
                                  <td><?php echo $financial_plan_list['contract'] ?></td>
                                  <td  style="width:150px;"><?php echo $financial_plan_list['fund_name'] ?></td>
                                  <!-- <td>$ <php echo number_format(preg_replace('/\s+/', '', $financial_plan_list['curr_base'])) ?></td> -->
                                  <td style="width:200px;">$ <?php echo number_format(preg_replace('/\s+/', '', $financial_plan_list['prop_base']), 0) ?></td>
                                  <td style="width:200px;">$ <?php echo number_format(preg_replace('/\s+/', '', $financial_plan_list['eac']), 0) ?></td>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', ($financial_plan_list['prop_base'] - $financial_plan_list['eac'])), 0) ?></td>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $financial_plan_list['priorfy']), 0) ?></td>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $financial_plan_list['fytodate']), 0) ?></td>
                                  <td>$ <?php echo number_format(preg_replace('/\s+/', '', $financial_plan_list['fybalance']), 0) ?></td>
                                <!--  <td><?php // echo $financial_plan_list['start_date'] ?></td>
                                  <td><?php // echo $financial_plan_list['end_date'] ?></td> -->
                                  <td><button type="button" class="btn  btn-primary" onclick="financial_year(<?php echo $financial_plan_list['financial_id'] ?>)"><i class="fa fa-calendar" aria-hidden="true"></i></button></td>
                                  <td style="width:200px;">
                                    <a href="<?php echo site_url('financial_plan/edit/'.$financial_plan_list['financial_id']); ?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                    <a href="<?php echo site_url('financial_plan/delete/'.$financial_plan_list['financial_id']); ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-trash" aria-hidden="true" onclick="return confirm('Are you sure to delete?')"></i></a>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="History" onclick="openhistory(<?php echo $financial_plan_list['financial_id'] ?>)"><i class="fa fa-history" aria-hidden="true"></i></button>
                                  </td>
                                </tr>
                                    <?php endforeach; ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <td colspan="3"><b> Total</b></td>
                                <!-- <td><b>$ <php echo number_format(preg_replace('/\s+/', '', $curr_base) )?></b></td> -->
                                <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $prop_base))?></b></td>
                                <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $eac))?></b></td>
                                <td colspan="4"></td>
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
          <h4 class="modal-title">Financial Plan History</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="dataTable_modal" class="table table-striped">
                <thead>
                  <tr>
                    <th>Project Phases</th>
                    <th>Contract</th>
                    <th>Fund Source</th>
                    <!-- <th>Current Base</th> -->
                    <th>Base Line</th>
                    <th>EAC</th>
                    <th>Prior FYs Expended to date</th>
                    <th>FY18 Expended to date</th>
                    <th>FY18 Balance</th>
                    <th>Date Added</th>
                  </tr>
                </thead>
                <tbody id="history">
                  <tr>
                    <td></td>
                    <td></td>
                   <!--  <td></td> -->
                    <td></td>
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
          url: "<?php echo site_url(); ?>/financial_plan/history",
          type:"post",
          dataType:"json",
          data:{financial_id:id },
          success:function(response)
          {
            if(response)
            { var html = "";
              $("#history").empty();
              for (var i = 0; i < response.length; i++)
              {
                html += '<tr><td>'+ response[i]['phase_name'] +'</td><td>'+ response[i]['contract_name'] +'</td><td>'+ response[i]['fund_name'] +'</td><td>$ '+ changeNumber(response[i]['prop_base']) +'</td><td>$ '+ changeNumber(response[i]['eac']) +'</td><td>$ '+ changeNumber(response[i]['priorfy']) +'</td><td>$ '+ changeNumber(response[i]['fytodate']) +'</td><td>$ '+ changeNumber(response[i]['fybalance']) +'</td><td>'+ response[i]['date_added'] +'</td></tr>';
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

  <!-- Modal 2-->
  <div class="custom_modal modal fade" id="myfinancialyear" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
          <h4 class="modal-title">Financial Years</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="dataTable_modal" class="table table-striped">
                <thead>
                  <tr>
                    <th>Year</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody id="financialplanbody">
                  <tr>
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

    function financial_year(id)
    {
      $.ajax(
        {
          url: "<?php echo site_url(); ?>/financial_plan/financial_year",
          type:"post",
          dataType:"json",
          data:{financial_id:id },
          success:function(response)
          {
            if(response)
            { var html = "";
              $("#financialplanbody").empty();
              for (var i = 0; i < response.length; i++)
              {
                html += '<tr><td>'+ response[i]['financial_years'] +'</td><td>$ '+ changeNumber(response[i]['amount']) +'</td></tr>';
              }

              $("#financialplanbody").append(html);
              $("#myfinancialyear").modal();
            }
            else
            {
              alert("No Financial Year Break up found !!");
            }
          }
        }
      );
    }


  </script>

  <!-- Modal 2 End -->

<script>

  $(document).ready(function() {
    // var table = $("#dataTable_exp").DataTable({'aoColumnDefs': [{ 'bSortable': false, 'aTargets': [-1] }]});
    var table = $("#dataTable_exp").dataTable({"ordering": false});

    $('#dataTable_exp_wrapper').find(".row").eq(0).find(".col-sm-6").removeClass("col-sm-6").addClass("col-sm-2");

    var dt_filter = '<div class="col-md-8" style="padding:0;"><form class="form-horizontal" action="" method="post" id="formfilters">'+
            '<div class="col-md-4"><div class="row" style="margin:0;"> <label class="col-sm-6 " style="font-weight:500;">Project Phase:</label> <div class="col-sm-6" style="padding:0;"> <select class="form-control input-sm" name="phasefilter" id="phasefilter" style="width:100%;" onchange="populateContractfilter(this)"><option value="">--select--</option><?php foreach($phases as $phase): ?><option value="<?php echo $phase['phase_id'] ?>"><?php echo $phase['phase_name'] ?></option><?php
           endforeach; ?></select></div></div></div>'+
            '<div class="col-md-3" style="padding:0;"><div class="row" style="margin:0;"> <label class="col-sm-5 " style="font-weight:500;">Contract:</label> <div class="col-sm-7" style="padding:0;"><select class="form-control contract" name="contractfilter" id="contractfilter" disabled="true" style="width:100%;" ><option>--None--</option></select></div></div></div>'+
            '<div class="col-md-3" style="padding:0;"><div class="row" style="margin:0;"> <label class="col-sm-7 " style="font-weight:500;">Fund Source:</label> <div class="col-sm-5" style="padding:0;"> <select class="form-control input-sm" name="fundfilter" id="fundfilter" style="width:100%;"><option value="">--select--</option><?php foreach($funds as $fund): ?><option value="<?php echo $fund['fund_id'] ?>"><?php echo $fund['fund_name'] ?></option><?php
           endforeach; ?></select> </div></div></div>'+
            '<div class="col-md-2"><input type="submit" name="postFilter" class="btn btn-sm btn-success" value="Apply"/></form></div>'+

              '</div>';
    $('#dataTable_exp_wrapper').find(".row").eq(0).find(".col-sm-2").eq(0).after(dt_filter);
    $('#dataTable_exp_wrapper').find(".row").eq(0).css("margin-bottom", "10px");

  } );


</script>

<script type="text/javascript">
  $('input.number').keyup(function(event) {

  // skip for arrow keys
  if(event.which >= 37 && event.which <= 40) return;

  // format number
  $(this).val(function(index, value) {
    return value
    .replace(/\D/g, "");
    /*.replace(/\B(?=(\d{3})+(?!\d))/g, ",");*/
  });
});
</script>

<script type="text/javascript">
    $(function () {
      //get current date
      var today = new Date();
      //get current month
      var curMonth = today.getMonth();
      var fiscalYr = "";
      if (curMonth > 6) { //
        var fiscalYr = (today.getFullYear() + 2).toString();
      } else {
        var fiscalYr = (today.getFullYear() + 1).toString();
      }
      $('.datetimepicker').datetimepicker({
        viewMode: 'years',
        format: 'YYYY',
        minDate: new Date(fiscalYr, 0, 1)
      });
    });

    $(document).keydown(function (e)
    {
      var keycode1 = (e.keyCode ? e.keyCode : e.which);
      if (keycode1 == 0 || keycode1 == 9)
      {
          e.preventDefault();
          e.stopPropagation();
      }
    });
</script>


<script type="text/javascript">

  function addMore()
  {
    var cnt = $("#count").val();

      var htmldata = '<div class="col-md-12" id="myDiv'+cnt+'"><div class="col-md-5">'
           +'<div class="form-group">'
           +'<div class="col-sm-12">'
           +'<input type="text" name="year[]" class="form-control datetimepicker" id="datetimepicker'+cnt+'" placeholder="" onblur="check2(this)">'
             +'</div> </div></div><div class="col-md-5">'
           +'<div class="form-group">'
           +'<div class="col-sm-12">'
           +'<input type="text" name="amount[]" class="form-control number" id="amount'+cnt+'" placeholder="" onblur="checkmodal(this)">'
           +'</div>'
           +'</div></div><div class="col-md-2">'
          +'<button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete" '
          +' onclick="deleteRow('+cnt+')"><i class="fa fa-minus" aria-hidden="true"></i>'
          +'</button>'
          +'</div> </div></div>';

      $("#tBody").append(htmldata);


      var x = parseInt(cnt)+1;
      $("#count").val(x);

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

    //get current date
    var today = new Date();

    //get current month
    var curMonth = today.getMonth();

    var fiscalYr = "";
    if (curMonth > 6) { //
        var fiscalYr = (today.getFullYear() + 2).toString();
    } else {
        var fiscalYr = (today.getFullYear() + 1).toString();
    }

    $('.datetimepicker').datetimepicker({
      viewMode: 'years',
      format: 'YYYY',
      minDate: new Date(fiscalYr, 0, 1)
    });

  }

  function deleteRow(id)
  {
    $("#myDiv"+id).remove();
  }

</script>

<script type="text/javascript">

  //Ajax call to populate Contract.
  function populateContract(i) {
    var phase_id = i.value;
    var a = i.id;

    var x = $("#"+a).parent().parent().find(".contract").attr('id');
    var f = $("#"+a).parent().parent().find(".fund").attr('id');

    $.ajax(
        {
          url: "<?php echo site_url(); ?>/financial_plan/contractlist",
          type:"post",
          dataType:"json",
          data:{ phase_id:phase_id },
          success:function(response)
          {
            $("#"+x).empty();

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

            $("#"+x).removeAttr('disabled');
            $("#"+x).prop('required',true);
            $("#"+f).removeAttr('disabled');
            $("#"+x).append(html);
          }
        }
      );
  }

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

  //Ajax call to check for duplicate
  function check(i)
  {
    var fund_id = i.value;
    var a = i.id;

    var f = $("#"+a).parent().parent().find(".phase").val();
    var x = $("#"+a).parent().parent().find(".contract").val();

    $.ajax(
        {
          url: "<?php echo site_url(); ?>/financial_plan/check",
          type:"post",
          dataType:"json",
          data:{ phase_id:f, fund_id:fund_id, contract:x },
          success:function(response)
          {
            if(response && response != '')
              {
                if(response != 0)
                {
                  alert("Project Phase To Contract To Fund Source Already Exist")
                  $("#"+a).val("");
                }
              }
          }
        }
      );
  }

  function check2(i)
  {
    var x = i.value;

    var z = i.id;

    if(x)
    {
      var years = document.getElementsByName("year[]");

      for(var i=0; i<years.length; i++)
      {
        for (var j = 0; j < years.length; j++)
        {
          var cmp1 = years[i].value;
          var cmp2 = years[j].value;

          if(cmp1 && cmp2)
          {
            if(i != j)
            {
              if(cmp1 == cmp2)
              {
                alert("Can't enter duplicate Year");
                $("#"+z).val("");
              }
            }
          }
        }
      }
    }
  }

  //Ajax call to check allocated amount should not be smaller
  function verify(i)
  {
    var amount = parseInt(i.value);
    var a = i.id;
    var fund_id = $("#fund").val();

    if(amount)
    {
      if(fund_id != 0)
      {
        $.ajax(
        {
          url: "<?php echo site_url(); ?>/financial_plan/verify",
          type:"post",
          dataType:"json",
          data:{ fund_id:fund_id},
          success:function(response)
          {
            if(response && response != '')
              {
                var available = parseInt(response);

                if(amount > available)
                {
                //  alert("Amount entered is more than available amount for the fund source selected")
                //  $("#"+a).val(0);
                }
              }
          }
        });
      }
      else
      {
        alert("Please select fund source");
        $("#"+a).val(0);
      }
    }
    if(!amount && amount!=0)
    {
      alert("Current field cannot be empty");
      $("#"+a).val(0);
    }
  }

  //Ajax call to check allocated amount should not be smaller
  function verify1(i)
  {
    var prop_base = parseInt($("#prop_base").val());
    var amount = parseInt(i.value);
    var unfunded = prop_base - amount;
    $('#unfunded').val(unfunded);
    var a = i.id;

    var fund_id = $("#fund").val();

    if(amount)
    {
      if(fund_id != 0)
      {
        $.ajax(
        {
          url: "<?php echo site_url(); ?>/financial_plan/verify",
          type:"post",
          dataType:"json",
          data:{ fund_id:fund_id},
          success:function(response)
          {
            if(response && response != '')
              {
                var available = parseInt(response);

                if(amount > available)
                {
                //  alert("Amount entered is more than available amount for the fund source selected")
                //  $("#"+a).val(0);
                }
              }
          }
        });
      }
      else
      {
        alert("Please select fund source");
        $("#"+a).val(0);
      }
    }

    if(!amount && amount!=0)
    {
      alert("Current field cannot be empty");
      $("#"+a).val(0);
    }
  }

  //Ajax call to check allocated amount should not be smaller
  function verify2(i)
  {
    var amount = parseInt(i.value);
    var a = i.id;

    var fund_id = $("#fund").val();

    if (amount)
    {
      if(fund_id != 0)
      {
        $.ajax(
        {
          url: "<?php echo site_url(); ?>/financial_plan/verify",
          type:"post",
          dataType:"json",
          data:{ fund_id:fund_id},
          success:function(response)
          {
            if(response && response != '')
              {
                var available = parseInt(response);

                if(amount > available)
                {
               //   alert("Amount entered is more than available amount for the fund source selected")
               //   $("#"+a).val(0);
               //   $("#priorfy").prop('readonly',true);
               //   $("#fytodate").prop('readonly',true);
               //   $("#fybalance").prop('readonly',true);
                    $("#priorfy").prop('readonly',false);
                    $("#fytodate").prop('readonly',false);
                    $("#fybalance").prop('readonly',false);
                }
                else
                {
                  $("#priorfy").prop('readonly',false);
                  $("#fytodate").prop('readonly',false);
                  $("#fybalance").prop('readonly',false);
                }
              }
          }
        });
      }
      else
      {
        alert("Please select fund source");
        $("#"+a).val(0);
      }
    }

    if (amount == 0)
    {
      $("#priorfy").val(0);
      $("#fytodate").val(0);
      $("#fybalance").val(0);

      $("#priorfy").prop('readonly',true);
      $("#fytodate").prop('readonly',true);
      $("#fybalance").prop('readonly',true);
    }

   if(!amount && amount!=0)
    {
      alert("Current field cannot be empty");
      $("#"+a).val(0);

      $("#priorfy").val(0);
      $("#fytodate").val(0);
      $("#fybalance").val(0);

      $("#priorfy").prop('readonly',true);
      $("#fytodate").prop('readonly',true);
      $("#fybalance").prop('readonly',true);
    }
  }

  //Ajax call to check allocated amount should not be smaller
  function verify3(i)
  {
    var prop_base = parseInt($("#prop_base").val());

    var priorfy = parseInt($("#priorfy").val());

    var fytodate = parseInt($("#fytodate").val());

    var fybalance = parseInt($("#fybalance").val());

    if (!priorfy && priorfy !=0 )
    {
      alert("Prior Financial Year expended to date can't be empty");
      $("#priorfy").val(0);
    }

    if (!fytodate && fytodate !=0 )
    {
      alert("Financial Year expended to date can't be empty");
      $("#fytodate").val(0);
    }

    if (!fybalance && fybalance !=0 )
    {
      alert("Financial Year balance can't be empty");
      $("#fybalance").val(0);
    }

    if((priorfy || priorfy==0) && (fytodate || fytodate==0) && (fybalance || fybalance==0))
    {
      var fysum = priorfy + fytodate + fybalance;
      var remaining = prop_base - fysum;

      $("#yearicon").show();
      $("#modaleac").text(remaining);
      $("#remainingeac").text(remaining);
      $("#yearicon").show();
    }

  }

  function checkmodal(i)
  {
    var x = i.value;

    var z = i.id;

    var available = parseInt($("#modaleac").text());

    var total = 0;

    if(x)
    {
      var amounts = document.getElementsByName("amount[]");

      for(var i=0; i<amounts.length; i++)
      {
        var cmp = amounts[i].value;

        if(cmp)
        {
          total = total + parseInt(cmp);
        }
      }
    }

    if(total > available)
    {
      alert("Sum of all the amount can't be greater than Remaining Prop base amount");
      $("#"+z).val("");
    }
    else
    {
      var remaining = available-total;
      $("#remainingeac").text(remaining);
    }
  }

  function verifymodal()
  {
    var amounts = document.getElementsByName("amount[]");

    var years = document.getElementsByName("year[]");

    var count = 0;

    for(var i=0; i<amounts.length; i++)
    {
      var cmp1 = amounts[i].value;
      var cmp2 = years[i].value;

      if (cmp1 && cmp2)
      {
        count++;
      }
    }

    if(count == amounts.length)
    {
      $("#yearModal").modal('hide');
    }
    else
    {
      alert("Year and Amount can't be empty")
    }

  }

  function resetmodal()
  {
    $("#tBody").empty();
    $("#remainingeac").text(0);
    $("#yearModal").modal('hide');
  }

</script>

<script type="text/javascript">

  function checkform()
  {
      var x = $("#contract0").val();
      if(x)
      {
        $('#financeform').find('[type="submit"]').trigger('click');
      }
      else
      {
        alert("Please select Project Phase");
        $("#phase0").prop('selectedIndex',0);
      }
  }

  function openyearmodal()
  {
    $('#yearModal').modal({
      backdrop: 'static',
      keyboard: false
    })
  }

</script>
