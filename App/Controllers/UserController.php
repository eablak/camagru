<?php

namespace App\Controllers;
use App\Model\User;
use App\Database;

class UserController{

    var $file_path;

    public function __construct(){
        $this->file_path = dirname(__DIR__);
        $this->file_path = str_replace('/App', '/public/assets/html', $this->file_path);
    }

    public function register(){

        $this->file_path = $this->file_path . '/register.html';
        include $this->file_path;


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            $password = password_hash($password, PASSWORD_DEFAULT);

            $db = Database::connect();
            $userModel = new User($db);
            $result = $userModel->register($name, $password, $email);

            if ($result === 1){
                echo "Successfully inserted";
            }else{
                echo "Error: " . $result->error;
            }
        }


    }




}


?>