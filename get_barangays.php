<?php
require_once('conn.php');
header('Content-Type: application/json');

$results = [];

$city_id = isset($_GET['city_id']) ? intval($_GET['city_id']) : null;
$city_name = isset($_GET['city_name']) ? trim($_GET['city_name']) : null;

// Primary: ph_barangays table (id, name, city_id)
$queryParts = [];
if ($city_id) $queryParts[] = "city_id = $city_id";
if ($city_name) {
    $city_name_esc = mysqli_real_escape_string($conn, $city_name);
    $queryParts[] = "city_name = '$city_name_esc'";
}

$query = "SELECT id, name FROM ph_barangays";
if (!empty($queryParts)) $query .= ' WHERE ' . implode(' AND ', $queryParts);
$query .= " ORDER BY name ASC";

try {
    $res = mysqli_query($conn, $query);
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $results[] = ['id' => $row['id'], 'name' => $row['name']];
        }
    }
} catch (\mysqli_sql_exception $ex) {
    error_log('get_barangays.php ph_barangays error: ' . $ex->getMessage());
}

// Fallback: try legacy 'barangays' table
if (empty($results)) {
    $q2 = "SELECT id, name FROM barangays";
    if ($city_id) $q2 .= " WHERE city_id = $city_id";
    $q2 .= " ORDER BY name ASC";
    $r2 = @mysqli_query($conn, $q2);
    if ($r2 && mysqli_num_rows($r2) > 0) {
        while ($row = mysqli_fetch_assoc($r2)) $results[] = ['id'=>$row['id'],'name'=>$row['name']];
    }
}

// Final fallback: distinct barangay values from persons table
if (empty($results)) {
    $r3 = @mysqli_query($conn, "SELECT DISTINCT barangay FROM persons WHERE barangay IS NOT NULL AND barangay<>'' ORDER BY barangay ASC");
    if ($r3) while ($row = mysqli_fetch_assoc($r3)) $results[] = ['id'=>$row['barangay'],'name'=>$row['barangay']];
}

echo json_encode($results);
mysqli_close($conn);
?>
