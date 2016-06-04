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
     * user registration
     * @param $fname
     * @param $lname
     * @param $email
     * @param $contact
     * @param $password
     * @param $pan
     * @param $dob
     * @param $gender
     * @param $corp_email
     * @param $created_at
     * @return array|int
     */
    public function createUser($fname, $lname, $email, $contact, $password, $pan, $dob, $gender, $corp_email, $created_at) {
        require_once 'PassHash.php';
        $response = array();

        // First check if user already existed in db
        if (!$this->isUserExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);

            // Generating API key
            $api_key = $this->generateApiKey();

            // insert query
            $stmt = $this->conn->prepare("INSERT INTO customers(first_name, last_name, email, contact, password,pan,dob, gender,corp_email,created_at,api_key, status) values(?,?,?,?,?,?,?,?,?,?,?,1)");

            $stmt->bind_param("sssssssssss", $fname, $lname, $email, $contact, $password_hash, $pan, $dob, $gender, $corp_email, $created_at, $api_key);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) {
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

    /**
     * Fetching user by email
     * @param $email
     * @return $user
     */
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT first_name, last_name,email, api_key, status, created_at FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user api key
     * @param $user_id
     * @return null
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT api_key FROM customers WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $api_key = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user id by api key
     * @param $api_key
     * @return null
     */
    public function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT id FROM customers WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
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
        $stmt = $this->conn->prepare("SELECT car_details.* FROM car_details, customers WHERE car_details.user_id = customers.id AND customers.id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cars = $stmt->get_result();
        $stmt->close();
        return $cars;
    }

    /**
     * update car by car id of a particular user
     * @param $user_id
     * @param $car_id
     * @param $car_no
     * @param $car_model
     * @param $car_layout
     * @param $file_name
     * @param $ac_availability
     * @param $music_system
     * @param $air_bag
     * @param $seat_belt
     * @param $updated_at
     * @return bool
     */
    public function updateCar($user_id, $car_id, $car_no, $car_model, $car_layout, $file_name, $ac_availability, $music_system, $air_bag, $seat_belt, $updated_at) {
        $stmt = $this->conn->prepare("UPDATE car_details SET user_id = ?, car_no = ?, car_model = ?, car_layout = ?, car_image = ?, ac_availability = ?, music_system = ?, air_bag = ?, seat_belt = ?, updation_time = ? WHERE id = ?");

        $stmt->bind_param("sssssssssss", $user_id, $car_no, $car_model, $car_layout, $file_name, $ac_availability, $music_system, $air_bag, $seat_belt, $updated_at, $car_id);

        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * Deleting a task
     * @param $user_id
     * @param $task_id
     * @return bool
     */
    public function deleteTask($user_id, $task_id) {
        $stmt = $this->conn->prepare("DELETE t FROM tasks t, user_tasks ut WHERE t.id = ? AND ut.task_id = t.id AND ut.user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * 
     * @param type $user_id
     * @param type $car_id
     * @param type $ride_date
     * @param type $ride_time
     * @param type $price_per_seat
     * @param type $seat_availability
     * @param type $only_ladies
     * @return boolean
     */
    public function create_rides($user_id, $car_id, $ride_date, $ride_time, $price_per_seat, $seat_availability, $only_ladies) {
        $creation_time = date("Y-m-d h:i:sa");
        if ($stmt = $this->conn->prepare('INSERT INTO rides(user_id, car_id, ride_date, ride_time,  price_per_seat, seat_availability, only_ladies, creation_time) values(?,?,?,?,?,?,?,?)')) {
            $stmt->bind_param('ssssssss', $user_id, $car_id, $ride_date, $ride_time, $price_per_seat, $seat_availability, $only_ladies, $creation_time);

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

}

?>