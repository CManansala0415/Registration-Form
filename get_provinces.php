<?php
require_once('conn.php');
header('Content-Type: application/json');

$results = [];

$country_id = isset($_GET['country_id']) ? intval($_GET['country_id']) : null;
$region_id = isset($_GET['region_id']) ? intval($_GET['region_id']) : null;
$region_code = isset($_GET['region_code']) ? trim($_GET['region_code']) : null;

// If region_code supplied, try to resolve to region_id
if ($region_code && !$region_id) {
    $rc = mysqli_real_escape_string($conn, $region_code);
    if ($rc !== '') {
        $rres = @mysqli_query($conn, "SELECT id FROM ph_regions WHERE region_code = '$rc' LIMIT 1");
        if ($rres && mysqli_num_rows($rres) > 0) {
            $rrow = mysqli_fetch_assoc($rres);
            $region_id = intval($rrow['id']);
        }
    }
}

// Build query for ph_provinces
$query = "SELECT id, province_name FROM ph_provinces";
$conds = [];
if ($country_id) $conds[] = "country_id = " . intval($country_id);
if ($region_id) $conds[] = "region_id = " . intval($region_id);
if (!empty($conds)) $query .= ' WHERE ' . implode(' AND ', $conds);
$query .= " ORDER BY id ASC";

$res = @mysqli_query($conn, $query);
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $results[] = ['id' => $row['id'], 'name' => $row['province_name']];
    }
}

// Fallback: distinct provinces from persons table
if (empty($results)) {
    $res2 = @mysqli_query($conn, "SELECT DISTINCT province FROM persons WHERE province IS NOT NULL AND province <> '' ORDER BY province ASC");
    if ($res2) {
        while ($row = mysqli_fetch_assoc($res2)) {
            $results[] = ['id' => $row['province'], 'name' => $row['province']];
        }
    }
}

echo json_encode($results);
mysqli_close($conn);
?>
