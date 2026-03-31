<?php
/**
 * create_messages_table.php
 * Visit once in browser to create the messages table:
 * http://localhost:3308/nestra/backend/create_messages_table.php
 */
include 'db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    sender_id   INT NOT NULL,
    receiver_id INT NOT NULL,
    message     TEXT NOT NULL,
    sent_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_convo (sender_id, receiver_id),
    INDEX idx_sent  (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql)) {
    echo "✅ messages table created (or already exists).";
}
else {
    echo "❌ Error: " . $conn->error;
}
$conn->close();
?>
