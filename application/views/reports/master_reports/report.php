<div class="container-fluid main_content_body" id="main-body">
    <div class="row">
        <div class="col-md-12">
            <div class="content_body_top">
                <h4>
                    <a href="<?php echo site_url('phase'); ?>"
                        class="btn btn-lg btn-default" id="">Masters</a> <a
                        href="<?php echo site_url('project'); ?>"
                        class="btn btn-lg btn-default" id="">Projects</a> <a
                        href="<?php echo site_url('report'); ?>"
                        class="btn btn-lg list-add-btn" id="">Reports</a> <span> <a
                        href="<?php echo site_url('report'); ?>" class="active" id="">Fund
                            Source Report</a> <a
                        href="<?php echo site_url('report/fy_report'); ?>" id="">Fiscal
                            Year Report</a> </span>
                </h4>
            </div>
            <div class="body_content">
                <!-- Fund based Financial year breakup -->
                <div class="panel panel-default">
                    <form class="form-horizontal" action="" method="post">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <span>Select Fund Source</span>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="fund_id" name="fund_id"
                                    required="required"">
                                    <option value="">-- Please Select --</option>
            <?php foreach($fundLists as $fundList): ?>
                                    <option value="<?php echo $fundList['fund_id'] ?>">
                <?php echo $fundList['fund_name'] ?>
                                    </option>
            <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="submit" name="postSubmit" class="btn btn-theme"
                                    value="Submit" style="margin-top: 0px !important;" />
                            </div>
                        </div>
                    </form>
        <?php if(isset($project_wise_costs)) { ?>
                    <div class="panel-heading">
                        <h5>
            <?php echo $fund_name; ?>
                        </h5>
                    </div>
                    <div class="panel-body">
            <?php $y = ( date('m') > 6) ? date('y') + 1 : date('y'); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="dataTable_exp1" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Project</th>
                                            <th>Prior</th>
                                            <th>FY<?php echo $y ?> expended to date</th>
                                            <th>FY<?php echo $y ?> remaining</th>
            <?php foreach($years as $year): ?>
                                            <th><?php echo $year['financial_years'] ?>
                                            </th>
            <?php endforeach; ?>
                                            <th>Sum</th>
                                        </tr>
                                    </thead>
                                    <tbody>

            <?php foreach($project_wise_costs as $project_wise_cost): ?>

                <?php if($project_wise_cost['flag'] == 1) { ?>
                                        <tr style="border: 2px solid red;">
                <?php } else { ?>


                                        <tr>
                <?php } ?>
                                            <td><?php echo $project_wise_cost['project_name'] ?>
                                            </td>
                <?php if ($project_wise_cost['prior']) { ?>
                                            <td>$ <?php echo number_format(preg_replace('/\s+/', '', $project_wise_cost['prior'])) ?>
                                            </td>
                <?php } else {?>
                                            <td><?php echo "-" ?>
                                            </td>
                <?php } ?>
                <?php if ($project_wise_cost['expended']) { ?>
                                            <td>$ <?php echo number_format(preg_replace('/\s+/', '', $project_wise_cost['expended'])) ?>
                                            </td>
                <?php } else {?>
                                            <td><?php echo "-" ?>
                                            </td>
                <?php } ?>
                <?php if ($project_wise_cost['remaining']) { ?>
                                            <td>$ <?php echo number_format(preg_replace('/\s+/', '', $project_wise_cost['remaining'])) ?>
                                            </td>
                <?php } else {?>
                                            <td><?php echo "-" ?>
                                            </td>
                <?php } ?>
                <?php for($i=0; $i < count($project_wise_cost['funds']); $i++ ) {?>
                    <?php if ($project_wise_cost['funds'][$i]) { ?>
                                            <td>$ <?php echo number_format(preg_replace('/\s+/', '', $project_wise_cost['funds'][$i])) ?>
                                            </td>
                    <?php } else {?>
                                            <td><?php echo "-" ?>
                                            </td>
                    <?php } ?>
                <?php } ?>
                <?php if ($project_wise_cost['sum']) { ?>
                                            <td>$ <?php echo number_format(preg_replace('/\s+/', '', $project_wise_cost['sum'])) ?>
                                            </td>
                <?php } else {?>
                                            <td><?php echo "-" ?>
                                            </td>
                <?php } ?>
                                        </tr>
            <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td><b>Sum</b>
                                            </td>
                                            <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $total_prior)) ?>
                                            </b>
                                            </td>
                                            <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $total_expended)) ?>
                                            </b>
                                            </td>
                                            <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $total_remaining)) ?>
                                            </b>
                                            </td>
            <?php foreach($total_years as $year): ?>
                                            <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $year)) ?>
                                            </b>
                                            </td>
            <?php endforeach; ?>
                                            <td><b>$ <?php echo number_format(preg_replace('/\s+/', '', $total_sum)) ?>
                                            </b>
                                            </td>
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
