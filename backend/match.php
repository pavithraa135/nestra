<?php
include 'db_connect.php';
session_start();

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "User not logged in."]));
}

$current_user_id = $_SESSION['user_id'];

// Get the current user's cluster
$sql = "SELECT cluster_id FROM user_clusters WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(["error" => "No cluster found for this user."]));
}

$current_cluster = $result->fetch_assoc()['cluster_id'];
$stmt->close();

// Fetch matches in the same cluster, excluding current user
$sql = "SELECT u.id AS user_id, u.fullname, sr.needs
        FROM user_clusters uc
        JOIN users u ON uc.user_id = u.id
        JOIN survey_responses sr ON u.id = sr.user_id
        WHERE uc.cluster_id = ? AND uc.user_id != ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $current_cluster, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$matches = [];
while ($row = $result->fetch_assoc()) {
    $matches[] = $row;
}

$stmt->close();
$conn->close();

// Return matches as JSON
header('Content-Type: application/json');
echo json_encode($matches);
?>
