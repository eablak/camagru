<?php

namespace App\Model;
use PDO;
use PDOException;

class User{

    private PDO $conn;

    public function __construct(PDO $db){
        $this->conn = $db;
    }

    public function register(string $username , string $password, string $email){

        try{

            $stmt = $this->conn->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
            $result = $stmt->execute(['username' => $username, 'password' => $password, 'email' => $email]);

            return 1;
            
        }catch(PDOException $e){
            if ($e->getCode() == '23000')
                return -1;
        }
    }



    public function getUsername(){
        
    }


}



?>