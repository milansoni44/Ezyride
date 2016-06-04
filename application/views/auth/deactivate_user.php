<?php $this->load->view('layout/header'); ?>

<!-- page heading start-->
<div class="page-heading">
    <h3>
        Deactivate User
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
                    <p><?php echo sprintf(lang('deactivate_subheading'), $user->username); ?></p>

                    <?php echo form_open("auth/deactivate/" . $user->id); ?>

                    <p>
                        <?php echo lang('deactivate_confirm_y_label', 'confirm'); ?>
                        <input type="radio" name="confirm" value="yes" checked="checked" />
                        <?php echo lang('deactivate_confirm_n_label', 'confirm'); ?>
                        <input type="radio" name="confirm" value="no" />
                    </p>

                    <?php echo form_hidden(array('id' => $user->id)); ?>

                    <p><?php echo form_submit('submit', lang('deactivate_submit_btn')); ?></p>

                    <?php echo form_close(); ?>

                </div>
            </section>
            <?php $this->load->view('layout/footer'); ?>