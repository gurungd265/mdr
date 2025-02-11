<?php
// Database connection settings
$servername = "mysql313.phy.lolipop.lan";
$username = "LAA1600585";
$password = "projectofmdriica";
$dbname = "LAA1600585-projectofmdr";

// Establishing a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
