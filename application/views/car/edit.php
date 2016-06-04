<?php $this->load->view('layout/header'); ?>

<!-- page heading start-->
<div class="page-heading">
    <h3>
        Update Car
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
                    Update Car
                </header>
                <div class="panel-body">
                    <div id="infoMessage"><?php echo $message; ?></div>
                    <?php echo form_open_multipart(uri_string(), array('class' => 'form-horizontal', 'role' => 'form')); ?>

                    <div class="form-group">
                        <label for="user" class="col-lg-2 col-sm-2 control-label">User</label>
                        <div class="col-lg-10">
                            <?php 
								$option = array(''=>'select User');
								if(!empty($customers)){
									foreach($customers as $row_cust){
										$option[$row_cust->id] = $row_cust->first_name." ".$row_cust->last_name;
									}
								}
								echo form_dropdown('user',$option,isset($_POST['user'])?$_POST['user']:$car->user_id,'class="form-control" id="user"');
							?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="car_no" class="col-lg-2 col-sm-2 control-label">Car No</label>
                        <div class="col-lg-10">
                            <input type="text" name="car_no" id="car_no" class="form-control" value="<?php echo set_value('car_no',$car->car_no); ?>" />
                        </div>
                    </div>
					
					<div class="form-group">
                        <label for="car_model" class="col-lg-2 col-sm-2 control-label">Car Model</label>
                        <div class="col-lg-10">
                            <input type="text" name="car_model" id="car_model" class="form-control" value="<?php echo set_value('car_model',$car->car_model); ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="car_layout" class="col-lg-2 col-sm-2 control-label">Car Layout</label>
                        <div class="col-lg-10">
                            <input type="text" name="car_layout" id="car_layout" class="form-control" value="<?php echo set_value('car_layout',$car->car_layout); ?>" />
                        </div>
                    </div>
					
					<div class="form-group">
                        <label for="car_image" class="col-lg-2 col-sm-2 control-label">Car Image</label>
                        <div class="col-lg-10">
                            <input type="file" name="car_image" id="car_image" />
                        </div>
                    </div>
					
					<div class="form-group">
                        <label for="ac_availibility" class="col-lg-2 col-sm-2 control-label">Ac Availability</label>
                        <div class="col-lg-10">
                            <input type="radio" name="ac_availibility" id="ac_availibility" value="y" <?php if($car->ac_availability == 'y'){?> checked<?php } ?>/> Yes&nbsp;
                            <input type="radio" name="ac_availibility" id="ac_availibility" value="n" <?php if($car->ac_availability == 'n'){?> checked<?php } ?>/> No
                        </div>
                    </div>
					
					<div class="form-group">
                        <label for="music_system" class="col-lg-2 col-sm-2 control-label">Music System</label>
                        <div class="col-lg-10">
                            <input type="radio" name="music_system" id="music_system" value="y" <?php if($car->music_system == 'y'){?> checked<?php } ?>/> Yes&nbsp;
                            <input type="radio" name="music_system" id="music_system" value="n" <?php if($car->music_system == 'n'){?> checked<?php } ?>/> No
                        </div>
                    </div>
					
					<div class="form-group">
                        <label for="air_bag" class="col-lg-2 col-sm-2 control-label">Air Bag</label>
                        <div class="col-lg-10">
                            <input type="radio" name="air_bag" id="air_bag" value="y" <?php if($car->air_bag == 'y'){?> checked<?php } ?>/> Yes&nbsp;
                            <input type="radio" name="air_bag" id="air_bag" value="n" <?php if($car->air_bag == 'n'){?> checked<?php } ?>/> No
                        </div>
                    </div>
					
					<div class="form-group">
                        <label for="seat_belt" class="col-lg-2 col-sm-2 control-label">Seat Belt</label>
                        <div class="col-lg-10">
                            <input type="radio" name="seat_belt" id="seat_belt" value="y" <?php if($car->seat_belt == 'y'){?> checked<?php } ?>/> Yes&nbsp;
                            <input type="radio" name="seat_belt" id="seat_belt" value="n" <?php if($car->seat_belt == 'n'){?> checked<?php } ?>/> No
                        </div>
                    </div>
					<input type="hidden" name="e_car_image" value="<?php echo $car->car_image; ?>" />
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button type="submit" name="submit" class="btn btn-primary">Edit Car</button>
                        </div>
                    </div>
                

                    <?php echo form_close(); ?>
                </div>
            </section>
        </div></div>
</section>