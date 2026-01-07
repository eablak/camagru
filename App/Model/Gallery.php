<?php

namespace App\Model;
use PDO;
use PDOException;

class Gallery{

    private PDO $conn;

    public function __construct(PDO $db){
        $this->conn = $db;
    }


    public function getAllImagesCount(){

        $sql = "SELECT COUNT(id) AS total FROM galleries";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    }


    public function getRelatedImages(int $startFrom, int $resultPerPage){

        $sql = "SELECT * FROM galleries ORDER BY created_date DESC LIMIT :startFrom, :resultsPerPage";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':startFrom', $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(':resultsPerPage', $resultPerPage, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }


    public function relatedImagesLikes(int $imgId){

        $sql = "SELECT COUNT(*) FROM likes WHERE photo_id = :photo_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['photo_id' => $imgId]);

        $likeCount = (int)$stmt->fetchColumn();
        return $likeCount;

    }

    public function relatedImagesComments(int $imgId){

        $sql = "SELECT content, username FROM comments c INNER JOIN users u on u.id = c.user_id WHERE photo_id = :photo_id ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['photo_id' => $imgId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;

    }



    public function saveLike(int $userId, int $imageId){

        $sql = "INSERT IGNORE INTO likes (user_id, photo_id) VALUES (:user_id, :photo_id)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute(['user_id' => $userId, 'photo_id' => $imageId]);
        return $result;

    }

    public function saveComment(int $userId, int $imageId, string $comment){

        $sql = "INSERT INTO comments (user_id, photo_id, content) VALUES (:user_id, :photo_id, :content)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute(['user_id' => $userId, 'photo_id' => $imageId, 'content' => $comment]);
        return $result;

    }

}




?>