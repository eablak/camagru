<?php

include("database.php");


$sql_user = "CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(30) NOT NULL UNIQUE,
password VARCHAR(150) NOT NULL,
email VARCHAR(50) NOT NULL UNIQUE )";


$sql_gallery = "CREATE TABLE galleries (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
photo_path VARCHAR(150) NOT NULL ,
created_date DATETIME,

FOREIGN KEY (user_id) REFERENCES users(id) )";


$sql_like = "CREATE TABLE likes (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
photo_id INT NOT NULL,

FOREIGN KEY (user_id) REFERENCES users(id),
FOREIGN KEY (photo_id) REFERENCES galleries(id) )";


$sql_comment = "CREATE TABLE comments (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
photo_id INT NOT NULL,
content VARCHAR(200) NOT NULL,

FOREIGN KEY (user_id) REFERENCES users(id),
FOREIGN KEY (photo_id) REFERENCES galleries(id) )";


try{
    if ($conn->query($sql_user) === TRUE && $conn->query($sql_gallery) === TRUE && $conn->query($sql_like) === TRUE && $conn->query($sql_comment) === TRUE) {
        echo "All tables created successfully\n";
    }
}catch(mysqli_sql_exception){
  echo "Error creating table: " . $conn->error . "\n";
}

?>