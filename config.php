<?php
    namespace WebPasswordManager;
    class DbAuth
    {
        public static $dbHost = "localhost";
        public static $dbUsername = "root";
        public static $dbPassword = "";
        public static $dbName = "web_password_manager";
    }

    class GlobalConfig
    {
        // Allow new users to be created
        public static $allowUserCreation = True;
    }
?>