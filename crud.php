<?php
require_once('conn.php');

$firstName   = $_POST['firstName'];
$middleName  = $_POST['middleName'];
$lastName    = $_POST['lastName'];
$suffix      = $_POST['suffix'];
$birthday    = $_POST['birthday'];
$gender      = $_POST['gender'];
$nationality = $_POST['nationality'];
$home        = $_POST['home'];
$country     = $_POST['country'];
$province    = $_POST['province'];
$city        = $_POST['city'];
$barangay    = $_POST['barangay'];
$zipcode     = $_POST['zipcode'];

// Insert into the persons table
$sql = "
INSERT INTO persons (
    first_name, middle_name, last_name, suffix_name,
    birthday, gender, nationality,
    home, country, province, city, barangay, zipcode, created_at
) VALUES (
    '$firstName', '$middleName', '$lastName', '$suffix',
    '$birthday', '$gender', '$nationality',
    '$home', '$country', '$province', '$city', '$barangay', '$zipcode', NOW()
)
";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => "success",
        "message" => "New record created successfully!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
