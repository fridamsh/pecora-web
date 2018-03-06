<?php

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "Volleyball94";
$dbName = "pecora";

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
mysqli_set_charset($conn, 'utf8');