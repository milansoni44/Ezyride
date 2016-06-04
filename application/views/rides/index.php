<?php $this->load->view('layout/header'); ?>

<!-- page heading start-->
<div class="page-heading">
    <h3>
        Rides
    </h3>
</div>
<!-- page heading end-->

<!--body wrapper start-->
<div class="wrapper">
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <header class="panel-heading">
                    Rides Detail
                    <span class="tools pull-right">
                        <a href="" class="fa fa-plus"></a>
                    </span>
                </header>
                <div class="panel-body">
                    <div class="adv-table">
                        <table  class="display table table-bordered table-striped" id="dynamic-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Car ID</th>
                                    <th>Ride Date</th>
                                    <th>Ride Time</th>
                                    <th>Prise Per Seat</th>
                                    <th>Seat Availability</th>
                                    <th>Only Ladies</th>
                                    <th>Creation Time</th>
                                    <th>Updation Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result) { ?>
                                    <?php foreach ($result as $rides_detail) { ?>
                                        <tr class="gradeX">
                                            <td><?php echo $rides_detail->id; ?></td>
                                            <td><?php echo $rides_detail->user_id; ?></td>
                                            <td><?php echo $rides_detail->car_id; ?></td>
                                            <td><?php echo $rides_detail->ride_date; ?></td>
                                            <td><?php echo $rides_detail->ride_time; ?></td>
                                            <td><?php echo $rides_detail->price_per_seat; ?></td>
                                            <td><?php echo $rides_detail->seat_availability; ?></td>
                                            <td><?php echo $rides_detail->only_ladies ?></td>
                                            <td><?php echo $rides_detail->creation_time; ?></td>
                                            <td><?php echo $rides_detail->updation_time; ?></td>
                                            <td><?php echo anchor('rides', 'Edit'); ?> | <?php echo anchor('rides', 'Delete'); ?></td>
                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Car ID</th>
                                    <th>Ride Date</th>
                                    <th>Ride Time</th>
                                    <th>Prise Per Seat</th>
                                    <th>Seat Availability</th>
                                    <th>Only Ladies</th>
                                    <th>Creation Time</th>
                                    <th>Updation Time</th>
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