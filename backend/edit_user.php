<?php
include 'db_connect.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $room = $_POST['room'];

    $conn->query("UPDATE users SET fullname='$fullname' WHERE id=$id");
    $conn->query("UPDATE survey SET room='$room' WHERE user_id=$id");

    echo "Updated";
} else {
    echo "ID missing";
}
?>
