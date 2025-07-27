<?php
include 'db_connect.php';

$sql = "SELECT u.id, u.fullname, s.sleep, s.cleanliness, s.work, s.social, s.room, s.needs 
        FROM users u 
        JOIN survey s ON u.id = s.user_id";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
