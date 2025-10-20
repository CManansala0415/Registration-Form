<?php
require_once('conn.php');

// If a GET request asks for initial locations data, return countries & nationalities
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'locations_init') {
    $out = [ 'countries' => [], 'nationalities' => [] ];

    $cSql = "SELECT id, name FROM countries ORDER BY id";
    if ($cres = mysqli_query($conn, $cSql)) {
        while ($crow = mysqli_fetch_assoc($cres)) $out['countries'][] = $crow;
        mysqli_free_result($cres);
    }

    $nSql = "SELECT id, name FROM nationalities ORDER BY name";
    if ($nres = mysqli_query($conn, $nSql)) {
        while ($nrow = mysqli_fetch_assoc($nres)) $out['nationalities'][] = $nrow;
        mysqli_free_result($nres);
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($out);
    mysqli_close($conn);
    exit;
}

// Otherwise handle POST insertion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName   = $_POST['firstName'] ?? '';
    $middleName  = $_POST['middleName'] ?? '';
    $lastName    = $_POST['lastName'] ?? '';
    $suffix      = $_POST['suffix'] ?? '';
    $birthday    = $_POST['birthday'] ?? '';
    $gender      = $_POST['gender'] ?? '';
    $nationality = $_POST['nationality'] ?? '';
    $home        = $_POST['home'] ?? '';
    $country     = $_POST['country'] ?? '';
    $province    = $_POST['province'] ?? '';
    $city        = $_POST['city'] ?? '';
    $barangay    = $_POST['barangay'] ?? '';
    $zipcode     = $_POST['zipcode'] ?? '';

    // Insert into the persons table (consider using prepared statements for safety)
    $sql = "INSERT INTO persons (
        first_name, middle_name, last_name, suffix_name,
        birthday, gender, nationality,
        home, country, province, city, barangay, zipcode, created_at
    ) VALUES (
        '$firstName', '$middleName', '$lastName', '$suffix',
        '$birthday', '$gender', '$nationality',
        '$home', '$country', '$province', '$city', '$barangay', '$zipcode', NOW()
    )";

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
    exit;
}

// if nothing matched
http_response_code(400);
echo json_encode(["status"=>"error","message"=>"Invalid request"]);
mysqli_close($conn);
?>
