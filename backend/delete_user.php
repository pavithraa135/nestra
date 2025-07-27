<?php
include 'db_connect.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $conn->query("DELETE FROM survey WHERE user_id=$id");
    $conn->query("DELETE FROM users WHERE id=$id");

    echo "Deleted";
} else {
    echo "ID missing";
}
?>
