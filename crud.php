<?php
require_once('conn.php');

// Helper: escape string for SQL
function esc($conn, $val) {
    return mysqli_real_escape_string($conn, $val);
}

// Read incoming POST values (use null coalesce)
$input = [];
$keys = ['firstName','middleName','lastName','suffix','birthday','gender','nationality','home','country','region','province','city','barangay','zipcode'];
foreach ($keys as $k) {
    $input[$k] = isset($_POST[$k]) ? $_POST[$k] : null;
}

// Get list of columns in persons table so we can adapt inserts
$personCols = [];
$colRes = mysqli_query($conn, "DESCRIBE persons");
if ($colRes) {
    while ($c = mysqli_fetch_assoc($colRes)) {
        $personCols[] = $c['Field'];
    }
}

// Lookup table mapping for resolving ids -> names and preferred person columns
$lookups = [
    'nationality' => ['table'=>'nationalities','idCol'=>'ID','nameCol'=>'Nationality','personIdCol'=>'nationality_id','personNameCol'=>'nationality'],
    'country' => ['table'=>'countries','idCol'=>'id','nameCol'=>'country_name','personIdCol'=>'country_id','personNameCol'=>'country'],
    'region' => ['table'=>'ph_regions','idCol'=>'id','nameCol'=>'region_name','personIdCol'=>'region_id','personNameCol'=>'region'],
    'province' => ['table'=>'ph_provinces','idCol'=>'id','nameCol'=>'province_name','personIdCol'=>'province_id','personNameCol'=>'province'],
    'city' => ['table'=>'ph_cities','idCol'=>'city_id','nameCol'=>'city_name','personIdCol'=>'city_id','personNameCol'=>'city'],
    'barangay' => ['table'=>'ph_barangays','idCol'=>'id','nameCol'=>'name','personIdCol'=>'barangay_id','personNameCol'=>'barangay']
];

// Prepare arrays for insert
$insertCols = [];
$insertVals = [];

// helper to add column/value to insert arrays
function addInsert(&$cols, &$vals, $colName, $value, $isNumeric=false) {
    $cols[] = $colName;
    if ($isNumeric) $vals[] = intval($value);
    else $vals[] = "'" . $value . "'";
}

// Add basic fields (text)
$mapping = [
    'firstName' => 'first_name',
    'middleName' => 'middle_name',
    'lastName' => 'last_name',
    'suffix' => 'suffix_name',
    'birthday' => 'birthday',
    'gender' => 'gender',
    'home' => 'home'
];
foreach ($mapping as $k => $col) {
    if (in_array($col, $personCols)) {
        $val = $input[$k] !== null ? esc($conn, $input[$k]) : '';
        addInsert($insertCols, $insertVals, $col, $val, false);
    }
}

// Handle lookup fields (nationality, country, region, province, city, barangay, zipcode)
foreach ($lookups as $key => $info) {
    $posted = $input[$key];
    if ($posted === null) continue;

    $personIdCol = $info['personIdCol'];
    $personNameCol = $info['personNameCol'];

    // If posted is numeric, prefer to store id in persons if column exists
    if (is_numeric($posted)) {
        if (in_array($personIdCol, $personCols)) {
            addInsert($insertCols, $insertVals, $personIdCol, intval($posted), true);
            continue;
        }

        // else try to resolve name from lookup table and store name if possible
        $id = intval($posted);
        $t = $info['table']; $idCol = $info['idCol']; $nameCol = $info['nameCol'];
        $q = "SELECT `$nameCol` FROM `$t` WHERE `$idCol` = $id LIMIT 1";
        $r = mysqli_query($conn, $q);
        if ($r && mysqli_num_rows($r) > 0) {
            $row = mysqli_fetch_assoc($r);
            $resolvedName = $row[$nameCol];
            if (in_array($personNameCol, $personCols)) {
                addInsert($insertCols, $insertVals, $personNameCol, esc($conn, $resolvedName), false);
                continue;
            }
        }

        // fallback: store the numeric id into the name column if exists
        if (in_array($personNameCol, $personCols)) {
            addInsert($insertCols, $insertVals, $personNameCol, esc($conn, strval($posted)), false);
            continue;
        }
        // else skip
    } else {
        // posted is a name/string; store into personNameCol if exists
        if (in_array($personNameCol, $personCols)) {
            addInsert($insertCols, $insertVals, $personNameCol, esc($conn, $posted), false);
            continue;
        }

        // if personIdCol exists, try to resolve id by name from lookup table
        if (in_array($personIdCol, $personCols)) {
            $t = $info['table']; $idCol = $info['idCol']; $nameCol = $info['nameCol'];
            $nameEsc = esc($conn, $posted);
            $q = "SELECT `$idCol` FROM `$t` WHERE `$nameCol` = '$nameEsc' LIMIT 1";
            $r = mysqli_query($conn, $q);
            if ($r && mysqli_num_rows($r) > 0) {
                $row = mysqli_fetch_assoc($r);
                addInsert($insertCols, $insertVals, $personIdCol, intval($row[$idCol]), true);
                continue;
            }
        }

        // fallback: store the string in any available personNameCol
        if (in_array($personNameCol, $personCols)) {
            addInsert($insertCols, $insertVals, $personNameCol, esc($conn, $posted), false);
            continue;
        }
    }
}

// Ensure nationality column exists somewhere: if not handled above and persons has nationality, add empty
if (!in_array('nationality', $insertCols) && in_array('nationality', $personCols)) {
    $val = $input['nationality'] !== null ? esc($conn, $input['nationality']) : '';
    addInsert($insertCols, $insertVals, 'nationality', $val, false);
}

// Ensure zipcode column is stored as plain text if persons table has it
if (!in_array('zipcode', $insertCols) && in_array('zipcode', $personCols)) {
    $val = $input['zipcode'] !== null ? esc($conn, $input['zipcode']) : '';
    addInsert($insertCols, $insertVals, 'zipcode', $val, false);
}

// Always add created_at if present
if (in_array('created_at', $personCols)) {
    $insertCols[] = 'created_at';
    $insertVals[] = 'NOW()';
}

// Build SQL
if (count($insertCols) === 0) {
    echo json_encode(["status" => "error", "message" => "No valid columns to insert into persons."]);
    mysqli_close($conn);
    exit;
}

$colsSql = implode(', ', $insertCols);
$valsSql = implode(', ', array_map(function($v){ return is_numeric($v) ? $v : $v; }, $insertVals));

$sql = "INSERT INTO persons ($colsSql) VALUES ($valsSql)";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => "success",
        "message" => "New record created successfully!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
