<?php

require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;


    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(200, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email)
{
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(200, $response);
        $app->stop();
    }
}

/**
 * @param array $file
 * @return mixed
 * @throws \Slim\Exception\Stop
 */
function pan_upload($file = array())
{
    $app = \Slim\Slim::getInstance();
    $dir = dirname(dirname(dirname(__FILE__))) . "/assets/uploads/";

    $errors = array();
    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_type = $file['type'];
    $explode = explode('.', $file_name);
    $file_ext = strtolower(end($explode));

//    $expensions = array("jpeg", "jpg", "png");
    $response = array();

//    if (in_array($file_ext, $expensions) === false) {
//        $response["error"] = true;
//        $response["message"] = "extension not allowed, please choose a JPEG or PNG file.";
//        echoRespnse(200, $response);
//        $app->stop();
//    }

    if ($file_size > 5097152) {
        $response["error"] = true;
        $response["message"] = "File size must be excately 2 MB";
        echoRespnse(200, $response);
        $app->stop();
    }

    if (empty($errors) == true) {
        move_uploaded_file($file_tmp, $dir . $file_name);
        return $file_name;
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param array $response Json response
 */
function echoRespnse($status_code, $response)
{
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route)
{
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $db = new DbHandler();

        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(200, $response);
            $app->stop();
        } else {
            global $user_id;
            // get user primary key id
            $user = $db->getUserId($api_key);
            if ($user != NULL)
                $user_id = $user["id"];
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoRespnse(200, $response);
        $app->stop();
    }
}

/**
 * @param $val1
 * @param $val2
 * @return int
 */
function compareDeepValue($val1, $val2)
{
    return strcmp($val1['id'], $val2['id']);
}

/**
 * User Registration
 * url - /register
 * method - POST
 * params - name, email, password
 */
$app->post('/register', function () use ($app) {
    // check for required params
    verifyRequiredParams(array('first_name', 'email', 'contact'));

    $response = array();

    // reading post params
    $fname = $app->request->post('first_name');
//    $lname = $app->request->post('last_name');
    $email = $app->request->post('email');
//    $password = $app->request->post('password');
    $contact = $app->request->post('contact');
    $date = $app->request->post('dob');
    $dob = date('Y-m-d', strtotime($date));
    $gender = $app->request->post('gender');
    $created_at = date('Y-m-d H:m:s');
    // validating email address
    //validateEmail($email);

    $db = new DbHandler();

    /*if($db->isPhoneExists($contact)){
        $response["error"] = true;
        $response["message"] = "Sorry, this phone already existed";
        echoRespnse(200, $response);
        $app->stop();
    }*/

    if ($db->isUserExists($email)) {
        $response["error"] = true;
        $response["message"] = "Sorry, this email already existed";
        echoRespnse(200, $response);
        $app->stop();
    }

    $res = $db->createUser($fname, $email, $contact, $dob, $gender, $created_at);

    if ($res == USER_CREATED_SUCCESSFULLY) {
        $user = $db->getUserByEmail($email);
        $response["error"] = false;
        $response["status"] = true;
        $response['apiKey'] = $user['api_key'];
        $response['seller_id'] = $user['seller_id'];
        $response["message"] = "You are successfully registered, please verify your mobile number.";
        echoRespnse(200, $response);
    } else if ($res == USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
        echoRespnse(200, $response);
    } else if ($res == USER_ALREADY_EXISTED) {
        if ($user = $db->checkStatus($email) == 0) {
            $response["error"] = false;
            $response["status"] = false;
            $response['seller_id'] = $user['seller_id'];
            $response["message"] = "Sorry, this email already existed, please verify your mobile number";
            echoRespnse(200, $response);
        } else {
            $response["error"] = true;
            $response["message"] = "Sorry, this email already existed";
            echoRespnse(200, $response);
        }
    }
});

$app->put('/user', 'authenticate', function () use ($app) {
    global $user_id;
    $response = array();
    // check for required params
    verifyRequiredParams(array('first_name'));

    $fname = $app->request->put('first_name');
    $contact = $app->request->put('contact');
    $date = $app->request->put('dob');
    $dob = date('Y-m-d', strtotime($date));
    $pic = $app->request->put('profile_pic');
    $gender = $app->request->put('gender');
    $fb_stat = $app->request->put('fb_stat');
    $corp_mail = $app->request->put('corp_email_verify');
    $pan = $app->request->put('pan_image');
    $pan_verify = $app->request->put('pan_verify');

    // changes on 25/07/2016
    $address = $app->request->put('address');
    $city_id = $app->request->put('city_id');
    $city_name = $app->request->put('city_name');
    $state_id = $app->request->put('state_id');
    $state_name = $app->request->put('state_name');
    $zip_code = $app->request->put('zip_code');
    $ifsc_code = $app->request->put('ifsc_code');
    $acc_no = $app->request->put('acc_no');
    $payout_mode = $app->request->put('payout_mode');
    // changes end

    $updated_at = date('Y-m-d H:m:s');

    $db = new DbHandler();

    /*if($db->isPhoneExists($contact,$user_id) == TRUE){
        // no code to run
    }else if($db->isPhoneExists($contact,$user_id) == FALSE){
        // no code to run
    }else{
        $response["error"] = true;
        $response["message"] = "Sorry, this phone already existed";
        echoRespnse(200, $response);
        $app->stop();
    }*/

    $res = $db->updateUser($user_id, $fname, $contact, $dob, $pic, $gender, $fb_stat, $corp_mail, $pan, $pan_verify, $address, $city_id, $city_name, $state_id, $state_name, $zip_code, $ifsc_code, $acc_no, $payout_mode, $updated_at);

    if ($res) {
        $response['error'] = false;
        $response['message'] = "User updated successfully";
        echoRespnse(200, $response);
    } else {
        $response['error'] = true;
        $response['message'] = "User failed to update";
        echoRespnse(200, $response);
    }
});

/**
 * verify otp
 * url - /very_otp
 * method - POST
 * params - user_id, otp
 */
$app->post('/verify_otp', 'authenticate', function () use ($app) {
    global $user_id;
    $response = array();
    //check for required params
    verifyRequiredParams(array('otp'));
    $otp = $app->request->post('otp');

    $db = new DbHandler();
    if ($db->activateUser($user_id, $otp)) {
        $db->updateStatus($user_id);
        $user = $db->getUserByID($user_id);
        $response["error"] = false;
        $response['fname'] = $user['first_name'];
        $response['email'] = $user['email'];
        $response['mobile'] = $user['contact'];
        $response['dob'] = date("d-m-Y", strtotime($user['dob']));
        $response['gender'] = $user['gender'];
        $response["message"] = "User successfully activated";
    } else {
        $response["error"] = true;
        $response["message"] = "Oops! something went wrong.";
    }
    echoRespnse(200, $response);
});

$app->post('/resend_otp', 'authenticate', function () use ($app) {
    global $user_id;
    //check for required params
    verifyRequiredParams(array('phone'));
    $response = array();
    $phone = $app->request->post('phone');

    $db = new DbHandler();
    if ($db->resendOTP($phone, $user_id)) {
        $response["error"] = false;
        $response["message"] = "OTP send successfully";
    } else {
        $response["error"] = true;
        $response["message"] = "Error while sending otp";
    }
    echoRespnse(200, $response);
});

/**
 * User Login no need to use
 * url - /login
 * method - POST
 * params - email, password
 */
$app->post('/login', function () use ($app) {
    // check for required params
    verifyRequiredParams(array('email', 'password'));

    // reading post params
    $email = $app->request()->post('email');
    $password = $app->request()->post('password');
    $response = array();

    $db = new DbHandler();
    // check for correct email and password
    if ($db->checkLogin($email, $password)) {
        // get the user by email
        $user = $db->getUserByEmail($email);
//                print_r($user);exit;
        if ($user != NULL) {
            $response["error"] = false;
            $response['fname'] = $user['first_name'];
//            $response['lname'] = $user['last_name'];
            $response['email'] = $user['email'];
            $response['mobile'] = $user['contact'];
            $response['apiKey'] = $user['api_key'];
            $response['createdAt'] = $user['created_at'];
        } else {
            // unknown error occurred
            $response['error'] = true;
            $response['message'] = "An error occurred. Please try again";
        }
    } else {
        // user credentials are wrong
        $response['error'] = true;
        $response['message'] = 'Login failed. Incorrect credentials';
    }

    echoRespnse(200, $response);
});

$app->post('/rides', 'authenticate', function () use ($app) {
    global $user_id;

    verifyRequiredParams(array('car_id', 'from_lat', 'to_lat', 'from_long', 'to_long', 'from_main_address', 'from_sub_address', 'to_main_address', 'to_sub_address', 'ride_date', 'ride_time', 'price_per_seat', 'seat_availability', 'only_ladies'));

    // reading post params
    $car_id = $app->request->post('car_id');
    $from_lat = $app->request->post('from_lat');
    $to_lat = $app->request->post('to_lat');
    $from_long = $app->request->post('from_long');
    $to_long = $app->request->post('to_long');
    $from_main_address = $app->request->post('from_main_address');
    $from_sub_address = $app->request->post('from_sub_address');
    $to_main_address = $app->request->post('to_main_address');
    $to_sub_address = $app->request->post('to_sub_address');
    $ride_date = $app->request->post('ride_date');
    $ride_f_date = date('Y-m-d', strtotime($ride_date));
    $ride_time = $app->request->post('ride_time');
    $price_per_seat = $app->request->post('price_per_seat');
    $seat_availability = $app->request->post('seat_availability');
    $only_ladies = $app->request->post('only_ladies');


    $response = array();

    $db = new DbHandler();

    $res = $db->create_rides($user_id, $car_id, $from_lat, $to_lat, $from_long, $to_long, $from_main_address, $from_sub_address, $to_main_address, $to_sub_address, $ride_f_date, $ride_time, $price_per_seat, $seat_availability, $only_ladies);

    if ($res) {
        $response['error'] = false;
        $response['message'] = 'Rides create successful.';
        echoRespnse(200, $response);
    } else {
        $response['error'] = true;
        $response['message'] = 'Erro while create rides.';
        echoRespnse(200, $response);
    }
});

$app->post('/rides', 'authenticate', function () use ($app) {
    global $user_id;
    verifyRequiredParams(array('date'));

    $date = $app->request->post('date');
    $ride_date = date('Y-m-d', strtotime($date));

    $db = new DbHandler();
    $result = $db->getUsersRides($user_id, $ride_date);

});

$app->get('/rides/:id', 'authenticate', function ($rides_id) {
    global $user_id;
    $response = array();
    $db = new DbHandler();

    // fetch car
    $result = $db->getRides($rides_id, $user_id);

    $response["error"] = false;
    $response["rides"] = array();

    // looping through result and preparing tasks array
    while ($rides = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $rides["id"];
        $tmp["user_id"] = $rides["user_id"];
        $tmp["car_id"] = $rides["car_id"];
        $tmp["ride_date"] = $rides["ride_date"];
        $tmp["ride_time"] = $rides["ride_time"];
        $tmp["price_per_seat"] = $rides["price_per_seat"];
        $tmp["seat_availability"] = $rides["seat_availability"];
        $tmp["only_ladies"] = $rides["only_ladies"];
        $tmp["creation_time"] = $rides["creation_time"];
        $tmp["updation_time"] = $rides["updation_time"];

        array_push($response["rides"], $tmp);
    }

    if ($result->num_rows > 0) {
        echoRespnse(200, $response);
    } else {
        $responses['error'] = true;
        $responses['message'] = 'No Data Found';
        echoRespnse(200, $responses);
    }
});

$app->delete('/rides/:id', 'authenticate', function ($rides_id) {
    global $user_id;
    $response = array();

    $db = new DbHandler();

    $result = $db->deleteRides($user_id, $rides_id);
    if ($result) {
        $response['error'] = FALSE;
        $response['message'] = 'Ride Delete Successful.';
        echoRespnse(200, $response);
    } else {
        $response['error'] = TRUE;
        $response['message'] = 'Ride Not Deleted.';
        echoRespnse(200, $response);
    }
});

$app->put('/rides/:id', 'authenticate', function ($rides_id) use ($app) {

    global $user_id;
    $car_id = $app->request->put('car_id');
    $ride_date = $app->request->put('ride_date');
    $ride_time = $app->request->put('ride_time');
    $price_per_seat = $app->request->put('price_per_seat');
    $seat_availability = $app->request->put('seat_availability');
    $only_ladies = $app->request->put('only_ladies');


    $db = new DbHandler();
    $response = array();

    // updating task
    $result = $db->updateRides($rides_id, $user_id, $car_id, $ride_date, $ride_time, $price_per_seat, $seat_availability, $only_ladies);
    if ($result) {
        // task updated successfully
        $response["error"] = false;
        $response["message"] = "Rides updated successfully";
    } else {
        // task failed to update
        $response["error"] = true;
        $response["message"] = "Rides failed to update. Please try again!";
    }
    echoRespnse(200, $response);
});


/**
 * Add Car
 * url-/car
 * method - POST
 * params - car_no, car_model, car_layout, car_image, ac_availability, music_system, air_bag, seat_belt
 */
$app->post('/car', 'authenticate', function () use ($app) {
    global $user_id;

    verifyRequiredParams(array('car_no', 'car_model', 'car_layout'));

    // reading post params
    $car_no = $app->request->post('car_no');
    $car_model = $app->request->post('car_model');
    $car_layout = $app->request->post('car_layout');
    $ac_availability = $app->request->post('ac_availability');
    $music_system = $app->request->post('music_system');
    $air_bag = $app->request->post('air_bag');
    $seat_belt = $app->request->post('seat_belt');
    $car_url = $app->request->post('car_url');
    $created_at = date('Y-m-d H:m:s');

    /*$file_name = '';
    if (isset($_FILES['car_image'])) {
        $file_name = pan_upload($_FILES['car_image']);
    }*/
    $response = array();

    $db = new DbHandler();
    $res = $db->create_car($user_id, $car_no, $car_model, $car_layout, $car_url, $ac_availability, $music_system, $air_bag, $seat_belt, $created_at);

    if ($res) {
        $response["error"] = false;
        $response["message"] = "$car_no successfully created";
        echoRespnse(200, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "Error while inserting car";
        echoRespnse(200, $response);
    }
});

/**
 * get car details of particular car_id
 * url - /car/:id
 * method - GET
 */
$app->get('/car/:id', 'authenticate', function ($car_id) use ($app) {
    global $user_id;
    $req = $app->request;
    $base_url = $req->getUrl() . "/ezyride/assets/uploads";

    $response = array();
    $db = new DbHandler();

    // fetch car
    $result = $db->getCar($car_id, $user_id);

    if ($result) {
        $response['error'] = false;
        $response['id'] = $result['id'];
        $response['user_id'] = $result['user_id'];
        $response['car_no'] = $result['car_no'];
        $response['car_model'] = $result['car_model'];
        $response['car_layout'] = $result['car_layout'];
        $response['car_image'] = $base_url . "/" . $result['car_image'];
        $response['ac_availability'] = $result['ac_availability'];
        $response['music_system'] = $result['music_system'];
        $response['air_bag'] = $result['air_bag'];
        $response['seat_belt'] = $result['seat_belt'];
        $response['user'] = $result['first_name'];
        echoRespnse(200, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoRespnse(200, $response);
    }
});

/**
 * Listing all cars of particual user
 * method GET
 * url /car
 */
$app->get('/car', 'authenticate', function () use ($app) {
    global $user_id;
    $req = $app->request;
    $base_url = $req->getUrl() . "/ezyride/assets/uploads";

    $response = array();
    $db = new DbHandler();

    // fetching all user cars
    $result = $db->getAllUserCars($user_id);

    $response["error"] = false;
    $cars = array();
//    $response["cars"] = array();
    $response['cars'] = array(array('id' => 0, 'car_model' => 'select'));
    // looping through result and preparing cars array
    while ($car = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $car["id"];
        $tmp["car_model"] = $car["car_model"];
        $cars[] = $tmp;
    }
    if (sizeof($cars) > 0) {
        $cars = array_merge($response['cars'], $cars);
        $response['cars'] = $cars;
        $response['cars'] = array(array('id' => 156415642, 'car_model' => 'Add new Car'));
        $cars1 = array_merge($cars, $response['cars']);
        $response['cars'] = $cars1;

        echoRespnse(200, $response);
    } else {
        $response1["error"] = true;
        $response1['message'] = "No cars found.";
        echoRespnse(200, $response1);
    }
});

$app->get('/car_details', 'authenticate', function () use ($app) {
    global $user_id;

    $response = array();
    $db = new DbHandler();

    // fetching all user cars
    $result = $db->getAllUserCars($user_id);

    $response["error"] = false;
//    $cars = array();
    $response["cars"] = array();
    // looping through result and preparing car details array
    while ($car = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $car["id"];
        $tmp["car_no"] = $car["car_no"];
        $tmp["car_model"] = $car["car_model"];
        $tmp["car_layout"] = $car["car_layout"];
        $tmp["car_image"] = $car['car_image'];
        $tmp["ac_availability"] = $car["ac_availability"];
        $tmp["music_system"] = $car["music_system"];
        $tmp["air_bag"] = $car["air_bag"];
        $tmp["seat_belt"] = $car["seat_belt"];
        $response["cars"][] = $tmp;
    }

    if (sizeof($response["cars"]) > 0) {
        $response['error'] = false;
        echoRespnse(200, $response);
    } else {
        $response1["error"] = true;
        $response1['message'] = "No cars found.";
        echoRespnse(200, $response1);
    }
});

/**
 * Updating existing car
 * method PUT
 * params car_id
 * url - /car/:id
 */
$app->put('/car/:id', 'authenticate', function ($car_id) use ($app) {
    // check for required params
    global $user_id;

    verifyRequiredParams(array('car_no', 'car_model', 'car_layout'));

    // reading post params
    $car_no = $app->request->post('car_no');
    $car_model = $app->request->post('car_model');
    $car_layout = $app->request->post('car_layout');
    $ac_availability = $app->request->post('ac_availability');
    $music_system = $app->request->post('music_system');
    $air_bag = $app->request->post('air_bag');
    $seat_belt = $app->request->post('seat_belt');
    $car_url = $app->request->post('car_url');
    $updated_at = date('Y-m-d H:m:s');

    /*$file_name = '';
    if (isset($_FILES['car_image'])) {
        $file_name = pan_upload($_FILES['car_image']);
    }*/

    $db = new DbHandler();
    $response = array();

    // updating car
    $result = $db->updateCar($user_id, $car_id, $car_no, $car_model, $car_layout, $car_url, $ac_availability, $music_system, $air_bag, $seat_belt, $updated_at);

    if ($result) {
        // car updated successfully
        $response["error"] = false;
        $response["message"] = "Car updated successfully";
        echoRespnse(200, $response);
    } else {
        // car failed to update
        $response["error"] = true;
        $response["message"] = "Car failed to update. Please try again!";
        echoRespnse(200, $response);
    }
});

$app->delete('/car/:id', 'authenticate', function ($car_id) use ($app) {
    global $user_id;

    $db = new DbHandler();
    $response = array();

    // delete car
    $result = $db->deleteCar($user_id, $car_id);
    if ($result > 0) {
        // car deleted successfully
        $response["error"] = false;
        $response["message"] = "Car Deleted successfully";
        echoRespnse(200, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "Car failed to delete";
        echoRespnse(200, $response);
    }
});

$app->post('/upload_car', 'authenticate', function () use ($app) {
    global $user_id;
    $base_url = $app->request->getUrl() . "/ezyride/assets/uploads/";
    $response = array();
    $file_name = '';
    if (isset($_FILES['car_image'])) {
        if ($file_name = pan_upload($_FILES['car_image'])) {
            $response['error'] = false;
            $response['url'] = $base_url . $file_name;
        } else {
            $response['error'] = true;
            $response['message'] = "Failed to upload car image";
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Failed to upload car image";
    }
    echoRespnse(200, $response);
    $app->stop();
});

$app->post('/search_ride', 'authenticate', function () use ($app) {
    global $user_id;

    verifyRequiredParams(array('to_lat', 'to_long', 'from_lat', 'from_long'));
    $response = array();
    $to_lat = $app->request->post('to_lat');
    $to_long = $app->request->post('to_long');
    $from_lat = $app->request->post('from_lat');
    $from_long = $app->request->post('from_long');
    $date = $app->request->post('date');
    $ride_date = date('Y-m-d', strtotime($date));

    $db = new DbHandler();

    $result = $db->search_from_ride($from_lat, $from_long, $ride_date);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $from_result[] = $row;
        }
    } else {
        $responses['error'] = true;
        $responses['message'] = 'No rides found';
        echoRespnse(200, $responses);
        $app->stop();
    }

    $result1 = $db->search_to_ride($to_lat, $to_long, $ride_date);

    if ($result1) {
        while ($row1 = mysqli_fetch_assoc($result1)) {
            $to_result[] = $row1;
        }
    } else {
        $responses['error'] = true;
        $responses['message'] = 'No rides found';
        echoRespnse(200, $responses);
        $app->stop();
    }
    if (!empty($from_result) && !empty($to_result)) {
        $intersect = array_uintersect($from_result, $to_result, 'compareDeepValue');
        if (!empty($intersect)) {
            $response["error"] = false;
            $response["rides"] = array();
            foreach ($intersect as $search) {
                $rides = $db->getRidesByID($search['id']);
                $rides1[] = $rides->fetch_assoc();
            }
            foreach ($rides1 as $rw) {
                $tmp = array();
                $tmp["id"] = $rw["id"];
                $tmp["user_id"] = $rw["user_id"];
                $tmp['user_image'] = $rw['profile_image'];
                $tmp['username'] = $rw['first_name'];
                $tmp["car_id"] = $rw["car_id"];
                $tmp['car_no'] = $rw['car_no'];
                $tmp["from_lat"] = $rw["from_lat"];
                $tmp["from_long"] = $rw["from_long"];
                $tmp["to_lat"] = $rw["to_lat"];
                $tmp["to_long"] = $rw["to_long"];
                $tmp["from_main_address"] = $rw["from_main_address"];
                $tmp["from_sub_address"] = $rw["from_sub_address"];
                $tmp["to_main_address"] = $rw["to_main_address"];
                $tmp["to_sub_address"] = $rw["to_sub_address"];
                $tmp["ride_date"] = $rw["ride_date"];
                $tmp["ride_time"] = $rw["ride_time"];
                $tmp["price_per_seat"] = $rw["price_per_seat"];
                $tmp["seat_availability"] = $rw["seat_availability"];
                $tmp["only_ladies"] = $rw["only_ladies"];
                // changes on 18/07/2016
                $tmp['user_gender'] = $rw['gender'];
                $tmp['car_image'] = $rw['car_image'];
                $tmp['seat_belt'] = $rw['seat_belt'];
                $tmp['air_bag'] = $rw['air_bag'];
                $tmp['ac_availability'] = $rw['ac_availability'];
                $tmp['music_system'] = $rw['music_system'];
                $tmp['car_layout'] = $rw['car_layout'];
                $tmp['car_model'] = $rw['car_model'];
                $tmp['pan_verify'] = $rw['pan_verify'];
                $tmp['fb_verify'] = $rw['fb_verify'];
                $tmp['corp_email_verify'] = $rw['corp_email_verify'];
                $tmp['contact_verify'] = $rw['contact_verify'];
                $tmp['age'] = $rw['age'];
                $tmp['car_image'] = $rw['car_image'];
                $tmp["creation_time"] = $rw["creation_time"];
                $tmp["updation_time"] = $rw["updation_time"];

                array_push($response["rides"], $tmp);
            }
            if (sizeof($rides1) > 0) {
                echoRespnse(200, $response);
                $app->stop();
            } else {
                $responses['error'] = true;
                $responses['message'] = 'No rides found';
                echoRespnse(200, $responses);
                $app->stop();
            }
        } else {
            $response['error'] = true;
            $response['message'] = "No rides found";
            echoRespnse(200, $response);
            $app->stop();
        }
    } else {
        $response['error'] = true;
        $response['message'] = "No rides found";
        echoRespnse(200, $response);
        $app->stop();
    }
});

$app->post('/my_rides', 'authenticate', function () use ($app) {
    global $user_id;
    // check for required params
    verifyRequiredParams(array('date'));

    $date = $app->request->post('date');
    $ride_date = date('Y-m-d', strtotime($date));

    $db = new DbHandler();

    $result = $db->getAllRideByDate($user_id, $ride_date);

    $response["error"] = false;
    $response["rides"] = array();

    // looping through result and preparing tasks array
    while ($rides = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["ride_id"] = $rides["id"];
        $tmp["user_id"] = $rides["user_id"];
        $tmp['user_image'] = $rides['profile_image'];
        $tmp['username'] = $rides['first_name'];
        $tmp["car_id"] = $rides["car_id"];
        $tmp["from_lat"] = $rides["from_lat"];
        $tmp["from_long"] = $rides["from_long"];
        $tmp["to_lat"] = $rides["to_lat"];
        $tmp["to_long"] = $rides["to_long"];
        $tmp["from_main_address"] = $rides["from_main_address"];
        $tmp["from_sub_address"] = $rides["from_sub_address"];
        $tmp["to_main_address"] = $rides["to_main_address"];
        $tmp["to_sub_address"] = $rides["to_sub_address"];
        $tmp["ride_date"] = $rides["ride_date"];
        $tmp["ride_time"] = $rides["ride_time"];
        $tmp["price_per_seat"] = $rides["price_per_seat"];
        $tmp["seat_availability"] = $rides["seat_availability"];
        $tmp["only_ladies"] = $rides["only_ladies"];
        $tmp["creation_time"] = $rides["creation_time"];
        $tmp["updation_time"] = $rides["updation_time"];

        array_push($response["rides"], $tmp);
    }

    if ($result->num_rows > 0) {
        echoRespnse(200, $response);
    } else {
        $responses['error'] = true;
        $responses['message'] = 'No Rides Found';
        echoRespnse(200, $responses);
    }

});

$app->get('/get_states', function () use ($app) {
//    verifyRequiredParams(array('country'));
//    $countryID = $app->request->post('country');

    $db = new DbHandler();
    $result = $db->get_states("101");

    $response["error"] = false;
    $response["states"] = array(array("id" => 0, "state_name" => "select state", "country_id" => 0));

    // looping through result and preparing tasks array
    while ($states = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $states["id"];
        $tmp["state_name"] = $states["name"];
        $tmp['country_id'] = $states['country_id'];
        array_push($response["states"], $tmp);
    }

    if (sizeof($response["states"]) > 0) {
        $response['error'] = false;
        echoRespnse(200, $response);
    } else {
        $response1["error"] = true;
        $response1['message'] = "No states found.";
        echoRespnse(200, $response1);
    }
});

$app->post('/get_city', function () use ($app) {
    verifyRequiredParams(array('state'));
    $stateID = $app->request->post('state');

    $db = new DbHandler();
    $result = $db->get_city($stateID);

    $response["error"] = false;
    $response["cities"] = array(array("id" => 0, "city_name" => "select city", "state_id" => 0));

    // looping through result and preparing tasks array
    while ($city = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $city["id"];
        $tmp["city_name"] = $city["name"];
        $tmp['state_id'] = $city['state_id'];
        array_push($response["cities"], $tmp);
    }

    if (sizeof($response["cities"]) > 1) {
        $response['error'] = false;
        echoRespnse(200, $response);
    } else {
        $response1["error"] = true;
        $response1['message'] = "No city found.";
        echoRespnse(200, $response1);
    }
});

$app->get('/auth', function () use ($app) {
    // $access_key = $app->request->post('access_key');
    //  $secret_key = $app->request->post('secret_key');
    // header
    $headers = array(
        'Content-Type: application/json'
    );
    // Get cURL resource
    $ch = curl_init();
    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, 'https://splitpaysbox.citruspay.com/marketplace/auth/');

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('access_key' => 'OG8OF2SZPIAK0EOP5HGR',
        'secret_key' => 'fd8686928e68093612daafb834e252a8e21223c7')));
// Close request to clear up some resources
    $resp = curl_exec($ch);
    curl_close($ch);
    echo $resp;
});

$app->run();
?>