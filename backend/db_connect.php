<?php
$servername = "localhost";
$username = "root";         // Change if your DB uses a different username
$password = "";             // Set your MySQL password if any
$dbname = "nestra";         // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create DB if not exists
$sqlCreateDB = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sqlCreateDB) === TRUE) {
    echo "Database checked/created successfully.<br>";
} else {
    die("Database creation failed: " . $conn->error);
}

// Select DB
$conn->select_db($dbname);

// Create table if not exists
$sqlCreateTable = "
CREATE TABLE IF NOT EXISTS survey_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    sleep VARCHAR(50) NOT NULL,
    cleanliness VARCHAR(50) NOT NULL,
    work VARCHAR(50) NOT NULL,
    social VARCHAR(50) NOT NULL,
    room VARCHAR(50) NOT NULL,
    needs TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

if ($conn->query($sqlCreateTable) === TRUE) {
    echo "Table 'survey_responses' checked/created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
?>


