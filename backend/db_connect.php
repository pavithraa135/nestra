<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$port = 3308; // Make sure MySQL is running on this port
$dbname = 'nestra_db';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: uncomment to check connection success
// echo "Connected successfully";
?>
