<?php
// Turn on errors temporarily for debugging (remove/disable in production)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
require_once('conn.php');
mysqli_set_charset($conn, 'utf8mb4');

// capture stray output
ob_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
    exit;
}

$firstName   = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
$middleName  = isset($_POST['middleName']) ? trim($_POST['middleName']) : '';
$lastName    = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
$suffix      = isset($_POST['suffix']) ? trim($_POST['suffix']) : '';
$birthday    = isset($_POST['birthday']) ? trim($_POST['birthday']) : '';
$gender      = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$nationality = isset($_POST['nationality']) ? trim($_POST['nationality']) : '';
$home        = isset($_POST['home']) ? trim($_POST['home']) : '';
$region      = isset($_POST['region']) ? trim($_POST['region']) : '';
$country     = isset($_POST['country']) ? trim($_POST['country']) : '';
$province    = isset($_POST['province']) ? trim($_POST['province']) : '';
$city        = isset($_POST['city']) ? trim($_POST['city']) : '';
$barangay    = isset($_POST['barangay']) ? trim($_POST['barangay']) : '';
$zipcode     = isset($_POST['zipcode']) ? trim($_POST['zipcode']) : '';

// basic validation
if ($firstName === '' || $lastName === '') {
    echo json_encode(["status" => "error", "message" => "First name and last name are required."]);
    exit;
}

// Make sure the persons table has these columns; adjust names if not.
$sql = "INSERT INTO persons (
    first_name, middle_name, last_name, suffix_name,
    birthday, gender, nationality,
    home, region, country, province, city, barangay, zipcode, created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    $err = mysqli_error($conn);
    error_log("crud.php prepare failed: $err");
    echo json_encode(["status" => "error", "message" => "Prepare failed: $err"]);
    exit;
}

$bind = mysqli_stmt_bind_param(
    $stmt,
    'ssssssssssssss',
    $firstName,
    $middleName,
    $lastName,
    $suffix,
    $birthday,
    $gender,
    $nationality,
    $home,
    $region,
    $country,
    $province,
    $city,
    $barangay,
    $zipcode
);

if ($bind === false) {
    $err = mysqli_stmt_error($stmt);
    error_log("crud.php bind failed: $err");
    echo json_encode(["status" => "error", "message" => "Bind failed: $err"]);
    exit;
}

$exec = mysqli_stmt_execute($stmt);
$stray = trim(ob_get_clean());

if ($exec) {
    echo json_encode(["status" => "success", "message" => "New record created successfully!"]);
} else {
    $err = mysqli_stmt_error($stmt) ?: mysqli_error($conn);
    error_log("crud.php execute failed: $err");
    // return server error code so front-end sees 500 too
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $err,
        "stray_output" => $stray
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
