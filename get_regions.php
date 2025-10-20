<?php
require_once('conn.php');
header('Content-Type: application/json');

$out = [];
$country_id = isset($_GET['country_id']) ? (int)$_GET['country_id'] : 0;

if ($country_id) {
    // Try canonical regions table first
    $q = "SELECT id, name FROM p_regions WHERE country_id = $country_id ORDER BY name ASC";
    $r = mysqli_query($conn, $q);
    if ($r && mysqli_num_rows($r) > 0) {
        while ($row = mysqli_fetch_assoc($r)) {
            $out[] = ['id' => $row['id'], 'name' => $row['name']];
        }
    } else {
        // fallback: distinct region values from persons
        $r2 = mysqli_query($conn, "SELECT DISTINCT region as name FROM persons WHERE region IS NOT NULL AND region<>'' ORDER BY region ASC");
        if ($r2) {
            while ($row = mysqli_fetch_assoc($r2)) {
                $out[] = ['id' => $row['name'], 'name' => $row['name']];
            }
        }
    }
}

echo json_encode($out);
mysqli_close($conn);
?>
