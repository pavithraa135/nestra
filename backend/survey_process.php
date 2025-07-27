<?php
include 'db_connect.php';

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sleep = $_POST['sleep'];
    $cleanliness = $_POST['cleanliness'];
    $work = $_POST['work'];
    $social = $_POST['social'];
    $room = $_POST['room'];
    $needs = $_POST['needs'];

    // Assuming the user is logged in and session contains user_id
    session_start();
    if (!isset($_SESSION['user_id'])) {
        die("User not logged in. Please login first.");
    }
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO survey (user_id, sleep_schedule, cleanliness_level, work_style, social_pref, room_pref, needs) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $sleep, $cleanliness, $work, $social, $room, $needs);

    if ($stmt->execute()) {
        echo "✅ Survey submitted successfully.";
    } else {
        echo "❌ Error submitting survey: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
