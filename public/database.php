<?php

namespace App;

use Exception; 
use PDO;
use PDOException;

class Database{

    private static $username = "phpmyadmin";
    private static $password = "phpmyadmin";
    private static $db_name = "camagru";
    private static $dsn = "mysql:host=db;dbname=camagru;charset=utf8";

    public static function connect(){
        try{

            $pdo = new PDO(self::$dsn, self::$username, self::$password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connection success";
        }catch (PDOException $e){
            // error_log(date('Y-m-d: H:i:s') . "Database connection error" . $e->getMessage(), 3, "error.log");
            throw new Exception("Database connection failed");
        }

        return $pdo;
    }
}


?>