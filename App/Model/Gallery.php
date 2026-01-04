<?php

namespace App\Model;
use PDO;
use PDOException;

class Gallery{

    private PDO $conn;

    public function __construct(PDO $db){
        $this->conn = $db;
    }


    public function getAllImages(){

        $sql = "SELECT photo_path FROM galleries ORDER BY created_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);

    }



}




?>