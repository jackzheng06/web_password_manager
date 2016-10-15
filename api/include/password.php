<?php
    namespace WebPasswordManager;

    class Password {
        public $db;

       /**
        * _construct
        * Password class, used to manage user stored passwords
        *
        * Parameters:
        *     (db) - A DatabaseConnection instance
        */
        public function __construct($db){
            $this->db = $db;
        }

       /**
        * create
        * Create a new password for user
        *
        * Parameters:
        *     (uid) - User ID
        *     (name) - Name for the new password 
        *     (password) - Password in encrypted format, should be JSON
        *     (description) - Password description
        */
        public function create($uid, $name, $password, $description){
            $result = False;
            // Prepare MySQL statement
            $stmt = $this->db->sqli->prepare("INSERT INTO passwords 
                                              (uid, pwd_name, encrypted_data, description)
                                              VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $uid, $name, $password, $description);
            // If execution is successful
            if($stmt->execute()){
                $result = True;
            }
            return $result;
        }

       /**
        * get_by_uid
        * Get all passwords under an user id.
        * Returns an 2d array in the following format:
        * key                | value
        * ---------------------------------------------
        * id                 | password id (int)
        * uid                | user id (int)
        * pwd_name           | name of the password (string)
        * encrypted_data     | encrypted password body (json string)
        * description        | description of the password (string)
        *
        * Parameters:
        *     (uid) - User ID
        */
        public function get_by_uid($uid){
            $result = array();
            // Prepare MySQL statement
            $stmt = $this->db->sqli->prepare("SELECT id,uid,pwd_name,encrypted_data,description FROM passwords WHERE uid=?");
            $stmt->bind_param("i",$uid);
            $stmt->execute();
            $stmt->store_result();
            // Bind result
            $stmt->bind_result($resultId, $resultUid, $resultPwdName, $resultEncryptedData, $resultDescription);
            // While loop to get all passwords
            while($stmt->fetch()){
                array_push($result, array(
                    "id"=>$resultId,
                    "uid"=>$resultUid,
                    "pwd_name"=>$resultPwdName,
                    "encrypted_data"=>$resultEncryptedData,
                    "description"=>$resultDescription
                ));
            }
            // Return result
            return $result;
        }

       /**
        * get_by_uid
        * Get a password by its ID 
        * Returns an 2d array in the following format:
        * key                | value
        * ---------------------------------------------
        * id                 | password id (int)
        * uid                | user id (int)
        * pwd_name           | name of the password (string)
        * encrypted_data     | encrypted password body (json string)
        * description        | description of the password (string)
        * Returns False if password is not found
        *
        * Parameters:
        *     (id) - Password ID
        */
        public function get_by_id($id){
            $result = False;
            // Prepare MySQL Statememt
            $stmt = $this->db->sqli->prepare("SELECT id,uid,pwd_name,encrypted_data,description FROM passwords WHERE id=?");
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $stmt->store_result();
            // If num_rows is greater than 0
            if($stmt->num_rows > 0){
                // Fetch infomation
                $stmt->bind_result($resultId, $resultUid, $resultPwdName, $resultEncryptedData, $resultDescription);
                $stmt->fetch();
                // Dump info into result
                $result = array(
                    "id"=>$resultId,
                    "uid"=>$resultUid,
                    "pwd_name"=>$resultPwdName,
                    "encrypted_data"=>$resultEncryptedData,
                    "description"=>$resultDescription
                );
            }
            return $result;
        }

       /**
        * delete
        * Delete a password by id 
        * Returns a boolean indicates successfullness
        *
        * Parameters:
        *     (id) - Password ID   
        */
        public function delete($id){
            $result = False;
            // Prepare MySQL Statement
            $stmt = $this->db->sqli->prepare("DELETE FROM passwords WHERE id=?");
            $stmt->bind_param("i",$id);
            // Try to execute mysql statement
            if($stmt->execute()){
                // Set result to true
                $result = True;
            }
            return $result;
        }

       /**
        * update
        * Update a password by its id
        * Returns a boolean indicates successfullness
        *
        * Parameters:
        *     (id) - Password ID
        *     (name) - Name for the password
        *     (password) - Encrypted password data
        *     (description) - Password description
        */
        public function update($id, $name, $password, $description) {
            $result = False;
            // Prepare mysql statement
            $stmt = $this->db->sqli->prepare("UPDATE passwords SET pwd_name=?, encrypted_data=?, description=? WHERE id=?");
            // Bind param to mysql statement
            $stmt->bind_param("sssi",$name,$password,$description,$id);
            // Try to execute mysql statement
            if($stmt->execute()){
                //Set result to true
                $result = True;
            }
            return $result;
        }

    }
?>