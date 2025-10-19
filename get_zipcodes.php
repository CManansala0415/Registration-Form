<?php
require_once('conn.php');
header('Content-Type: application/json');
$out = [];
$city_id = isset($_GET['city_id']) ? intval($_GET['city_id']) : null;
if ($city_id) {
  $q = "SELECT DISTINCT zip_code as z FROM ph_cities WHERE city_id = $city_id AND zip_code IS NOT NULL AND zip_code<>'' ORDER BY zip_code ASC";
  $r = @mysqli_query($conn, $q);
  if ($r && mysqli_num_rows($r) > 0) {
    while ($row = mysqli_fetch_assoc($r)) $out[] = ['id'=>$row['z'],'name'=>$row['z']];
  }
}
if (empty($out)) {
  $r2 = @mysqli_query($conn, "SELECT DISTINCT zipcode as z FROM persons WHERE zipcode IS NOT NULL AND zipcode<>'' ORDER BY zipcode ASC");
  if ($r2) while ($row = mysqli_fetch_assoc($r2)) $out[] = ['id'=>$row['z'],'name'=>$row['z']];
}
echo json_encode($out);
mysqli_close($conn);
?>
<?php
require_once('conn.php');
header('Content-Type: application/json');
$results = [];

$city_id = isset($_GET['city_id']) ? intval($_GET['city_id']) : null;
$city_name = isset($_GET['city_name']) ? trim($_GET['city_name']) : null;

// Prefer ph_cities.zip_code
try {
    if ($city_id) {
        $q = "SELECT DISTINCT zip_code FROM ph_cities WHERE city_id = $city_id AND zip_code IS NOT NULL AND zip_code <> '' ORDER BY zip_code ASC";
    } elseif ($city_name) {
        $city_name_esc = mysqli_real_escape_string($conn, $city_name);
        $q = "SELECT DISTINCT zip_code FROM ph_cities WHERE city_name = '$city_name_esc' AND zip_code IS NOT NULL AND zip_code <> '' ORDER BY zip_code ASC";
    } else {
        $q = null;
    }

    if ($q) {
        $res = mysqli_query($conn, $q);
        if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $results[] = ['id' => $row['zip_code'], 'name' => $row['zip_code']];
            }
        }
    }
} catch (\mysqli_sql_exception $ex) {
    error_log('get_zipcodes.php ph_cities error: ' . $ex->getMessage());
}

// Fallback to persons table
if (empty($results)) {
    try {
        $res2 = mysqli_query($conn, "SELECT DISTINCT zipcode FROM persons WHERE zipcode IS NOT NULL AND zipcode <> '' ORDER BY zipcode ASC");
        if ($res2) {
            while ($row = mysqli_fetch_assoc($res2)) {
                $results[] = ['id' => $row['zipcode'], 'name' => $row['zipcode']];
            }
        }
    } catch (\mysqli_sql_exception $ex) {
        error_log('get_zipcodes.php persons fallback error: ' . $ex->getMessage());
    }
}

echo json_encode($results);
mysqli_close($conn);
?>
