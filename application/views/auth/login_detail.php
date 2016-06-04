<?php $this->load->view('layout/header'); ?>

<!-- page heading start-->
<div class="page-heading">
    <h3>
        Login Detail
    </h3>
</div>
<!-- page heading end-->

<!--body wrapper start-->
<div class="wrapper">
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <header class="panel-heading">
                    Login Detail

                </header>
                <div class="panel-body">
                    <div class="adv-table">
                        <table  class="display table table-bordered table-striped" id="dynamic-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Admin Name</th>
                                    <th>Login Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result) {
                                    foreach ($result as $login_detail) {
                                        ?>
                                        <tr class="gradeX">
                                            <td><?php echo $login_detail->id; ?></td>
                                            <td><?php
                                                $admin_name = $this->ion_auth->user()->row();
                                                echo $admin_name->first_name . ' ' . $admin_name->last_name;
                                                ?></td>
                                            <td><?php echo $login_detail->login_time; ?></td>
                                        </tr>
                                    <?php
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Admin Name</th>
                                    <th>Login Date</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="panel-body">
                    <p><?php echo anchor('auth/create_user', lang('index_create_user_link')) ?> | <?php echo anchor('auth/create_group', lang('index_create_group_link')) ?></p>
                </div>
            </section>
        </div>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>