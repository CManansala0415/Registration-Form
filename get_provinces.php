<?php
require_once('conn.php');
header('Content-Type: application/json');

$out = [];
$region_id = isset($_GET['region_id']) ? (int)$_GET['region_id'] : 0;

if ($region_id) {
    // Try canonical provinces table first
    $q = "SELECT id, name FROM p_provinces WHERE region_id = $region_id ORDER BY name ASC";
    $r = mysqli_query($conn, $q);
    if ($r && mysqli_num_rows($r) > 0) {
        while ($row = mysqli_fetch_assoc($r)) {
            $out[] = ['id' => $row['id'], 'name' => $row['name']];
        }
    } else {
        // fallback: distinct province values from persons
        $r2 = mysqli_query($conn, "SELECT DISTINCT province as name FROM persons WHERE province IS NOT NULL AND province<>'' ORDER BY province ASC");
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
