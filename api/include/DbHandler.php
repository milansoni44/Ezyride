<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Milan Soni
 */
class DbHandler {

    /** @var mysqli */
    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . './DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /* ------------- `users` table method ------------------ */

    /**
     * create user
     * @param $fname
     * @param $lname
     * @param $email
     * @param $contact
     * @param $dob
     * @param $gender
     * @param $created_at
     * @return array|int
     */
    public function createUser($fname, $lname, $email, $contact, $dob, $gender, $created_at) {
        require_once 'PassHash.php';
        $response = array();

        // First check if user already existed in db
        if (!$this->isUserExists($email)) {
            // Generating password hash
//            $password_hash = PassHash::hash($password);

            // Generating API key
            $api_key = $this->generateApiKey();
            $otp = rand(100000, 999999);

            // insert query
            $stmt = $this->conn->prepare("INSERT INTO customers(first_name, last_name, email, contact, dob, gender,created_at,otp,api_key, status) values(?,?,?,?,?,?,?,?,?,0)");

            $stmt->bind_param("sssssssss", $fname, $lname, $email, $contact, $dob, $gender,$created_at, $otp,$api_key);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // send sms
                $this->sendSms($contact, $otp);
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }

        return $response;
    }

    /**
     * @param $user_id
     * @param $otp
     * @return bool
     */
    public function activateUser($user_id,$otp) {
        //$stmt = $this->conn->prepare("SELECT u.id, u.name, u.email, u.mobile, u.apikey, u.status, u.created_at FROM users u, sms_codes WHERE sms_codes.code = ? AND sms_codes.user_id = u.id");
        //$stmt->bind_param("s", $otp);
        $sql = "SELECT otp from customers WHERE id = '".$user_id."'";
        $result = mysqli_query($this->conn,$sql);
        $row = mysqli_fetch_row($result);
        if($row[0] === $otp){
            // activate the user
            $this->activateUserStatus($user_id);
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function activateUserStatus($user_id){
        $sql = "UPDATE customers set status = 1 WHERE id = '".$user_id."'";
        $result = mysqli_query($this->conn,$sql);

        if($result){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param null $phone
     * @return bool
     */
    public function resendOTP($phone = NULL,$user_id){
        $otp = rand(100000, 999999);
        // send sms
        if($this->sendSms($phone, $otp)){
            if($this->updateOtp($otp,$user_id,$phone)) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function updateOtp($otp,$user_id,$phone){
        $sql = "UPDATE customers set otp = '".$otp."', contact='".$phone."' where id = '".$user_id."'";
        $result = mysqli_query($this->conn,$sql);
        if($result){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT password FROM customers WHERE email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->bind_result($password_hash);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password

            $stmt->fetch();

            $stmt->close();

            if (PassHash::check_password($password_hash, $password)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
    }

    public function isPhoneExists($phone = NULL){
        $sql = "SELECT contact from customers where contact = '".$phone."'";
        $result = mysqli_query($this->conn,$sql);
        $row = mysqli_num_rows($result);
        if($row > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    public function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT id from customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function checkStatus($email = NULL){
        $sql = "SELECT status from customers where email = '".$email."'";
        $result = mysqli_query($this->conn,$sql);
        $row = mysqli_num_rows($result);
        if($row > 0){
            return $row1 = mysqli_fetch_assoc($result);
        }
    }

    public function updateStatus($user_id){
        $sql = "Update customers SET contact_verify = '1',status = '1' where id = '".$user_id."'";
        $result= mysqli_query($this->conn,$sql);
        if($result){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Fetching user by email
     * @param $email
     * @return $user
     */
    public function getUserByEmail($email) {
        $sql = "SELECT first_name,last_name,email,contact,api_key,status,created_at FROM customers WHERE email = '".$email."'";
        $result = mysqli_query($this->conn,$sql);
        if($result){
            return $row = mysqli_fetch_assoc($result);
        }else{
            return NULL;
        }
    }

    public function getUserByID($user_id){
        $sql = "SELECT first_name,last_name,email,contact,api_key,status,dob,gender FROM customers WHERE id = '".$user_id."'";
        $result = mysqli_query($this->conn,$sql);
        if($result){
            return $row = mysqli_fetch_assoc($result);
        }else{
            return NULL;
        }
    }

    /**
     * Fetching user api key
     * @param $user_id
     * @return null
     */
    public function getApiKeyById($user_id) {
        /*$stmt = $this->conn->prepare("SELECT api_key FROM customers WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $api_key = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }*/
        $sql = "SELECT api_key FROM customers WHERE id = '".$user_id."'";
        $result = mysqli_query($this->conn,$sql);
        if($result){
            return $api_key = mysqli_fetch_assoc($result);
        }else{
            return NULL;
        }
    }

    /**
     * Fetching user id by api key
     * @param $api_key
     * @return null
     */
    public function getUserId($api_key) {
        /*$stmt = $this->conn->prepare("SELECT id FROM customers WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }*/
        $sql = "SELECT id FROM customers WHERE api_key = '".$api_key."'";
        $result = mysqli_query($this->conn,$sql);
        if($result){
            return $user_id = mysqli_fetch_assoc($result);
        }else{
            return NULL;
        }
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT id from customers WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

    /**
     * insert car details along with user_id
     * @param $user_id
     * @param $car_no
     * @param $car_model
     * @param $car_layout
     * @param $file_name
     * @param $ac_availability
     * @param $music_system
     * @param $air_bag
     * @param $seat_belt
     * @param $created_at
     * @return bool
     */
    public function create_car($user_id, $car_no, $car_model, $car_layout, $file_name, $ac_availability, $music_system, $air_bag, $seat_belt, $created_at) {
        $stmt = $this->conn->prepare("INSERT INTO car_details(user_id,car_no, car_model, car_layout, car_image, ac_availability,music_system,air_bag, seat_belt,creation_time) values(?,?,?,?,?,?,?,?,?,?)");

        $stmt->bind_param("ssssssssss", $user_id, $car_no, $car_model, $car_layout, $file_name, $ac_availability, $music_system, $air_bag, $seat_belt, $created_at);

        $result = $stmt->execute();

        $stmt->close();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * fetch all cars of particular user
     * @param $car_id
     * @param $user_id
     * @return array|null
     */
    public function getCar($car_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT car_details.*,customers.first_name,customers.last_name FROM car_details LEFT JOIN customers ON customers.id = car_details.user_id WHERE car_details.id = ? AND car_details.user_id = ?");
        $stmt->bind_param("ii", $car_id, $user_id);
        if ($stmt->execute()) {
            $car = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $car;
        } else {
            return NULL;
        }
    }

    /**
     * Fetch all users cars
     * @param $user_id
     * @return bool|mysqli_result
     */
    public function getAllUserCars($user_id) {
        $sql = "SELECT car_details.* FROM car_details, customers WHERE car_details.user_id = customers.id AND customers.id = $user_id";
        $result = mysqli_query($this->conn,$sql);
        if($result){
            return $result;
        }else{
            return NULL;
        }
        /*$stmt = $this->conn->prepare("SELECT car_details.* FROM car_details, customers WHERE car_details.user_id = customers.id AND customers.id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cars = $stmt->get_result();
        $stmt->close();
        return $cars;*/
    }

    /**
     * update car by user id and car id
     * @param $user_id
     * @param $car_id
     * @param $car_no
     * @param $car_model
     * @param $car_layout
     * @param $car_url
     * @param $ac_availability
     * @param $music_system
     * @param $air_bag
     * @param $seat_belt
     * @param $updated_at
     * @return bool
     */
    public function updateCar($user_id, $car_id,$car_no, $car_model, $car_layout, $car_url, $ac_availability, $music_system, $air_bag, $seat_belt, $updated_at) {
        $stmt = $this->conn->prepare("UPDATE car_details SET user_id = ?, car_no = ?, car_model = ?, car_layout = ?, car_image = ?, ac_availability = ?, music_system = ?, air_bag = ?, seat_belt = ?, updation_time = ? WHERE id = ?");

        $stmt->bind_param("sssssssssss", $user_id, $car_no, $car_model, $car_layout, $car_url, $ac_availability, $music_system, $air_bag, $seat_belt, $updated_at, $car_id);

        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * delete car by user id and car id
     * @param $user_id
     * @param $car_id
     * @return bool
     */
    public function deleteCar($user_id, $car_id) {
        $stmt = $this->conn->prepare("DELETE t FROM car_details t, customers ut WHERE t.id = ? AND ut.id = t.user_id AND ut.id = ?");
        $stmt->bind_param("ii", $car_id, $user_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function create_rides($user_id, $car_id, $from_lat,$to_lat,$from_long,$to_long,$from_main_address,$from_sub_address,$to_main_address,$to_sub_address,$ride_date, $ride_time, $price_per_seat, $seat_availability, $only_ladies) {
        $creation_time = date("Y-m-d h:i:sa");
        if ($stmt = $this->conn->prepare('INSERT INTO rides(user_id, car_id, from_lat,to_lat,from_long,to_long,from_main_address,from_sub_address,to_main_address,to_sub_address, ride_date, ride_time,  price_per_seat, seat_availability, only_ladies, creation_time) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')) {
            $stmt->bind_param('ssssssssssssssss', $user_id, $car_id, $from_lat,$to_lat,$from_long,$to_long,$from_main_address,$from_sub_address,$to_main_address,$to_sub_address,$ride_date, $ride_time, $price_per_seat, $seat_availability, $only_ladies, $creation_time);

            $result = $stmt->execute();

            $stmt->close();

            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            printf("Errormessage: %s\n", $this->conn->error);
        }
    }

    /**
     * 
     * @param type $rides_id
     * @param type $user_id
     * @return type
     */
    public function getRides($rides_id, $user_id) {
        if ($stmt = $this->conn->prepare('SELECT r.* FROM rides r WHERE user_id = ? AND id = ?')) {
            $stmt->bind_param('ss', $user_id, $rides_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } else {
            printf("Errormessage: %s\n", $this->conn->error);
        }
    }

    /**
     * 
     * @param type $user_id
     * @param type $rides_id
     * @return type
     */
    public function deleteRides($user_id, $rides_id) {
        if ($stmt = $this->conn->prepare('DELETE r FROM rides r WHERE user_id = ? AND id = ?')) {
            $stmt->bind_param('ss', $user_id, $rides_id);
            $stmt->execute();
            $rides_affected_rows = $stmt->affected_rows;
            $stmt->close();
            return $rides_affected_rows > 0;
        } else {
            printf("Errormessage: %s\n", $this->conn->error);
        }
    }

    /**
     * 
     * @param type $rides_id
     * @param type $user_id
     * @param type $car_id
     * @param type $ride_date
     * @param type $ride_time
     * @param type $price_per_seat
     * @param type $seat_availability
     * @param type $only_ladies
     * @return type
     */
    public function updateRides($rides_id, $user_id, $car_id, $ride_date, $ride_time, $price_per_seat, $seat_availability, $only_ladies) {
        $updation_time = date("Y-m-d h:i:sa");
        if ($stmt = $this->conn->prepare("UPDATE rides r set r.car_id = ?, r.ride_date = ?, r.ride_time = ?, r.price_per_seat = ?, r.seat_availability = ?, r.only_ladies = ?, r.updation_time = ? WHERE r.id = ? AND r.user_id = ?")) {
            $stmt->bind_param("sssssssss", $car_id, $ride_date, $ride_time, $price_per_seat, $seat_availability, $only_ladies, $updation_time, $rides_id, $user_id);
            $stmt->execute();
            $num_affected_rows = $stmt->affected_rows;
            $stmt->close();
            return $num_affected_rows > 0;
        } else {
            printf("Errormessage: %s\n", $this->conn->error);
        }
    }

    /**
     * @param $mobile
     * @param $otp
     * @return bool
     */
    function sendSms($mobile, $otp) {

        $otp_prefix = ':';

        //Your message to send, Add URL encoding here.
        $message = urlencode("Hello! Welcome to Ezyride. Your OPT is $otp_prefix $otp");

        $response_type = 'json';

        //Define route
        $route = "4";

        //Prepare you post parameters
        $postData = array(
            'authkey' => MSG91_AUTH_KEY,
            'mobiles' => $mobile,
            'message' => $message,
            'sender' => MSG91_SENDER_ID,
            'route' => $route,
            'response' => $response_type
        );

//API URL
        $url = "https://control.msg91.com/sendhttp.php";

// init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
            //,CURLOPT_FOLLOWLOCATION => true
        ));


        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);

        //Print error if any
        if (curl_errno($ch)) {
            echo 'error:' . curl_error($ch);
            return false;
        }

        curl_close($ch);
        return true;

    }

}

?>