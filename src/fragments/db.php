<?php

$connect = mysqli_connect("localhost", "root", "", "IMT_database");
mysqli_set_charset($connect, "utf8");

// Check connection
if (!$connect) {
    die("Database connection failed."); 
}


?>