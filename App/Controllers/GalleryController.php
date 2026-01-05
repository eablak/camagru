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
        $totalResults = $this->galleryModel->getAllImagesCount();
        $resultPerPage = 5;
        $totalPages = ceil($totalResults / $resultPerPage);

        if (isset($_GET['page'])){
            $page = (int) $_GET['page'];
        }else{
            $page = 1;
        }

        $page = max(1, min($page, $totalPages));
        $startFrom = ($page - 1) * $resultPerPage;

        $relatedImages = $this->galleryModel->getRelatedImages($startFrom, $resultPerPage);

        include $this->file_path . '/gallery.html';

        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            error_log();
        }

    }

}


?>