<?php
header('Content-Type: application/json');
include 'db_connect.php';

// Example: Fetch AI match results (replace with actual AI model integration)
$sql = "SELECT name, compatibility_score, common_interests FROM matches ORDER BY compatibility_score DESC";
$result = $conn->query($sql);

$matches = [];
while ($row = $result->fetch_assoc()) {
    $matches[] = $row;
}

echo json_encode($matches);
$conn->close();
?>
