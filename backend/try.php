<?php
include 'db_connect.php';  // make sure this file connects to your MySQL and sets $conn

$sql = "CREATE TABLE IF NOT EXISTS survey_responses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  sleep VARCHAR(20) NOT NULL,
  cleanliness VARCHAR(20) NOT NULL,
  work VARCHAR(20) NOT NULL,
  social VARCHAR(20) NOT NULL,
  room VARCHAR(20) NOT NULL,
  diet VARCHAR(20) NOT NULL,
  pets VARCHAR(20) NOT NULL,
  noise VARCHAR(20) NOT NULL,
  needs TEXT NOT NULL,
  submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table survey_responses created successfully or already exists.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
