<?php
require_once('conn.php');
header('Content-Type: application/json');

$out = [];
$q = "SELECT id, country_name as name FROM countries ORDER BY country_name ASC";
$r = mysqli_query($conn, $q);
if ($r && mysqli_num_rows($r) > 0) {
    while ($row = mysqli_fetch_assoc($r)) {
        $out[] = ['id' => $row['id'], 'name' => $row['name']];
    }
} else {
    $r2 = mysqli_query($conn, "SELECT DISTINCT country as name FROM persons WHERE country IS NOT NULL AND country<>'' ORDER BY country ASC");
    if ($r2) {
        while ($row = mysqli_fetch_assoc($r2)) {
            $out[] = ['id' => $row['name'], 'name' => $row['name']];
        }
    }
}

echo json_encode($out);
mysqli_close($conn);
?>
