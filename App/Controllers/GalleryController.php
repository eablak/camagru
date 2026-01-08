<?php

namespace App\Controllers;

require_once __DIR__ . '/../../public/vendor/autoload.php';
use App\Database;
use App\Model\Gallery;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class GalleryController{

    private $userInfo;
    private $file_path;
    private $db;
    private $galleryModel;

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
            if ($this->galleryModel->saveComment($currentId, $json['imageId'], $json['commentText'])){
                $imgOwnerEmail = $this->galleryModel->getImageEmail($json['imageId']);
                if ($imgOwnerEmail){
                    $userEmailStatus = $this->galleryModel->getEmailStatus($json['imageId']);
                    if ($userEmailStatus)
                        $this->sendEmailNotification($imgOwnerEmail);
                }
            }
        }

    }

    public function mailer(){

        $mail = new PHPMailer(true);

        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $mail->isSMTP();
        $mail->SMTPAuth = true;

        $mail->Host = "smtp.gmail.com";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Username = 'esrablk9@gmail.com';
        $mail->Password = 'dupr rmfm riuu vwrr ';

        $mail->isHtml(true);

        return $mail;

    }

    public function sendEmailNotification(string $emailAddress){

        $mail = $this->mailer();

        $mail->setFrom('esrablk9@gmail.com', 'camagru');
        $mail->addAddress($emailAddress);
        $mail->Subject = "Comment Received";
        $mail->Body = <<<END
        Hi! Your Image get a new comment.
        END;

        try {
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }

    }



}


?>