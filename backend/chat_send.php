<?php
/**
 * chat_send.php — Save a message to the database
 * POST: receiver_id, message
 */
include 'db_connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$sender_id = intval($_SESSION['user_id']);
$receiver_id = intval($_POST['receiver_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if (!$receiver_id || $message === '') {
    echo json_encode(['error' => 'Missing receiver or message']);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO messages (sender_id, receiver_id, message, sent_at)
     VALUES (?, ?, ?, NOW())"
);
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
}
else {
    echo json_encode(['error' => $stmt->error]);
}
$stmt->close();
$conn->close();
?>
