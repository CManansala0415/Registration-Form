<?php
require_once('conn.php');

// Get and sanitize inputs
$firstName   = mysqli_real_escape_string($conn, $_POST['firstName']);
$middleName  = mysqli_real_escape_string($conn, $_POST['middleName']);
$lastName    = mysqli_real_escape_string($conn, $_POST['lastName']);
$suffix      = mysqli_real_escape_string($conn, $_POST['suffix']);
$birthday    = mysqli_real_escape_string($conn, $_POST['birthday']);
$gender      = mysqli_real_escape_string($conn, $_POST['gender']);
$nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
$home        = mysqli_real_escape_string($conn, $_POST['home']);
$country     = mysqli_real_escape_string($conn, $_POST['country']);
$region      = mysqli_real_escape_string($conn, $_POST['region']);
$province    = mysqli_real_escape_string($conn, $_POST['province']);
$city        = mysqli_real_escape_string($conn, $_POST['city']);
$barangay    = mysqli_real_escape_string($conn, $_POST['barangay']);
$zipcode     = mysqli_real_escape_string($conn, $_POST['zipcode']);

// Insert into the persons table
$sql = "
INSERT INTO persons (
    first_name, middle_name, last_name, suffix_name,
    birthday, gender, nationality,
    home, country, region, province, city, barangay, zipcode, created_at
) VALUES (
    '$firstName', '$middleName', '$lastName', '$suffix',
    '$birthday', '$gender', '$nationality',
    '$home', '$country', '$region', '$province', '$city', '$barangay', '$zipcode', NOW()
)
";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => "success",
        "message" => "Registration successful!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>