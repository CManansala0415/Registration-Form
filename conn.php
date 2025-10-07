<?php
$servername = "localhost";
$username   = "root";
$password   = "";              // change if needed
$dbname     = "sia"; // 🔹 replace with your actual database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . mysqli_connect_error()
    ]));
}
?>