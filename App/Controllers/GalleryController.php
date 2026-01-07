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
        
        $imgLikes = [];
        $imgComments = [];
        foreach($relatedImages as $relatedImg){
            $likeCount = $this->galleryModel->relatedImagesLikes($relatedImg['id']);
            $imgLikes[$relatedImg['id']] = $likeCount;
            $comments = $this->galleryModel->relatedImagesComments($relatedImg['id']);
            if ($comments)
                $imgComments[$relatedImg['id']] = $comments;
        }

        include $this->file_path . '/gallery.html';

    }


    public function likeImage(){

        $str = file_get_contents("php://input");
        $json = json_decode($str, true);

        if ($this->userInfo){
            $currentId = $_SESSION['id'];
            $this->galleryModel->saveLike($currentId, $json['imageId']);
        }

    }


    public function commentImage(){

        $str = file_get_contents("php://input");
        $json = json_decode($str, true);

        if ($this->userInfo){
            $currentId = $_SESSION['id'];
            $this->galleryModel->saveComment($currentId, $json['imageId'], $json['commentText']);
        }

    }


}


?>