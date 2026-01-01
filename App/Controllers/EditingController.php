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

            
            $this->localPath = str_replace('html', 'img/thumbnails/', $this->file_path);
            $this->localPath = $this->localPath . $this->imageName();
            imagejpeg($webcamImage, $this->localPath, 95);

            $this->thumbnailPath = '/assets/img/thumbnails/' . $this->imageName();
            return true;
        }

        return false;
    }


    public function saveImagetoDB(){

        $this->editingModel->saveImagetoDB($_SESSION['id'], $this->thumbnailPath);

    }


    public function editing_index(){
        
        if ($_SERVER["REQUEST_METHOD"] == "GET"){
            $sideContentHtml = $this->sideContentImages();
            include $this->file_path . '/editing.html';
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST"){

            if (isset($_POST['actionFileUpload'])){
                if ($this->fileSubmit()){
                    // a
                }
            }else if(!empty(file_get_contents("php://input"))){
                
                header('Content-Type: application/json; charset=utf-8');
    
                $str = file_get_contents("php://input");
                $json = json_decode($str, true);
    
                if ($this->createImage($json['webcam'], $json['superposable'])){
                    $this->saveImagetoDB();
                    echo json_encode(["message" => "Image succesfully saved!"]);
                } else{
                    echo json_encode(["message" => "Failed while saving image!"]);
                }

            }else{
                header("HTTP/1.0 404 Not Found");
            }

            return ;
        }


    }


    public function sideContentImages(){

        $userid = $_SESSION['id'];
        $imgPaths = $this->editingModel->getUserImagesPath($userid);

        $html = '';
        foreach ($imgPaths as $imgpath){
            // error_log("PATH!!  ". $imgpath);
            $html .= '<img src="' . htmlspecialchars($imgpath) . '" alt="User photo" />';
        }
        return $html;

    }


    public function fileSubmit(){

        header('Content-Type: application/json; charset=utf-8');

        if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0){
            
            $allowed_ext = array("jpeg" => "img/jpeg");
            
            $file_name = $_FILES["fileToUpload"]["name"];
            $file_type = $_FILES["fileToUpload"]["type"];
            $file_size = $_FILES["fileToUpload"]["size"];

            $ext = pathinfo($file_name, PATHINFO_EXTENSION);

            if (!array_key_exists($ext, $allowed_ext)){
                // die("Error: Please select a valid file format.");
                echo json_encode(["message" => "Error: Please select a valid file format."]);
                return ;
            }

            $target_dir = str_replace('/html', '/img/pictures/', $this->file_path);
            $target_file = $target_dir . "image.jpeg";

            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){
                $str = "The file " . $_FILES["fileToUpload"]["name"] . " has been uploaded";
                echo json_encode(["message" => $str]);
            }

        }else{
            echo json_encode(["message" => "Uploaded Error"]);
            return NULL;
        }

        return $target_file;

    }





}


?>