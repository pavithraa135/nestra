<?php
include 'db_connect.php';

$sql = "SELECT u.fullname AS name, s.sleep, s.cleanliness, s.work, s.social, s.room, s.needs
        FROM users u
        JOIN survey s ON u.id = s.user_id
        ORDER BY u.id DESC LIMIT 1";

$result = $conn->query($sql);
$data = [];

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
}

header('Content-Type: application/json');
echo json_encode($data);
?>
