<?php
require_once('conn.php');
header('Content-Type: application/json');

$results = [];
$country_id = isset($_GET['country_id']) ? intval($_GET['country_id']) : null;

$query = "SELECT id, region_name, region_code FROM ph_regions";
if ($country_id) $query .= " WHERE country_id = $country_id";
$query .= " ORDER BY id ASC";

try {
    $res = mysqli_query($conn, $query);
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $results[] = ['id' => $row['id'], 'name' => $row['region_name'], 'code' => $row['region_code']];
        }
    }
} catch (\mysqli_sql_exception $ex) {
    error_log('get_regions.php primary query error: ' . $ex->getMessage());
}

if (empty($results)) {
    $r2 = @mysqli_query($conn, "SELECT DISTINCT region as name FROM persons WHERE region IS NOT NULL AND region<>'' ORDER BY region ASC");
    if ($r2) while ($row = mysqli_fetch_assoc($r2)) $results[] = ['id'=>$row['name'],'name'=>$row['name']];
}

echo json_encode($results);
mysqli_close($conn);
?>
