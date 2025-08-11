<?php
// export_survey.php
include 'db_connect.php';

// Query survey data with user names
$query = "
    SELECT 
        sr.id,
        sr.user_id,
        u.fullname,
        sr.sleep,
        sr.cleanliness,
        sr.work,
        sr.social,
        sr.room,
        sr.diet,
        sr.pets,
        sr.noise,
        sr.needs,
        sr.submitted_at
    FROM survey_responses sr
    JOIN users u ON sr.user_id = u.id
";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// File path
$filename = __DIR__ . "/../ai/survey_data.csv";
$fp = fopen($filename, 'w');

// Write CSV header
$header = [
    'id',
    'user_id',
    'fullname',
    'sleep',
    'cleanliness',
    'work',
    'social',
    'room',
    'diet',
    'pets',
    'noise',
    'needs',
    'submitted_at'
];
fputcsv($fp, $header);

// Write rows
while ($row = $result->fetch_assoc()) {
    fputcsv($fp, $row);
}

fclose($fp);
$conn->close();

echo "✅ Survey data exported to ai/survey_data.csv successfully.";
?>
