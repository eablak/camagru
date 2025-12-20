<?php

namespace App\Model;
require_once __DIR__ . '/../../public/vendor/autoload.php';
use PDO;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class User{

    private PDO $conn;

    public function __construct(PDO $db){
        $this->conn = $db;
    }

    public function register(string $username , string $password, string $email, string $activation_token_hash){

        try{

            $stmt = $this->conn->prepare("INSERT INTO users (username, password, email, account_activation_hash) VALUES (:username, :password, :email, :activation_token_hash)");
            
            $result = $stmt->execute(['username' => $username, 'password' => $password, 'email' => $email, 'activation_token_hash' => $activation_token_hash]);

            return 1;
            
        }catch(PDOException $e){
            if ($e->getCode() == '23000')
                return -1;
            else
                return -2;
        }
    }



    public function activate_account(){

        $token = $_GET["token"];
        $token_hash = hash("sha256", $token);

        $sql = "SELECT * FROM users
                WHERE account_activation_hash = :account_activation_hash";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute(['account_activation_hash' => $token_hash]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user === null) {
            die("token not found");
        }

        $sql = "UPDATE users
                SET account_activation_hash = '1'
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return ($stmt->execute(['id' => $user['id']]));

        

    }


}



?>