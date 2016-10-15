<?php
    include "../config.php";
    include "include/databaseconnection.php";
    include "include/user.php";
    include "include/password.php";

    $db = new WebPasswordManager\DatabaseConnection(
        WebPasswordManager\DbAuth::$dbHost,
        WebPasswordManager\DbAuth::$dbUsername,
        WebPasswordManager\DbAuth::$dbPassword,
        WebPasswordManager\DbAuth::$dbName
    );

    $user = new WebPasswordManager\User($db);
    $pwd = new WebPasswordManager\Password($db);

    $result = array(
        "status"=>"failed",
        "message"=>"not found"
    );
    // Begin API Routing
    switch($_POST["action"]){
        // Create a new user
        case "create_user":
            // Check global config to see if user creation is open 
            if(WebPasswordManager\GlobalConfig::$allowUserCreation){
                if($user->create($_POST["username"], $_POST["password"])){
                    $result["status"] = "success";
                    $result["message"] = "user created";
                } else {
                    $result["message"] = "unknown error, username must already exist";
                }
            } else {
                $result["message"] = "user creation closed";
            }
        break;
        // Create a new authentication token
        case "create_token":
            // Check if username and password matches
            if($user->check_password($_POST["username"], $_POST["password"])){
                // Now create a new token 
                $token = $user->create_token($_POST["username"]);
                if($token){
                    $result["status"] = "success";
                    $result["message"] = $token;
                } else {
                    $result["message"] = "unknown error";
                }
            } else {
                $result["message"] = "username or password not match";
            }
        break;
        // Create a new password for user (not authentication password)
        case "create_password":
            $userInfo = $user->simple_auth($_POST["uname"], $_POST["token"]);
            if($userInfo){
                if($pwd->create($userInfo["id"], $_POST["name"], $_POST["password"], $_POST["description"])){
                    $result["status"] = "success";
                    $result["message"] = "password created";
                } else {
                    $result["message"] = "unknown error";
                }
            } else {
                $result["message"] = "you are not authorized";
            }
        break;
        // Get all passwords for an user 
        case "get_all_password":
            $userInfo = $user->simple_auth($_POST["uname"], $_POST["token"]);
            if($userInfo){
                $result["status"] = "success";
                $result["message"] = $pwd->get_by_uid($userInfo["id"]);
            } else {
                $result["message"] = "you are not authorized";
            }
        break;
        // Check to see if a token is valid
        case "check_token":
            $userInfo = $user->simple_auth($_POST["uname"], $_POST["token"]);
            if($userInfo){
                $result["status"] = "success";
                $result["message"] = $userInfo;
            } else {
                $result["message"] = "token not valid";
            }
        break;
        // Delete a password 
        case "delete_password":
            $userInfo = $user->simple_auth($_POST["uname"], $_POST["token"]);
            $passwordInfo = $pwd->get_by_id($_POST["id"]);
            if($userInfo){
                if($userInfo["id"] == $passwordInfo["uid"]){
                    if($pwd->delete($_POST["id"])){
                        $result["status"] = "success";
                        $result["message"] = "password deleted";
                    } else {
                        $result["message"] = "unknown error";
                    }
                } else {
                    $result["message"] = "you are not authorized";
                }
            } else {
                $result["message"] = "you are not authorized";
            }
        break;

       /** 
        * update_password
        * Update a password
        * Input:
        *     (uname) - Username, hashed format
        *     (token) - Authentication token
        *     (id) - Password ID
        *     (name) - New password name
        *     (password) - New password, encrypted format
        *     (description) - New password description
        * Error:
        *     "you are not authorized" - uname and token not valid
        *     "unknown error" - something wrong happened while trying to delete record
        */
        case "update_password":
            $userInfo = $user->simple_auth($_POST["uname"], $_POST["token"]);
            $passwordInfo = $pwd->get_by_id($_POST["id"]);
            if($userInfo){
                if($userInfo["id"] == $passwordInfo["uid"]){
                    //$userInfo["id"], $_POST["name"], $_POST["password"], $_POST["description"]
                    if($pwd->update($_POST["id"],$_POST["name"],$_POST["password"],$_POST["description"])){
                        $result["status"] = "success";
                        $result["message"] = "password updated";
                    } else {
                        $result["message"] = "unknown error";
                    }
                } else {
                    $result["message"] = "you are not authorized";
                }
            } else {
                $result["message"] = "you are not authorized";
            }
        break;
    }

    echo json_encode($result);
?>