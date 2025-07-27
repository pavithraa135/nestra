<?php
// survey_process.php

require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sleep = $_POST['sleep'] ?? '';
    $cleanliness = $_POST['cleanliness'] ?? '';
    $work = $_POST['work'] ?? '';
    $social = $_POST['social'] ?? '';
    $room = $_POST['room'] ?? '';
    $needs = $_POST['needs'] ?? '';
    $username = $_POST['username'] ?? ''; // optional for login-linked surveys

    // Sanitize inputs
    $sleep = mysqli_real_escape_string($conn, $sleep);
    $cleanliness = mysqli_real_escape_string($conn, $cleanliness);
    $work = mysqli_real_escape_string($conn, $work);
    $social = mysqli_real_escape_string($conn, $social);
    $room = mysqli_real_escape_string($conn, $room);
    $needs = mysqli_real_escape_string($conn, $needs);
    $username = mysqli_real_escape_string($conn, $username);

    // Save to survey table
    $query = "INSERT INTO survey_responses (username, sleep, cleanliness, work, social, room, needs)
              VALUES ('$username', '$sleep', '$cleanliness', '$work', '$social', '$room', '$needs')";

    if (mysqli_query($conn, $query)) {
        // Optionally store survey_id or username in session
        session_start();
        $_SESSION['username'] = $username;

        // Redirect to AI matching or result page
        header("Location: ../frontend/match_result.html"); // or to matchRoommate.php if it's dynamic
        exit();
    } else {
        echo "Error saving survey: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
