<?php
header('Content-Type: application/json');
include 'db_connect.php';

// Ensure no unwanted output before JSON
ob_start();

$response = array();
$response['data'] = array();

try {
    $sql = "SELECT id, username, email, created_at FROM users ORDER BY id DESC";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        $response['data'][] = array(
            $row['id'],
            htmlspecialchars($row['username']),
            htmlspecialchars($row['email']),
            $row['created_at'],
            '<button class="editBtn" data-id="' . $row['id'] . '">Edit</button>
             <button class="deleteBtn" data-id="' . $row['id'] . '">Delete</button>'
        );
    }

    // Clear output buffer to avoid whitespace/errors
    ob_end_clean();
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(array("data" => [], "error" => $e->getMessage()));
}

$conn->close();
?>
