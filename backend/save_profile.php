<?php
/**
 * save_profile.php — Update fullname and email for logged-in user
 * POST: fullname, email
 */
include 'db_connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($fullname === '' || $email === '') {
    echo json_encode(['error' => 'Name and email are required']);
    exit;
}

$stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ? WHERE id = ?");
$stmt->bind_param("ssi", $fullname, $email, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
}
else {
    echo json_encode(['error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
