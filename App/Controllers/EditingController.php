<?php

namespace App\Controllers;
use App\Database;

class EditingController{

    var $file_path;

    public function __construct(){
        $this->file_path = dirname(__DIR__);
        $this->file_path = str_replace('/App', '/public/assets/html', $this->file_path);
        $this->db = Database::connect();
    }

    public function editing_index(){
        
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            include $this->file_path . '/editing.html';
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST"){

            header('Content-Type: application/json; charset=utf-8');

            $str = file_get_contents("php://input");
            $json = json_decode($str, true);

            echo json_encode(["success" => "succesfully sended", "data" => $json['superposable']]);
            return ;
        }


    }


}


?>