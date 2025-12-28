<?php

namespace App\Controllers;
use App\Database;
use App\Model\Editing;

class EditingController{

    var $file_path;

    public function __construct(){

        session_start();
        if(!isset($_SESSION['user'])){
            header("Location: /login");
            exit;
        }

        $this->file_path = dirname(__DIR__);
        $this->file_path = str_replace('/App', '/public/assets/html', $this->file_path);
        $this->db = Database::connect();
        $this->editingModel = new Editing($this->db);
        $this->thumbnailPath = "";
    }


    public function imageName(){
        
        $currentUser = $_SESSION['user'];
        $currentUserid = $_SESSION['id'];

        $returnCount = $this->editingModel->userPhotoCount((int)($currentUserid));
        
        $fileName = "user_" . $currentUserid . "photo_" . $returnCount+1 . ".jpeg";
        
        return $fileName;
    }


    public function createImage(string $imageURL, string $selectedName){


        $imageContent = base64_decode(str_replace("data:image/jpeg;base64,", "", $imageURL));

        $imgFilePath = str_replace('/html', '/img/pictures/' ,$this->file_path);
        $imgFilePath = $imgFilePath . "/image.jpeg";

        $ifp = fopen($imgFilePath, 'wb');
        if (fwrite($ifp, $imageContent)){
            fclose($ifp);

            $superposablePath = str_replace('/html', '/img/superposable/', $this->file_path);
            switch($selectedName){
                case 'cat':
                    $superposablePath .= 'img1.png';
                break;
                case 'mouse':
                    $superposablePath .= 'img2.png';
                break;
                case 'hamster':
                    $superposablePath .= 'img3.png';
                break;
                case 'dog':
                    $superposablePath .= 'img4.png';
                break;
                case 'duck':
                    $superposablePath .= 'img5.png';
                break;
            }

            // error_log("superposable: " . $superposablePath);

            $webcamImage = imagecreatefromjpeg($imgFilePath);
            $pasteImage = imagecreatefrompng($superposablePath);
            imagealphablending($webcamImage, true);
            imagesavealpha($webcamImage, true);

            $webcamW = imagesx($webcamImage);
            $webcamY = imagesy($webcamImage);
            $videoCssW = 800;
            $videoCssY = 600;

            $scaleX = $webcamW/$videoCssW;
            $scaleY = $webcamY/$videoCssY;

            $cssLeft = 80;
            $cssTop = 80;

            $destX = (int)($cssLeft * $scaleX);
            $destY = (int)($cssTop * $scaleY);
            
            $overlayCssW = $videoCssW * 0.20 ;
            $overlayW = (int)($overlayCssW * $scaleX);

            $pngW = imagesx($pasteImage);
            $pngH = imagesy($pasteImage);
            $overlayH = (int)($overlayW * ($pngH / $pngW));

            $resizedOverlay = imagecreatetruecolor($overlayW, $overlayH);
            imagealphablending($resizedOverlay, false);
            imagesavealpha($resizedOverlay, true);

            imagecopyresampled($resizedOverlay, $pasteImage, 0, 0, 0, 0, $overlayW, $overlayH, $pngW, $pngH);

            imagecopy($webcamImage, $resizedOverlay, $destX, $destY, 0, 0, $overlayW, $overlayH);

            
            $this->thumbnailPath = str_replace('html', 'img/thumbnails/', $this->file_path);
            $this->thumbnailPath = $this->thumbnailPath . $this->imageName();
            imagejpeg($webcamImage, $this->thumbnailPath, 95);

            return true;
        }

        return false;
    }


    public function saveImagetoDB(){

        $this->editingModel->saveImagetoDB($_SESSION['id'], $this->thumbnailPath);

    }




    public function editing_index(){
        
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            include $this->file_path . '/editing.html';
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST"){

            header('Content-Type: application/json; charset=utf-8');

            $str = file_get_contents("php://input");
            $json = json_decode($str, true);

            if ($this->createImage($json['webcam'], $json['superposable'])){
                $this->saveImagetoDB();
                // $this->createImageDB("userid", "photopath");
                echo json_encode(["message" => "Image succesfully saved!"]);
            } else{
                echo json_encode(["message" => "Failed while saving image!"]);
            }
            return ;
        }


    }


}


?>