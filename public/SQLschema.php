<?php

include("database.php");
use App\Database;



$sql_user = "CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(30) NOT NULL UNIQUE,
password VARCHAR(150) NOT NULL,
email VARCHAR(50) NOT NULL UNIQUE,
account_activation_hash VARCHAR(150),
reset_token_hash VARCHAR(150) UNIQUE,
reset_token_expires_at DATETIME )";


$sql_gallery = "CREATE TABLE galleries (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
photo_path VARCHAR(150) NOT NULL ,
created_date DATETIME,

FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE)";


$sql_like = "CREATE TABLE likes (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
photo_id INT NOT NULL,

UNIQUE (user_id, photo_id),

FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY (photo_id) REFERENCES galleries(id) ON DELETE CASCADE)";


$sql_comment = "CREATE TABLE comments (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
photo_id INT NOT NULL,
content VARCHAR(200) NOT NULL,

FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY (photo_id) REFERENCES galleries(id) ON DELETE CASCADE)";


try{
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $pdo->exec($sql_user);
  $pdo->exec($sql_gallery);
  $pdo->exec($sql_like);
  $pdo->exec($sql_comment);

  echo "All tables created successfully\n";
}catch(PDOException $e){
  echo "Error creating table: " . $e->getMessage() . "\n";
}

?>