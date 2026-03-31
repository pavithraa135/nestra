<?php
/**
 * get_profile.php
 * Returns current logged-in user's data (users + latest survey_responses)
 */
include 'db_connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Fetch user info
$stmt = $conn->prepare("SELECT id, fullname, email, username, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

// Fetch latest survey response
$stmt = $conn->prepare(
    "SELECT sleep, cleanliness, work, social, room, diet, pets, noise, needs, submitted_at
     FROM survey_responses WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$survey = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch best match compatibility from match engine result (just score, no heavy compute)
$match_score = null;
$match_name = null;
if ($survey) {
    $stmt = $conn->prepare(
        "SELECT u.fullname FROM survey_responses sr
         JOIN users u ON sr.user_id = u.id
         WHERE sr.user_id != ?
         ORDER BY sr.submitted_at DESC LIMIT 1"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row)
        $match_name = $row['fullname'];
}

$conn->close();

echo json_encode([
    'user' => $user,
    'survey' => $survey,
    'match_name' => $match_name,
]);
?>
