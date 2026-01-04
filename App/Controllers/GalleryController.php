<?php

namespace App\Controllers;
use App\Database;
use App\Model\Gallery;


class GalleryController{

    public function __construct(){
        
        session_start();
        $this->userInfo = false;

        if (isset($_SESSION['user'])){
            $this->userInfo = true;
        }

        $this->file_path = dirname(__DIR__);
        $this->file_path = str_replace('/App', '/public/assets/html', $this->file_path);
        $this->db = Database::connect();
        $this->galleryModel = new Gallery($this->db);

    }


    public function gallery_index(){

        $condition = $this->userInfo;
        $descImgPaths = $this->galleryModel->getAllImages();

        // foreach ($descImgPaths as $imgPath)
            // error_log($imgPath);
        
        include $this->file_path . '/gallery.html';

    }

}


?>