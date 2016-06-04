<?php $this->load->view('layout/header'); ?>

<div class="page-heading">
    <h3>
        Change Password
    </h3>
</div>
<!-- page heading end-->

<!--body wrapper start-->
<section class="wrapper">
    <!-- page start-->
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">

                <div class="panel-body">

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("auth/change_password",array('class' => 'form-horizontal'));?>

     <div class="form-group">
                        <label for="group_name" class="col-lg-2 col-sm-2 control-label">Old Password</label>
                        <div class="col-lg-10">
            <?php echo form_input($old_password);?>
                        </div>
     </div>

     <div class="form-group">
                        <label for="group_name" class="col-lg-2 col-sm-2 control-label">New Password</label>
                        <div class="col-lg-10">
            <?php echo form_input($new_password);?>
                        </div>
     </div>

     <div class="form-group">
                        <label for="group_name" class="col-lg-2 col-sm-2 control-label">Confirm Password</label>
                        <div class="col-lg-10">
            <?php echo form_input($new_password_confirm);?>
                        </div>
     </div>
<?php echo form_input($user_id);?>
 <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
      
      <?php echo form_submit('submit', lang('change_password_submit_btn'),array('class' => 'btn btn-primary'));?>
                        </div>
 </div>

<?php echo form_close();?>

                </div>
            </section>
        </div>
    </div>
</section>

<?php $this->load->view('layout/footer'); ?>