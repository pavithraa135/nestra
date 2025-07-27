<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sleep       = $_POST['sleep'] ?? '';
    $cleanliness = $_POST['cleanliness'] ?? '';
    $work        = $_POST['work'] ?? '';
    $social      = $_POST['social'] ?? '';
    $room        = $_POST['room'] ?? '';
    $needs       = $_POST['needs'] ?? '';

    $bestMatch = null;
    $highestScore = -1;

    // Fetch all users from survey table
    $sql = "SELECT * FROM survey_profiles";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $score = 0;

            if ($row['sleep'] === $sleep) $score += 2;
            if ($row['cleanliness'] === $cleanliness) $score += 2;
            if ($row['work'] === $work) $score += 2;
            if ($row['social'] === $social) $score += 2;
            if ($row['room'] === $room) $score += 2;

            // Basic match on needs text (if any keyword overlaps)
            if (!empty($needs) && strpos(strtolower($row['needs']), strtolower($needs)) !== false) {
                $score += 1;
            }

            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch = $row;
            }
        }

        if ($bestMatch) {
            echo json_encode([
                "status" => "success",
                "match_name" => $bestMatch['fullname'] ?? 'Unknown',
                "compatibility" => min(100, $highestScore * 10) . '%',
                "room" => 'Assigned Room: B12 (Based on preferences)',
                "reason" => "Matched on $highestScore out of 6 traits"
            ]);
        } else {
            echo json_encode([
                "status" => "fail",
                "message" => "No suitable match found."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "fail",
            "message" => "No users in survey database."
        ]);
    }

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>
