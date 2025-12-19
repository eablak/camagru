<?php

namespace App\Model;
use PDO;

class User{

    private PDO $conn;

    public function __construct(PDO $db){
        $this->conn = $db;
    }

    public function register(string $username , string $password, string $email){

        $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
        return $this->conn->exec($sql);
    }



    public function getUsername(){
        
    }


}



?>