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
                  <h5>Add New Fund</h5>
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
                        <div class="row">
                        <div class="col-md-2">
                          <div class="form-group">
                            <label class="col-sm-12" for="">Fund Code:</label>
                            <div class="col-sm-12">
                              <input type="text" name="fund_code[]" class="form-control" id="fund_code0" placeholder="Enter Fund Source Code" onblur="check(this)" required="required">
                            </div>
                          </div>
                        </div>
                          <div class="col-md-2">
                          <div class="form-group">
                            <label class="col-sm-12" for="">Fund Source:</label>
                            <div class="col-sm-12">
                              <input type="text" name="fund_name[]" class="form-control" id="fund_name0" placeholder="Enter Fund Source Name" onblur="check(this)" required="required">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label class="col-sm-12" for="">Allocated Amount (<i class="fa fa-usd" aria-hidden="true"></i>):</label>
                            <div class="col-sm-12">
                              <input type="text" name="allocated_amount[]" class="form-control number" id="allocated_amount0" placeholder="0" value="0" required="required" onblur="change(0)" readonly="readonly">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label class="col-sm-12" for="">Available Amount (<i class="fa fa-usd" aria-hidden="true"></i>):</label>
                            <div class="col-sm-12">
                              <input type="text" class="form-control number" id="available_amount0" placeholder="0" value="0" readonly="readonly" >
                            </div>
                          </div>
                        </div>

                        <div class="col-md-2">
                          <div class="form-group">
                            <label class="col-sm-12" for="">Status:</label>
                            <div class="col-sm-12">
                              <select class="form-control" name="is_active[]" id="" placeholder="Enter Description">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                              </select>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-1">
                          <button type="button" class="btn btn-primary" style="margin-top: 23px;" onclick="addMore()"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>
                      </div>
                      <div class="row" id="tBody"></div>
                        <div class="col-md-12 text-center" style="">
                          <input type="submit" name="postSubmit" class="btn btn-theme" value="Submit"/>
                          <button type="Reset" class="btn btn-theme">Reset</button>
                      </div>
                    </form>
                    </div>
                  </div>
                </div>
            </div>
              <input type="hidden" id="add" value="1" readonly="readonly">
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
                          	<th>Fund Code</th>
                            <th>Fund Source</th>
                            <th>Allocated Amount</th>
                            <th>Available Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                          <tbody>
                            <?php foreach($fund_lists as $fund_list): ?>
                              <tr>
                                <td><?php echo $fund_list['fund_code'] ?></td>
                                <td><?php echo $fund_list['fund_name'] ?></td>
                                <td>$ <?php echo number_format(preg_replace('/\s+/', '', $fund_list['allocated_amount'])) ?></td>
                                <td>$ <?php echo number_format(preg_replace('/\s+/', '', $fund_list['available_amount'])) ?></td>
                                <td>
                                  <?php if($fund_list['is_active'] != 0) { echo "Active"; ?>
                                  <?php  } else {?>
                                  <?php echo "InActive"; } ?>
                                </td>
                                <td>
                                  <!-- <a href="<?php echo site_url('fund/view/'.$fund_list['fund_id']); ?>" class="btn btn-success btn-sm" data-toggle="tooltip" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a> -->
                                  <a href="<?php echo site_url('fund/edit/'.$fund_list['fund_id']); ?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                  <a href="<?php echo site_url('fund/delete/'.$fund_list['fund_id']); ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-trash" aria-hidden="true" onclick="return confirm('Are you sure to delete?')"></i></a>
                                  <button type="button" class="btn btn-info btn-sm details-control" data-toggle="tooltip" title="History" onclick="openhistory(<?php echo $fund_list['fund_id'] ?>)"><i class="fa fa-history" aria-hidden="true"></i></button>
                                </td>
                              </tr>
                              <?php endforeach; ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td><b>Total</b></td>
                              <td></td>
                              <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $total_allocated_amount)) ?></b></td>
                              <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $total_available_amount)) ?></b></td>
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
          <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
          <h4 class="modal-title">Fund History</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="dataTable_modal" class="table table-striped">
                <thead>
                  <tr>
                    <th>Fund Source</th>
                    <th>Allocated Amount</th>
                    <th>Available Amount</th>
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

    function openhistory(id)
    {
      $.ajax(
        {
          url: "<?php echo site_url(); ?>/fund/history",
          type:"post",
          dataType:"json",
          data:{fund_id:id },
          success:function(response)
          {
            if(response)
            { var html = "";
              $("#history").empty();
              for (var i = 0; i < response.length; i++)
              {

                html += '<tr><td>'+ response[i]['fund_name'] +'</td><td>$ '+ changeNumber(response[i]['allocated_amount']) +'</td><td>$ '+ changeNumber(response[i]['available_amount']) +'</td><td>'+ response[i]['date_added'] +'</td></tr>';
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

<script type="text/javascript">
  function addMore()
  {
    var cnt= $("#add").val();

      var htmldata = '<div class="col-md-12" id="myDiv'+cnt+'" style="padding:0;"><div class="col-md-2"><div class="form-group"><div class="col-sm-12"><input type="text" name="fund_code[]" class="form-control" id="fund_code'+cnt+'" placeholder="Enter Fund Source Code" onblur="check(this)" required="required"></div></div></div><div class="col-md-2"><div class="form-group"><div class="col-sm-12"><input type="text" name="fund_name[]" class="form-control" id="fund_name'+cnt+'" placeholder="Enter Fund Source Name" onblur="check(this)" required="required"></div></div></div><div class="col-md-2"><div class="form-group"><div class="col-sm-12"><input name="allocated_amount[]" type="text" readonly="readonly" value="0" placeholder="0" class="form-control number" id="allocated_amount'+cnt+'"  placeholder="Enter Allocated Amount" required="required" onblur="change('+cnt+')"></div></div></div><div class="col-md-2"><div class="form-group"><div class="col-sm-12"><input type="text" class="form-control number" id="available_amount'+cnt+'" readonly="readonly" value="0" placeholder="0"></div></div></div><div class="col-md-2"><div class="form-group"><div class="col-sm-12"><select class="form-control" name="is_active[]" id=""><option value="1">Active</option><option value="0">Inactive</option></select></div></div></div><div class="col-md-1" id="del'+cnt+'"><button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete" onclick="deleteRow('+cnt+')" ><i class="fa fa-minus" aria-hidden="true"></i></button></div></div>';
    $("#tBody").append(htmldata);

    var x = parseInt(cnt)+1;
    $("#add").val(x);

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

  }

  function deleteRow(id)
  {
    $("#myDiv"+id).remove();
    var cnt= $("#add").val();
    var x = parseInt(cnt)-1;

    $("#add").val(x);
  }
</script>

<script type="text/javascript">
  function check(i)
  {
    var x = i.value;

    var z = i.id;

    if(x)
    {
      $.ajax(
        {
          url: "<?php echo site_url(); ?>/fund/check",
          type:"post",
          dataType:"json",
          data:{ fund_name:x },
          success:function(response)
          {
            if(response == 1)
            {
              alert("Fund Source already exist!!");
              $("#"+z).val("");
            }
          }
        }
      );
    }

    if(x)
    {
      var funds = document.getElementsByName("fund_name[]");

      for(var i=0; i<funds.length; i++)
      {
        for (var j = 0; j < funds.length; j++)
        {
          var cmp1 = funds[i].value;
          var cmp2 = funds[j].value;

          if(cmp1 && cmp2)
          {
            if(i != j)
            {
              if(cmp1 == cmp2)
              {
                alert("Can't enter duplicate Fund Source Name");
                $("#"+z).val("");
              }
            }
          }
        }
      }
    }
  }

</script>

<script type="text/javascript">
  function change(id)
  {
    $allocated_amount = $("#allocated_amount"+id).val();
    if($allocated_amount)
    {
      $("#available_amount"+id).val($allocated_amount);
    }
    else
    {
      $("#available_amount"+id).val(0);
    }
  }
</script>

<script type="text/javascript">
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
</script>
