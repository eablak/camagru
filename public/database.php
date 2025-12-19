<?php


$db_server = "localhost";
$db_user = "phpmyadmin";
$db_pass = "phpmyadmin";
$db_name = "camagru";
$conn = "";

try{
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
}catch (mysqli_sql_exception){
    echo "Could not connect!";
}

?>