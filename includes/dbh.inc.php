<?php

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "loginsystem";

/*$dbServername = "phpmyadmin3.c2wxg7ustixm.eu-west-2.rds.amazonaws.com";
$dbUsername = "phpMyAdmin3";
$dbPassword = "phpMyAdmin3";
$dbName = "pecora";*/

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);