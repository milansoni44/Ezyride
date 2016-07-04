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
 * User Registration
 * url - /register
 * method - POST
 * params - name, email, password
 */
$app->post('/register', function () use ($app) {
    // check for required params
    verifyRequiredParams(array('first_name', 'last_name', 'email', 'contact'));

    $response = array();

    // reading post params
    $fname = $app->request->post('first_name');
    $lname = $app->request->post('last_name');
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

    $res = $db->createUser($fname, $lname, $email, $contact, $dob, $gender, $created_at);

    if ($res == USER_CREATED_SUCCESSFULLY) {
        $user = $db->getUserByEmail($email);
        $response["error"] = false;
        $response["status"] = true;
        $response['apiKey'] = $user['api_key'];
        $response["message"] = "You are successfully registered, please verify your mobile number.";
        echoRespnse(200, $response);
    } else if ($res == USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
        echoRespnse(200, $response);
    } else if ($res == USER_ALREADY_EXISTED) {
        if ($db->checkStatus($email) == 0) {
            $response["error"] = false;
            $response["status"] = false;
            $response["message"] = "Sorry, this email already existed, please verify your mobile number";
            echoRespnse(200, $response);
        } else {
            $response["error"] = true;
            $response["message"] = "Sorry, this email already existed";
            echoRespnse(200, $response);
        }
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
        $response['lname'] = $user['last_name'];
        $response['email'] = $user['email'];
        $response['mobile'] = $user['contact'];
        $response['dob'] = date("d-m-Y", strtotime($user['dob']));
        $response['gender'] = $user['gender'];
        //$response['apiKey'] = $user['api_key'];
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
 * User Login
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
            $response['lname'] = $user['last_name'];
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

$app->get('/rides','authenticate',function() use($app){

});

/**
 * Add Car
 * url-/addcar
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
        $response['user'] = $result['first_name'] . " " . $result['last_name'];
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

    // fetching all user tasks
    $result = $db->getAllUserCars($user_id);

    $response["error"] = false;
    $cars = array();
//    $response["cars"] = array();
    $response['cars'] = array(array('id' => 0, 'car_model' => 'select'));
    // looping through result and preparing tasks array
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
        $cars1 = array_merge($cars,$response['cars']);
        $response['cars'] = $cars1;

        echoRespnse(200, $response);
    } else {
        $response1["error"] = true;
        $response1['message'] = "No cars found.";
        echoRespnse(200, $response1);
    }
});

$app->get('/car_details','authenticate',function() use($app){
    global $user_id;

    $response = array();
    $db = new DbHandler();

    // fetching all user tasks
    $result = $db->getAllUserCars($user_id);

    $response["error"] = false;
//    $cars = array();
    $response["cars"] = array();
    // looping through result and preparing tasks array
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

//echo sizeof($response["cars"]);exit;
    if (sizeof($response["cars"]) > 0) {
        $response['error'] = false;
        echoRespnse(200, $response);
    } else {
        $response1["error"] = true;
        $response1['message'] = "No cars found.";
        echoRespnse(200, $response1);
    }
});

/*

$tmp = array();
$tmp["id"] = $car["id"];
$tmp["car_no"] = $car["car_no"];
$tmp["car_model"] = $car["car_model"];
$tmp["car_layout"] = $car["car_layout"];
$tmp["car_image"] = $base_url . "/" . $car['car_image'];
$tmp["ac_availability"] = $car["ac_availability"];
$tmp["music_system"] = $car["music_system"];
$tmp["air_bag"] = $car["air_bag"];
$tmp["seat_belt"] = $car["seat_belt"];
$response["cars"][] = $tmp;*/
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

    // updating task
    $result = $db->updateCar($user_id, $car_id,$car_no, $car_model, $car_layout, $car_url, $ac_availability, $music_system, $air_bag, $seat_belt, $updated_at);

    if ($result) {
        // task updated successfully
        $response["error"] = false;
        $response["message"] = "Car updated successfully";
        echoRespnse(200, $response);
    } else {
        // task failed to update
        $response["error"] = true;
        $response["message"] = "Car failed to update. Please try again!";
        echoRespnse(200, $response);
    }
});

$app->delete('/car/:id','authenticate', function($car_id) use($app){
    global $user_id;

    $db = new DbHandler();
    $response = array();

    // delete car
    $result = $db->deleteCar($user_id,$car_id);
    if($result > 0){
        // car deleted successfully
        $response["error"] = false;
        $response["message"] = "Car Deleted successfully";
        echoRespnse(200, $response);
    }else{
        $response["error"] = true;
        $response["message"] = "Car failed to delete";
        echoRespnse(200, $response);
    }
});

/**
 * Updating existing task
 * method PUT
 * params task, status
 * url - /tasks/:id
 */
$app->put('/tasks/:id', 'authenticate', function ($task_id) use ($app) {
    // check for required params
    verifyRequiredParams(array('task', 'status'));

    global $user_id;
    $task = $app->request->put('task');
    $status = $app->request->put('status');

    $db = new DbHandler();
    $response = array();

    // updating task
    $result = $db->updateTask($user_id, $task_id, $task, $status);
    if ($result) {
        // task updated successfully
        $response["error"] = false;
        $response["message"] = "Task updated successfully";
        echoRespnse(200, $response);
    } else {
        // task failed to update
        $response["error"] = true;
        $response["message"] = "Task failed to update. Please try again!";
        echoRespnse(200, $response);
    }
});

/**
 * Deleting task. Users can delete only their tasks
 * method DELETE
 * url /tasks
 */
$app->delete('/tasks/:id', 'authenticate', function ($task_id) use ($app) {
    global $user_id;

    $db = new DbHandler();
    $response = array();
    $result = $db->deleteTask($user_id, $task_id);
    if ($result) {
        // task deleted successfully
        $response["error"] = false;
        $response["message"] = "Task deleted succesfully";
        echoRespnse(200, $response);
    } else {
        // task failed to delete
        $response["error"] = true;
        $response["message"] = "Task failed to delete. Please try again!";
        echoRespnse(200, $response);
    }
});

$app->post('/upload_car','authenticate',function() use($app){
    global $user_id;
    $base_url = $app->request->getUrl() . "/ezyride/assets/uploads/";
    $response = array();
    $file_name = '';
    if (isset($_FILES['car_image'])) {
        if($file_name = pan_upload($_FILES['car_image'])){
            $response['error'] = false;
            $response['url'] = $base_url.$file_name;
        }else{
            $response['error'] = true;
            $response['message'] = "Failed to upload car image";
        }
    }else{
        $response['error'] = true;
        $response['message'] = "Failed to upload car image";
    }
    echoRespnse(200,$response);
    $app->stop();


});

$app->run();
?>