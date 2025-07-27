<?php
// Sample logic based on preferences
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sleep       = $_POST['sleep'] ?? '';
    $cleanliness = $_POST['cleanliness'] ?? '';
    $work        = $_POST['work'] ?? '';
    $social      = $_POST['social'] ?? '';
    $room        = $_POST['room'] ?? '';
    $needs       = $_POST['needs'] ?? '';

    // Basic match logic (replace with AI/ML or real DB logic)
    $match = [
        "match_name" => "Neha Joshi",
        "compatibility" => "89%",
        "room" => "Room B21 – Calm Corner, 1st Floor",
        "reason" => "Similar schedule and room preferences"
    ];

    // You can refine this logic later with real matching algorithms
    if ($sleep === 'Night Owl' && $cleanliness === 'Very Clean' && $room === 'Twin Sharing') {
        $match = [
            "match_name" => "Priya Sharma",
            "compatibility" => "92%",
            "room" => "Room A12 – 2nd Floor, Quiet Wing",
            "reason" => "Both prefer quiet evenings, similar schedules, and organized space."
        ];
    }

    // Return as JSON
    echo json_encode([
        "status" => "success",
        "data" => $match
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
?>
