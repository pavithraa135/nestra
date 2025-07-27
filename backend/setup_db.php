<?php
$host = "localhost";
$user = "root";
$pass = "";
$port = 3308;
$dbname = "nestra";

// Connect to MySQL without selecting DB yet
$conn = new mysqli($host, $user, $pass, "", $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "✔️ Database '$dbname' created or already exists.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

$conn->close();

// Now connect using db_connect.php
include 'db_connect.php';

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    email VARCHAR(100),
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql) ? print("✔️ 'users' table ready.<br>") : print("❌ Error: " . $conn->error);

// Create survey table
$sql = "CREATE TABLE IF NOT EXISTS survey (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    sleep_schedule VARCHAR(50),
    cleanliness_level VARCHAR(50),
    work_style VARCHAR(50),
    social_pref VARCHAR(50),
    room_pref VARCHAR(50),
    needs TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($sql) ? print("✔️ 'survey' table ready.<br>") : print("❌ Error: " . $conn->error);

// Create matches table
$sql = "CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    match_id INT,
    compatibility INT,
    room_suggestion VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($sql) ? print("✔️ 'matches' table ready.<br>") : print("❌ Error: " . $conn->error);

// Insert sample user only if not exists
$username = 'priya';
$check = $conn->query("SELECT id FROM users WHERE username = '$username'");
if ($check->num_rows === 0) {
    $fullname = "Priya Sharma";
    $email = "priya@example.com";
    $password = password_hash("test123", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $username, $password);
    $stmt->execute() ? print("✔️ Sample user 'priya' inserted.<br>") : print("❌ Error: " . $stmt->error);
    $stmt->close();
} else {
    echo "ℹ️ Sample user 'priya' already exists.<br>";
}

$conn->close();
?>
