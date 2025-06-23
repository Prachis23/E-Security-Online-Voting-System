<?php
session_start();
$conn = new mysqli("localhost", "root", "", "voting_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM voters WHERE mobile = ? AND password = ?");
    $stmt->bind_param("ss", $mobile, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['mobile'] = $mobile;
        echo "<script>alert('Login successful!'); window.location.href='voter_dashboard.php';</script>";
        exit();
    } else {
        echo "<script>alert('Invalid mobile number or password');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voter Login</title>
    <style>
        body {
            background: #f0f2f5;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 320px;
            text-align: center;
        }
        .login-box h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-box input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .login-box button {
            padding: 10px 20px;
            background: #007BFF;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-box button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Voter Login</h2>
        <form method="POST">
            <input type="text" name="mobile" placeholder="Mobile Number" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>