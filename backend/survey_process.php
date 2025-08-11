<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die(json_encode(['error' => 'Invalid request method']));
}

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not logged in']));
}

$user_id = $_SESSION['user_id'];

$sleep = $_POST['sleep'] ?? '';
$cleanliness = $_POST['cleanliness'] ?? '';
$work = $_POST['work'] ?? '';
$social = $_POST['social'] ?? '';
$room = $_POST['room'] ?? '';
$diet = $_POST['diet'] ?? '';
$pets = $_POST['pets'] ?? '';
$noise = $_POST['noise'] ?? '';
$needs = $_POST['needs'] ?? '';

// Insert survey response
$stmt = $conn->prepare("INSERT INTO survey_responses (user_id, sleep, cleanliness, work, social, room, diet, pets, noise, needs) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssssss", $user_id, $sleep, $cleanliness, $work, $social, $room, $diet, $pets, $noise, $needs);

if (!$stmt->execute()) {
    die(json_encode(['error' => 'Failed to save survey: ' . $stmt->error]));
}
$stmt->close();

// Fetch current user survey (just inserted)
$user_sql = "SELECT * FROM survey_responses WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$current_user = $user_result->fetch_assoc();
$user_stmt->close();

// Fetch all other users' surveys
$others_sql = "SELECT sr.*, u.fullname FROM survey_responses sr JOIN users u ON sr.user_id = u.id WHERE sr.user_id != ?";
$others_stmt = $conn->prepare($others_sql);
$others_stmt->bind_param("i", $user_id);
$others_stmt->execute();
$others_result = $others_stmt->get_result();

$matches = [];
$total_attributes = 8; // sleep, cleanliness, work, social, room, diet, pets, noise

while ($other = $others_result->fetch_assoc()) {
    $score = 0;

    $score += ($current_user['sleep'] === $other['sleep']) ? 1 : 0;
    $score += ($current_user['cleanliness'] === $other['cleanliness']) ? 1 : 0;
    $score += ($current_user['work'] === $other['work']) ? 1 : 0;
    $score += ($current_user['social'] === $other['social']) ? 1 : 0;
    $score += ($current_user['room'] === $other['room']) ? 1 : 0;
    $score += ($current_user['diet'] === $other['diet']) ? 1 : 0;
    $score += ($current_user['pets'] === $other['pets']) ? 1 : 0;
    $score += ($current_user['noise'] === $other['noise']) ? 1 : 0;

    $compatibility_percentage = ($score / $total_attributes) * 100;

    $matches[] = [
        'user_id' => $other['user_id'],
        'fullname' => $other['fullname'],
        'compatibility' => round($compatibility_percentage, 2),
        'needs' => $other['needs']
    ];
}

$others_stmt->close();
$conn->close();

usort($matches, function($a, $b) {
    return $b['compatibility'] <=> $a['compatibility'];
});

$response = [
    'message' => '✅ Survey recorded successfully.',
    'matches' => array_slice($matches, 0, 5)
];

header('Content-Type: application/json');
echo json_encode($response);
?>
