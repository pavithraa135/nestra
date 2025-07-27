<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$port = '3308';
$dbname = 'nestra_db';

// Step 1: Connect to MySQL server (no DB selected yet)
$conn = new mysqli($host, $user, $pass, '', $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // echo "Database created or already exists.";
} else {
    die("Error creating database: " . $conn->error);
}

// Step 3: Select the database
$conn->select_db($dbname);

// Optional: Set character set
$conn->set_charset("utf8");

?>

