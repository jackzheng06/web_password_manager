<?php
    include "../config.php";
    include "include/databaseconnection.php";
    include "include/user.php";

    $db = new WebPasswordManager\DatabaseConnection(
        WebPasswordManager\DbAuth::$dbHost,
        WebPasswordManager\DbAuth::$dbUsername,
        WebPasswordManager\DbAuth::$dbPassword,
        WebPasswordManager\DbAuth::$dbName
    );

    $user = new WebPasswordManager\User($db);

    if($user->get_by_username("test")){

    } else {
        echo "No user found";
    }
?>