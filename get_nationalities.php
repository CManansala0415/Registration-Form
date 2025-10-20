<?php
require_once('conn.php');
header('Content-Type: application/json');

$out = [];
// Try the canonical nationalities table first (uses nationality_id, nationality_name)
$q = "SELECT nationality_id as id, nationality_name as name FROM nationalities ORDER BY nationality_name ASC";
$r = mysqli_query($conn, $q);
if ($r && mysqli_num_rows($r) > 0) {
    while ($row = mysqli_fetch_assoc($r)) {
        $out[] = ['id' => $row['id'], 'name' => $row['name']];
    }
} else {
    $r2 = mysqli_query($conn, "SELECT DISTINCT nationality as name FROM persons WHERE nationality IS NOT NULL AND nationality<>'' ORDER BY nationality ASC");
    if ($r2) {
        while ($row = mysqli_fetch_assoc($r2)) {
            $out[] = ['id' => $row['name'], 'name' => $row['name']];
        }
    }
}

echo json_encode($out);
mysqli_close($conn);
?>
