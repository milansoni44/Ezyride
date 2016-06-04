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
                    <?php echo form_open(uri_string(), array('class' => 'form-horizontal', 'role' => 'form')); ?>

                    <div class="form-group">
                        <label for="first_name" class="col-lg-2 col-sm-2 control-label">First Name</label>
                        <div class="col-lg-10">
                            <?php echo form_input($first_name); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="col-lg-2 col-sm-2 control-label">Last Name</label>
                        <div class="col-lg-10">
                            <?php echo form_input($last_name); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="company" class="col-lg-2 col-sm-2 control-label">Company Name</label>
                        <div class="col-lg-10">
                            <?php echo form_input($company); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="col-lg-2 col-sm-2 control-label">Phone</label>
                        <div class="col-lg-10">
                            <?php echo form_input($phone); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="col-lg-2 col-sm-2 control-label">Password</label>
                        <div class="col-lg-10">
                            <?php echo form_input($password); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm" class="col-lg-2 col-sm-2 control-label">Confirm Password</label>
                        <div class="col-lg-10">
                            <?php echo form_input($password_confirm); ?>
                        </div>
                    </div>

                    <?php if ($this->ion_auth->is_admin()): ?>
                    
                    <div class="form-group">
                        <label for="password" class="col-lg-2 col-sm-2 control-label">Member of groups</label>
                        <div class="col-lg-10">
                       
                        <?php foreach ($groups as $group): ?>
                            <label class="checkbox">
                                <?php
                                $gID = $group['id'];
                                $checked = null;
                                $item = null;
                                foreach ($currentGroups as $grp) {
                                    if ($gID == $grp->id) {
                                        $checked = ' checked="checked"';
                                        break;
                                    }
                                }
                                ?>
                                <input type="checkbox" name="groups[]" value="<?php echo $group['id']; ?>"<?php echo $checked; ?>>
                                <?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                        <?php endforeach ?>
                        </div>
                    </div>
                

                    <?php endif ?>

                    <?php echo form_hidden('id', $user->id); ?>
                   
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button type="submit" name="submit" class="btn btn-primary">Edit User</button>
                        </div>
                    </div>
                

                    <?php echo form_close(); ?>
                </div>
            </section>
        </div></div>
</section>
