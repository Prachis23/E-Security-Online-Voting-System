<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$conn = new mysqli("sql113.infinityfree.com", "if0_38815857", "JQJ1sETBRQfOKPF", "if0_38815857_voting_db");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile']; // Corrected to match DB column
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $checkStmt = $conn->prepare("SELECT * FROM voters WHERE full_name = ?");
    $checkStmt->bind_param("s", $full_name);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows == 0) {
        echo "<script>alert('You are not eligible to register. Your name is not in the voter list.');</script>";
    } else {
   
        $sanitized_name = strtolower(explode(' ',$full_name)[0]); //correct format
        $expected_password = $sanitized_name . "@123";

        echo "<script>console.log('Sanitized Name: \"$sanitized_name\"');</script>";
        echo "<script>console.log('Expected Password: \"$expected_password\"');</script>";
        echo "<script>console.log('Entered Password: \"$password\"');</script>";

        if ($password !== $confirm_password) {
            echo "<script>alert('Passwords do not match!');</script>";
        } elseif ($password !== $expected_password) {
            echo "<script>alert('Password must be in format: name@123');</script>";
        } else {
            // Removed confirm_password from database update
            $updateStmt = $conn->prepare("UPDATE voters SET dob = ?, address = ?, email = ?, mobile = ?, password = ? WHERE full_name = ?");
            $updateStmt->bind_param("ssssss", $dob, $address, $email, $mobile, $password, $full_name);

            if ($updateStmt->execute()) {
                session_start();
                $_SESSION['mobile']=$mobile;
                echo "<script>alert('Registration successful! Go to the next page.'); window.location.href='voter_dashboard.php';</script>";
            } else {
                echo "Error: " . $updateStmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Voter Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 400px;
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 15px 0px #aaa;
            border-radius: 10px;
            text-align: center;
        }
        .header {
            background: linear-gradient(to right, orange, white, green);
            padding: 10px;
            border-radius: 10px 10px 0 0;
        }
        h2 {
            margin: 10px 0;
        }
        p.quote {
            font-weight: bold;
            margin: 5px 0 15px;
        }
        input {
            width: 90%;
            padding: 8px;
            margin: 6px 0;
        }
        button {
            width: 90%;
            padding: 10px;
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: darkgreen;
        }
        .login-link {
            margin-top: 10px;
            font-size: 14px;
        }
        .login-link a {
            color: blue;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <img src="https://upload.wikimedia.org/wikipedia/commons/1/17/Ashoka_Chakra.svg" alt="Flag" width="50">
    </div>
    <h2>Voter Registration Form</h2>
    <p class="quote">"Your Vote is Your Voice"</p>
    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required><br>
        <input type="date" name="dob" required><br>
        <input type="text" name="address" placeholder="Address" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="mobile" placeholder="Mobile Number" required><br> <!-- Fixed mobile field -->
        <input type="password" name="password" placeholder="Password (name@123)" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
        <button type="submit">Register</button>
        <p class="login-link">Already have an account? <a href="voter_login.php">Login Here</a></p>
    </form>
</div>

</body>
</html>