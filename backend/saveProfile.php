<?php
include 'db_connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sleep = $conn->real_escape_string($_POST['sleep'] ?? '');
    $cleanliness = $conn->real_escape_string($_POST['cleanliness'] ?? '');
    $work = $conn->real_escape_string($_POST['work'] ?? '');
    $social = $conn->real_escape_string($_POST['social'] ?? '');
    $room = $conn->real_escape_string($_POST['room'] ?? '');
    $needs = $conn->real_escape_string($_POST['needs'] ?? '');


    $sql = "INSERT INTO survey_responses (sleep, cleanliness, work, social, room, needs)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssssss", $sleep, $cleanliness, $work, $social, $room, $needs);
        $success = $stmt->execute();

        if ($success) {
            echo "✅ Survey data saved successfully!";
        } else {
            echo "❌ Error saving data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "❌ Prepare failed: " . $conn->error;
    }

    $conn->close();
} else {
    echo "❌ Invalid request method.";
}
?>