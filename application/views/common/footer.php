        <div class="container-fluid" id="footer">
            <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <p class="footer-para">©2018 FicusDash. All Rights Reserved.<span style="float: right;">20180930 V1.1</span></p>
               </div>
          </div>
        </div>

<link rel="stylesheet" href="<?php echo base_url(); ?>asset/plugins/bootstrap_datetime/bootstrap-datetimepicker.css">
<script src="<?php echo base_url(); ?>asset/plugins/bootstrap_datetime/moment.js"></script>
<script src="<?php echo base_url(); ?>asset/plugins/bootstrap_datetime/bootstrap-datetimepicker.js"></script>
<script type="text/javascript">
            $(function () {
                $('#datetimepicker4').datetimepicker({
                    format: 'MM/DD/YYYY'
                });
                $('#datetimepicker5').datetimepicker({
                    format: 'MM/DD/YYYY'
                });
            });
</script>

<script src="<?php echo base_url(); ?>asset/plugins/data_table/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>asset/plugins/data_table/dataTables.bootstrap.min.js"></script>
<script>
  $(function () {
    $("#dataTable_exp").DataTable({
               'aoColumnDefs': [{ 'bSortable': false, 'aTargets': [-1] }]
    });

    $('#dataTable_modal').DataTable({ "searching": false, "ordering": false, "paging": false, "info": false } );

    $("#togglePanel_btn").click(function(){
        $("#togglePanel").toggleClass("hidden");
    });
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>

</body>
</html>
