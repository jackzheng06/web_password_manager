<?php
    namespace WebPasswordManager;

    class User {
        public $db;

       /**
        * _construct
        * User class, used to manage users
        *
        * Parameters:
        *     (db) - A DatabaseConnection instance
        */
        public function __construct($db){
            $this->db = $db;
        }

       /**
        * create
        * Create a new user for the application,
        * return True if creation is successful, False if failed.
        *
        * Parameters:
        *     (username) - New user's username
        *     (password) - New user's password, should be in hashed format
        */
        public function create($username, $password){
            $result = False;
            // Check if user exists, if not, proceed
            if($this->get_by_username($username)){
                
            }
        }

       /**
        * get_by_username
        * Get a user by username, returns an array in the following format
        * key      | value
        * ---------------------------------------------
        * id       | user id (int)
        * username | username, again (string)
        * password | salted & hashed password (string)
        * salt     | the salt for password (string)
        * Returns False if username is not found.
        *
        * Parameters:
        *     (username) - Username you want to find
        */
        public function get_by_username($username){
            $result = NULL;
            // Prepare MySQL statement
            $stmt = $this->db->sqli->prepare("SELECT * FROM users WHERE username=?");
            $stmt->bind_param("s",$username);
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows <= 0){
                // User cannot be found
                $result = False;
            } else {
                // Bind result
                $stmt->bind_result($resultId,$resultUsername,$resultPassword,$resultSalt);
                $stmt->fetch();
                // Construct array
                $result = array(
                    "id"=>$resultId,
                    "username"=>$resultUsername,
                    "password"=>$resultPassword,
                    "salt"=>$resultSalt
                );
            }
            // Return result
            return $result;
        }

       /**
        * hash_password
        * Hash a password using salt, returns hashed string.
        *
        * Parameters:
        *     (password) - The password to be hashed
        *     (salt) - Salt for the password
        */
        public function hash_password($password, $salt){
            $hash = $password;
            // Hash the password 100,000 times using SHA-512, add salt to it each time
            // During testing, this process takes 0.2592511177063 sec on a Core i5 6400.
            for ($i = 0; $i < 100000; $i++){
                $hash = hash('sha512', $hash . $salt);
            }
            // Return the hash 
            return $hash;
        }
    }
?>