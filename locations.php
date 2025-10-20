<?php
header('Content-Type: application/json');

// Read JSON file
$jsonFile = 'locations.json';

// Check if file exists
if (!file_exists($jsonFile)) {
    echo json_encode(array("status" => "error", "message" => "locations.json file not found"));
    exit;
}

// Read and decode JSON
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

// Check if JSON is valid
if ($data === null) {
    echo json_encode(array("status" => "error", "message" => "Invalid JSON format"));
    exit;
}

// Get action from request
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle different actions
if ($action == "getRegions") {
    echo json_encode(array("status" => "success", "data" => $data['regions']));
    
} else if ($action == "getProvinces") {
    $region = isset($_GET['region']) ? $_GET['region'] : '';
    
    if (isset($data['provinces'][$region])) {
        echo json_encode(array("status" => "success", "data" => $data['provinces'][$region]));
    } else {
        echo json_encode(array("status" => "error", "message" => "Region not found"));
    }
    
} else if ($action == "getCities") {
    $province = isset($_GET['province']) ? $_GET['province'] : '';
    
    if (isset($data['cities'][$province])) {
        echo json_encode(array("status" => "success", "data" => $data['cities'][$province]));
    } else {
        echo json_encode(array("status" => "error", "message" => "Province not found"));
    }
    
} else if ($action == "getBarangays") {
    $city = isset($_GET['city']) ? $_GET['city'] : '';
    
    if (isset($data['barangays'][$city])) {
        echo json_encode(array("status" => "success", "data" => $data['barangays'][$city]));
    } else {
        echo json_encode(array("status" => "error", "message" => "No barangays available"));
    }
    
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid action"));
}
?>