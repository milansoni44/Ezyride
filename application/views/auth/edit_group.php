<?php $this->load->view('layout/header'); ?>

<div class="page-heading">
    <h3>
        Edit Group
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
<p><?php echo lang('edit_group_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open(current_url(),array('class' => 'form-horizontal'));?>

     <div class="form-group">
                        <label for="group_name" class="col-lg-2 col-sm-2 control-label">Group Name</label>
                        <div class="col-lg-10">
            <?php echo form_input($group_name);?>
                        </div>
     </div>

     <div class="form-group">
                        <label for="group_description" class="col-lg-2 col-sm-2 control-label">Description</label>
                        <div class="col-lg-10">
            <?php echo form_input($group_description);?>
                        </div>
     </div>

      <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
         <?php echo form_submit('submit', lang('edit_group_submit_btn'),array('class' => 'btn btn-primary'));?>
                        </div>
      </div>

<?php echo form_close();?>
      
                </div>
            </section>
        </div>
    </div>
</section>

<?php $this->load->view('layout/footer'); ?>