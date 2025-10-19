<?php
require_once('conn.php');
header('Content-Type: application/json');

$results = [];

$province_id = isset($_GET['province_id']) ? intval($_GET['province_id']) : null;
$city_id = isset($_GET['city_id']) ? intval($_GET['city_id']) : null;
$city_name = isset($_GET['city_name']) ? trim($_GET['city_name']) : null;

// Primary: read from ph_cities (city_id, city_name, zip_code, provinces_id)
$queryParts = [];
if ($city_id) {
    $queryParts[] = "city_id = $city_id";
}
if ($province_id) {
    $queryParts[] = "provinces_id = $province_id";
}
if ($city_name) {
    $city_name_esc = mysqli_real_escape_string($conn, $city_name);
    $queryParts[] = "city_name = '$city_name_esc'";
}

$query = "SELECT city_id, city_name, zip_code FROM ph_cities";
if (!empty($queryParts)) {
    $query .= ' WHERE ' . implode(' AND ', $queryParts);
}
$query .= " ORDER BY city_name ASC";

try {
    $res = mysqli_query($conn, $query);
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $results[] = [
                'id' => $row['city_id'],
                'name' => $row['city_name'],
                'zip' => isset($row['zip_code']) ? $row['zip_code'] : ''
            ];
        }
    }
} catch (\mysqli_sql_exception $ex) {
    error_log('get_cities.php primary query error: ' . $ex->getMessage());
}

// Fallback: if no cities found in ph_cities, try distinct city values from persons
if (empty($results)) {
    $fallbackQuery = "SELECT DISTINCT city FROM persons WHERE city IS NOT NULL AND city <> '' ORDER BY city ASC";
    $res2 = @mysqli_query($conn, $fallbackQuery);
    if ($res2) {
        while ($row = mysqli_fetch_assoc($res2)) {
            $results[] = ['id' => $row['city'], 'name' => $row['city'], 'zip' => ''];
        }
    }
}

echo json_encode($results);
mysqli_close($conn);
?>
