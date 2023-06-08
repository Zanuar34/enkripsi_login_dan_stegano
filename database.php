<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "root";
$dbName = "login_register";

$mysqli = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$mysqli) {
    die("Something went wrong;");
}

?>