<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Database connection
$servername = "sql113.infinityfree.com";
$username = "if0_38815857";
$password = "JQJ1sETBRQfOKPF";
$dbname = "if0_38815857_voting_db"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} 

// Handle login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['username'] = $username;
        header("Location:Dashboard.php"); // change to your actual dashboard path
        exit();
    } else {
        echo "<script>alert('Invalid Login!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f4f4f4;
            text-align: center;
        }
        .header {
            background-color: black;
            color: white;
            padding: 10px;
            font-size: 20px;
            text-align: center;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }
        .left {
            width: 50%;
            text-align: center;
        }
        .left img {
            width: 200px;
        }
        .right {
            width: 40%;
            background: white;
            padding: 30px;
            box-shadow: 0px 0px 10px gray;
            border-radius: 5px;
            text-align: center;
        }
        .right img {
            width: 80px;
        }
        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid gray;
            border-radius: 5px;
        }
        button {
            background: black;
            color: white;
            padding: 10px;
            width: 100%;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: darkgray;
        }
        .register {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="header">YOUR VOTE IS YOUR VOICE!</div>

<div class="container">
    <div class="left">
        <img src="modi_image.jpg" alt="Modi Image"> <!-- Replace with actual image path -->
        <p>"Our lives begin to end the day we become silent about things that matter."</p>
    </div>
    <div class="right">
        <h2>USER LOGIN</h2>
        <img src="user_icon.png" alt="User Icon"> <!-- Replace with actual image path -->
        <form action="login.php" method="POST"> <!-- Updated to point to same page -->
            <label>Full Name</label>
            <input type="text" name="username" placeholder="Enter Full Name" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>
        
    </div>
</div>

</body>
</html>