<?php $this->load->view('layout/header'); ?>

<!-- page heading start-->
<div class="page-heading">
    <h3>
        Create User
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
                    User Detail
                </header>
                <div class="panel-body">
                    <div id="infoMessage"><?php echo $message; ?></div>
                        
                    <?php echo form_open("auth/create_group", array('class' => 'form-horizontal', 'role' => 'form')); ?>
                    <div class="form-group">
                        <label for="group_name" class="col-lg-2 col-sm-2 control-label">Group Name</label>
                        <div class="col-lg-10">
                            <?php echo form_input($group_name); ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="col-lg-2 col-sm-2 control-label">Description</label>
                        <div class="col-lg-10">
                            <?php echo form_input($description); ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button type="submit" name="submit" class="btn btn-primary">Create Group</button>
                            </div>
                        </div>

                    <?php echo form_close(); ?>
                </div>
            </section>
        </div>
    </div>
</section>

<?php $this->load->view('layout/footer'); ?>