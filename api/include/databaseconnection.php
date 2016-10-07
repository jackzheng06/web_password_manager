<?php
    namespace WebPasswordManager;

    class DatabaseConnection {
        public $sqli;

       /**
        * _construct
        * A database connection class, not required, but just in case we need
        * to add for functionaility to it.
        * This constructor will not raise any exception, so it is up to you to 
        * check connection status.
        *
        * Parameters:
        *     (host) - Database host
        *     (username) - Database username
        *     (password) - Database password
        *     (name) - Database name
        */
        public function __construct($host, $username, $password, $name){
            $this->sqli = new \MySQLi($host, $username, $password, $name);
        }
    }
?>