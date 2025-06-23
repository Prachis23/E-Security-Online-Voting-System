<?php
$conn = mysqli_connect("localhost", "root", "", "voting_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Database connected successfully!";
}
?>