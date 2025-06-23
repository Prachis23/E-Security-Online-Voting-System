<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
    echo "Unauthorized";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'reset') {
        mysqli_query($conn, "UPDATE voters SET has_voted = 0");
        echo "Votes reset successfully";
    } elseif ($action == 'stop') {
        mysqli_query($conn, "UPDATE settings SET voting_enabled = 0"); // You'll need a settings table
        echo "Voting stopped";
    } elseif ($action == 'edit_title') {
        $newTitle = $_POST['title'];
        mysqli_query($conn, "UPDATE settings SET dashboard_title = '$newTitle'"); // Update your settings
        echo "Title updated";
    } else {
        echo "Invalid action";
    }
}
?>