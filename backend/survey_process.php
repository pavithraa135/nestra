<?php
/**
 * survey_process.php
 * Saves survey → runs AI match → redirects to match_result.html
 */
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../frontend/survey.html");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html?error=not_logged_in");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$sleep = trim($_POST['sleep'] ?? '');
$cleanliness = trim($_POST['cleanliness'] ?? '');
$work = trim($_POST['work'] ?? '');
$social = trim($_POST['social'] ?? '');
$room = trim($_POST['room'] ?? '');
$diet = trim($_POST['diet'] ?? '');
$pets = trim($_POST['pets'] ?? '');
$noise = trim($_POST['noise'] ?? '');
$needs = trim($_POST['needs'] ?? '');

// ── Upsert: delete old survey for this user & insert fresh ────────────────
$del = $conn->prepare("DELETE FROM survey_responses WHERE user_id = ?");
$del->bind_param("i", $user_id);
$del->execute();
$del->close();

$stmt = $conn->prepare(
    "INSERT INTO survey_responses (user_id, sleep, cleanliness, work, social, room, diet, pets, noise, needs)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("isssssssss", $user_id, $sleep, $cleanliness, $work, $social, $room, $diet, $pets, $noise, $needs);

if (!$stmt->execute()) {
    die("Survey save failed: " . $stmt->error);
}
$stmt->close();
$conn->close();

// ── Redirect to match result page (the page will call match_engine.php) ───
header("Location: ../frontend/match_result.html?status=done");
exit;
?>
