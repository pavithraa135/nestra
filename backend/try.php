<?php
include 'db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS survey (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  sleep_schedule VARCHAR(50),
  cleanliness_level VARCHAR(50),
  work_style VARCHAR(50),
  social_pref VARCHAR(50),
  room_pref VARCHAR(50),
  needs TEXT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Survey table created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
