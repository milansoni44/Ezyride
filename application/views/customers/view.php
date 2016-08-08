<?php $this->load->view('layout/header'); ?>

<!-- page heading start-->
<div class="page-heading">
    <h3>
        View Customer
    </h3>
</div>
<!-- page heading end-->

<!--body wrapper start-->
<section class="wrapper">
    <!-- page start-->
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    View App User
                </header>
                <div class="panel-body">
                    <?php echo form_open_multipart(uri_string(), array('class' => 'form-horizontal', 'role' => 'form')); ?>

                    <div class="form-group">
                        <label for="first_name" class="col-lg-2 col-sm-2 control-label">First Name</label>
                        <div class="col-lg-10">
                            <input type="text" name="first_name" value="<?php echo $customer->first_name; ?>" class="form-control" disabled />
                        </div>
                    </div>

                    <!--<div class="form-group">
                        <label for="last_name" class="col-lg-2 col-sm-2 control-label">Last Name</label>
                        <div class="col-lg-10">
                            <input type="text" name="last_name" value="<?php /*echo $customer->last_name; */?>" class="form-control" disabled />
                        </div>
                    </div>-->

                    <div class="form-group">
                        <label for="email" class="col-lg-2 col-sm-2 control-label">Email</label>
                        <div class="col-lg-10">
                            <input type="text" name="email" value="<?php echo $customer->email; ?>" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contact" class="col-lg-2 col-sm-2 control-label">Contact</label>
                        <div class="col-lg-10">
                            <input type="text" name="contact" value="<?php echo $customer->contact; ?>" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dob" class="col-lg-2 col-sm-2 control-label">Dob</label>
                        <div class="col-lg-10">
                            <input type="text" name="dob" value="<?php echo $customer->dob; ?>" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="Gender" class="col-lg-2 col-sm-2 control-label">Gender</label>
                        <div class="col-lg-10">
                            <input type="text" name="gender" value="<?php echo $customer->gender; ?>" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="corp_email" class="col-lg-2 col-sm-2 control-label">Corporate Email</label>
                        <div class="col-lg-10">
                            <input type="text" name="corp_email" value="<?php echo $customer->corp_email; ?>" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="Pan" class="col-lg-2 col-sm-2 control-label">Pancard</label>
                        <div class="col-lg-10">
                            <img src="<?php echo base_url("assets/uploads"); ?>/<?php echo $customer->pan; ?>" width="100px"/>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </section>
        </div></div>
</section>