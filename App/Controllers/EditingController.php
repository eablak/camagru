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


    public function saveImage(string $imageURL, string $selectedName){


        $imageContent = base64_decode(str_replace("data:image/jpeg;base64,", "", $imageURL));

        $imgFilePath = str_replace('/html', '/img/pictures/' ,$this->file_path);
        $imgFilePath = $imgFilePath . "/image.png";

        $ifp = fopen($imgFilePath, 'wb');
        if (fwrite($ifp, $imageContent)){
            fclose($ifp);
            return true;
        }

        return false;
    }




    public function editing_index(){
        
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            include $this->file_path . '/editing.html';
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST"){

            header('Content-Type: application/json; charset=utf-8');

            $str = file_get_contents("php://input");
            $json = json_decode($str, true);

            if ($this->saveImage($json['webcam'], $json['superposable'])){
                // $this->saveImageDB("userid", "photopath");
                echo json_encode(["message" => "Image succesfully saved!"]);
            } else{
                echo json_encode(["message" => "Failed while saving image!"]);
            }
            return ;
        }


    }


}


?>