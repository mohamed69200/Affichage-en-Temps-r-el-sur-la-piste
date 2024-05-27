<?php
$servername = "172.19.6.10";
$username = "root";
$password = "sn";
$database = "Karting";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
