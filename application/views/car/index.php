<?php $this->load->view('layout/header'); ?>

<!-- page heading start-->
<div class="page-heading">
    <h3>
        Car Detail
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
                                    <th>Car Number</th>
                                    <th>Car Model</th>
                                    <th>Car Layout</th>
                                    <th>Image</th>
                                    <th>AC Availability</th>
                                    <th>Music System</th>
                                    <th>Air Bag</th>
                                    <th>Seat Belt</th>
                                    <th>Creation Time</th>
                                    <th>Updation Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               <?php if($result){?>
                                <?php foreach ($result as $car_detail) { ?>
                                <tr class="gradeX">
                                    <td><?php echo $car_detail->id; ?></td>
                                    <td><?php echo $car_detail->car_no; ?></td>
                                    <td><?php echo $car_detail->car_model; ?></td>
                                    <td><?php echo $car_detail->car_layout; ?></td>
                                    <td><?php echo $car_detail->car_image?></td>
                                    <td><?php echo $car_detail->ac_availability; ?></td>
                                    <td><?php echo $car_detail->music_system; ?></td>
                                    <td><?php echo $car_detail->air_bag;?></td>
                                    <td><?php echo $car_detail->seat_belt; ?></td>
                                    <td><?php echo $car_detail->creation_time; ?></td>
                                    <td><?php echo $car_detail->updation_time; ?></td>
                                    <td><?php echo anchor('car_detail/edit/'.$car_detail->id,'Edit'); ?> | <?php echo anchor('car_detail','Delete'); ?></td>
                                </tr>
                               <?php } } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Car Number</th>
                                    <th>Car Model</th>
                                    <th>Car Layout</th>
                                    <th>Image</th>
                                    <th>AC Availability</th>
                                    <th>Music System</th>
                                    <th>Air Bag</th>
                                    <th>Seat Belt</th>
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