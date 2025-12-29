<?php

namespace App\Model;
use PDO;
use PDOException;

class Editing{

    private PDO $conn;

    public function __construct(PDO $db){
        $this->conn = $db;
    }

    public function userPhotoCount(int $userid){

        try{

            $sql = "SELECT COUNT(*) FROM galleries WHERE user_id = :user_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['user_id' => $userid]);

            $photo_count = (int)$stmt->fetchColumn();
            return $photo_count;

        }catch (PDOException $e){

        }


    }


    public function saveImagetoDB(int $user_id, string $photo_path){

        $stmt = $this->conn->prepare("INSERT INTO galleries (user_id, photo_path, created_date) VALUES (:user_id, :photo_path, :created_date)");

        $created_date = date('Y-m-d H:i:s');

        $result = $stmt->execute(['user_id' => $user_id, 'photo_path' => $photo_path, 'created_date' => $created_date]);

    }


    public function getUserImagesPath(int $user_id){

        $sql = "SELECT photo_path FROM galleries WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $results;

    }

}


?>