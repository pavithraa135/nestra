<?php
include 'db_connect.php'; // make sure this file connects $conn

// Read DataTables parameters
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$searchValue = $_POST['search']['value']; // Search value

## Search
$searchQuery = "";
if ($searchValue != '') {
    $searchValue = mysqli_real_escape_string($conn, $searchValue);
    $searchQuery = " WHERE fullname LIKE '%".$searchValue."%' OR email LIKE '%".$searchValue."%' OR username LIKE '%".$searchValue."%'";
}

## Total number of records without filtering
$sel = mysqli_query($conn, "SELECT COUNT(*) AS allcount FROM detailsdb");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($conn, "SELECT COUNT(*) AS allcount FROM detailsdb ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT id, fullname, email, username, created_at FROM detailsdb ".$searchQuery." ORDER BY id DESC LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $empQuery);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array(
        "id" => $row['id'],
        "fullname" => $row['fullname'],
        "email" => $row['email'],
        "username" => $row['username'],
        "created_at" => $row['created_at']
    );
}

## Response
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);
?>
