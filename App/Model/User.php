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



    public function login(string $username, string $password): int|array{

        try{

            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username=?");

            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])){
                if ($user['account_activation_hash'] === '1'){
                    return $user;
                }else{
                    return -1;
                }
            }

        }catch(PDOExceptioon $e){

        }
        return 0;
    }


    public function reset_password_hashes(string $email): string|bool{

        $token = bin2hex(random_bytes(16));
        $token_hash = hash("sha256", $token);
        $expiry = date("Y-m-d H:i:s", time() + 60*30);

        $sql = "UPDATE users SET reset_token_hash = :reset_token_hash, reset_token_expires_at = :reset_token_expires_at WHERE email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['reset_token_hash' => $token_hash, 'reset_token_expires_at' => $expiry, 'email' => $email]);

        if ($stmt->rowCount())
            return $token;
        return false;

    }

    public function reset_password_mail(string $reset_token_hash){

        $token_hash = hash("sha256", $reset_token_hash);
        $sql = "SELECT * FROM users WHERE reset_token_hash = :reset_token_hash";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['reset_token_hash' => $token_hash]);

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user === false)
            return -1;

        if (strtotime($user['reset_token_expires_at']) <= time())
            return -2;

    }


    
    public function process_reset_password(string $token, string $password){

        $token_hash = hash("sha256", $token);

        $sql = "SELECT * FROM users WHERE reset_token_hash = :reset_token_hash";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['reset_token_hash' => $token_hash]);

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user === false)
            return -1;

        if (strtotime($user['reset_token_expires_at']) <= time())
            return -2;

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = :password_hash, reset_token_hash = :reset_token_hash, reset_token_expires_at = :reset_token_expires_at WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['password_hash' => $password_hash, 'reset_token_hash' => NULL, 'reset_token_expires_at' => NULL, 'id' => $user['id']]);

        return 1;
    }


    public function updateInfos(string $new_username, string $new_password, string $new_email, int $id){

        $sql = "UPDATE users SET username = :username, password= :password, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['username' => $new_username, 'password' => $new_password, 'email' => $new_email, 'id' => $id]);

    }

}



?>