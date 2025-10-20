<?php
header('Content-Type: application/json; charset=utf-8');
require_once('conn.php');

$action = $_GET['action'] ?? '';

if ($action === 'p_regions' && isset($_GET['country_id'])) {
    $country_id = (int)$_GET['country_id'];
    $sql = "SELECT id, name FROM p_regions WHERE country_id = $country_id ORDER BY name";
} elseif ($action === 'p_provinces' && isset($_GET['region_id'])) {
    $region_id = (int)$_GET['region_id'];
    $sql = "SELECT id, name FROM p_provinces WHERE region_id = $region_id ORDER BY name";
} elseif ($action === 'p_cities' && isset($_GET['province_id'])) {
    $province_id = (int)$_GET['province_id'];
    $sql = "SELECT id, name FROM p_cities WHERE province_id = $province_id ORDER BY name";
} elseif ($action === 'barangays' && isset($_GET['city_id'])) {
    $city_id = (int)$_GET['city_id'];
    $sql = "SELECT id, name FROM barangays WHERE city_id = $city_id ORDER BY name";
} else {
    echo json_encode([]);
    exit;
}

$res = mysqli_query($conn, $sql);
$out = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) $out[] = $row;
    mysqli_free_result($res);
}

echo json_encode($out);

?>
