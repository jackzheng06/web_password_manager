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
        * simple_auth
        * Do a simple authentication to check if a user is loggedin.
        * For now, this is just an alias for check_token.
        *
        * Parameters:
        *     (username_hash) - Username in hashed format
        *     (token) - Token to be checked
        */
        public function simple_auth($username_hash, $token){
            $result = False;
            $userInfo = $this->check_token($username_hash, $token);
            if($userInfo){
                $result = $userInfo;
            }
            return $result;
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
            if(!$this->get_by_username($username)){
                // Hash the password using random salt
                $salt = $this->generate_salt();
                $password = $this->hash_password($password, $salt);
                // Insert information into the database
                $stmt = $this->db->sqli->prepare("INSERT INTO users
                                                  (username, password, salt) VALUES 
                                                  (?,?,?)");
                $stmt->bind_param("sss",$username,$password,$salt);
                if($stmt->execute()){
                    $result = True;
                }
            }
            return $result;
        }

       /**
        * check_token
        * Check if an authentication token is valid.
        * Returns detailed user information if valid, otherwise, return False.
        *
        * Parameters:
        *     (username_hash) - Username in hashed format, just to ensure the client knows
        *                       what it is doing. One more level of security is always
        *                       good.
        *     (token) - Token to be checked
        */
        public function check_token($username_hash, $token){
            $result = False;
            // Select corrsponding token from database
            $stmt = $this->db->sqli->prepare("SELECT * FROM tokens WHERE token_body=?");
            $stmt->bind_param("s",$token);
            $stmt->execute();
            $stmt->store_result();
            // If token is found
            if($stmt->num_rows > 0){
                $stmt->bind_result($tokenId, $tokenUid, $tokenBody, $tokenValidUntil);
                $stmt->fetch();
                // Check if token is still valid
                    if($tokenValidUntil > time()){
                        // Get user information by uid
                        $userInfo = $this->get_by_id($tokenUid);
                        if($userInfo){
                            // Check if username_hash matches md5(username)
                            if($username_hash == hash("md5", $userInfo["username"])){
                                $result = $userInfo;
                            }
                        }
                        
                    }
            }
            return $result;
        }

       /**
        * check_password
        * Check to see if a password is valid for the user,
        * return True if password is a match, False otherwise.
        *
        * Parameters:
        *     (username) - New user's username
        *     (password) - New user's password, should be in hashed format
        */
        public function check_password($username, $password){
            $result = false;
            // Run SELECT query from database
            $stmt = $this->db->sqli->prepare("SELECT * FROM users WHERE username=?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            // If user is found, proceed, otherwise, result is False
            if($stmt->num_rows > 0){
                // Fetch user information
                $stmt->bind_result($resultId, $resultUsername, $resultPassword, $resultSalt);
                $stmt->fetch();
                // Hash the password
                $password = $this->hash_password($password, $resultSalt); 
                // If password is a match, result is True, otherwise, result is False 
                if($password == $resultPassword){
                    $result = true;
                }
            }
            
            return $result;
        }

       /**
        * create_token
        * Create a new authentication token for the user,
        * return False if user is not found, otherwise, return the token string.
        *
        * Parameters:
        *     (username) - New user's username
        *     (password) - New user's password, should be in hashed format
        */
        public function create_token($username){
            $result = False;
            // Check if user exists
            $userInfo = $this->get_by_username($username);
            if($userInfo){
                // Generate a new token, to be honest, I will just use 10 salt for this. 
                $token = "";
                $validUntil = time() + 60 * 60 * 7; // 7 Hours
                for($i = 0; $i < 10; $i++){
                    $token .= $this->generate_salt();
                }
                // Insert the new token to database
                $stmt = $this->db->sqli->prepare("INSERT INTO tokens
                                                  (uid, token_body, valid_until) VALUES 
                                                  (?,?,?)");
                $stmt->bind_param("isi", $userInfo["id"], $token, $validUntil);
                if($stmt->execute()){
                    $result = $token;
                } // Oops, some unknown error happened :(
            }

            return $result;
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
        * get_by_id
        * Get a user by id, returns an array in the following format
        * key      | value
        * ---------------------------------------------
        * id       | user id (int)
        * username | username, again (string)
        * password | salted & hashed password (string)
        * salt     | the salt for password (string)
        * Returns False if id is not found.
        *
        * Parameters:
        *     (id) - ID you want to find
        */
        public function get_by_id($id){
            $result = NULL;
            // Prepare MySQL statement
            $stmt = $this->db->sqli->prepare("SELECT * FROM users WHERE id=?");
            $stmt->bind_param("i",$id);
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
        * generate_salt
        * Generate a random salt string.
        */
        public function generate_salt(){
            // Generate a random number from 0 - 999
            $randNum = rand(0,999);
            // Add to current time 
            $randStr = $randNum . time();
            // MD5 hash it.
            $result = hash('md5', $randStr);
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