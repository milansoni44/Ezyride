<?php $this->load->view('layout/header'); ?>

<!-- page heading start-->
<div class="page-heading">
    <h3>
        Customers Detail
    </h3>
</div>
<!-- page heading end-->

<!--body wrapper start-->
<div class="wrapper">
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <header class="panel-heading">
                    Car Detail
                    <!--<span class="tools pull-right">
                        <a href="" class="fa fa-plus"></a>
                    </span>-->
                </header>
                <div class="panel-body">
                    <div class="adv-table">
                        <table  class="display table table-bordered table-striped" id="dynamic-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Corporate Email</th>
                                    <th>Api Key</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               <?php if(!empty($result)){?>
                                <?php foreach ($result as $cust_detail) { ?>
                                <tr class="gradeX">
                                    <td><?php echo $cust_detail->first_name." ".$cust_detail->last_name; ?></td>
                                    <td><?php echo $cust_detail->email; ?></td>
                                    <td><?php echo $cust_detail->contact; ?></td>
                                    <td><?php echo $cust_detail->corp_email; ?></td>
                                    <td><?php echo $cust_detail->api_key; ?></td>
                                    <td><a href="<?php echo base_url("customers/view"); ?>/<?php echo $cust_detail->id; ?>">View</a> | <a href="<?php echo base_url("customers/deactivate"); ?>/<?php echo $cust_detail->id; ?>" onclick="return confirm('Are you sure you want to deactivate user?');">Deativate</a></td>
                                </tr>
                               <?php } } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Corporate Email</th>
                                    <th>Api Key</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>