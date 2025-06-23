<?php
include 'db.php'; // Ensure correct database connection file

// Assume user_mobile is received dynamically (Replace with session/form input)
$user_mobile = "9130349145"; // Example mobile number

// Fetch voter details using mobile number
$sql = "SELECT * FROM voters WHERE mobile='$user_mobile'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $voter = $result->fetch_assoc();

    // Generate OTP and update it in the database
    $otp = rand(100000, 999999);
    $conn->query("UPDATE voters SET otp='$otp' WHERE mobile='$user_mobile'");
} else {
    echo "<p style='color:red;'>User not found! Please check your mobile number.</p>";
    exit; // Stop execution if voter is not found
}

// Fetch candidates list
$candidate_query = "SELECT * FROM candidate";
$candidate_result = $conn->query($candidate_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { display: flex; justify-content: space-between; padding: 20px; }
        .left-section { width: 40%; }
        .right-section { width: 50%; border-left: 2px solid #ccc; padding-left: 20px; }
        .info { font-size: 18px; margin-bottom: 10px; }
        .otp-form { display: flex; justify-content: center; margin-top: 20px; }
        input { padding: 10px; margin-right: 10px; }
        button { padding: 10px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Voter Details Section -->
        <div class="left-section">
            <h2>Welcome, <span style="color:green;"><?php echo htmlspecialchars($voter['full_name']); ?></span></h2>
            <p class="info"><strong>Mobile:</strong> <?php echo htmlspecialchars($voter['mobile']); ?></p>
            <p class="info"><strong>Address:</strong> <?php echo htmlspecialchars($voter['address']); ?></p>
            <p class="info"><strong>D.O.B:</strong> <?php echo htmlspecialchars($voter['dob']); ?></p>
            <p class="info"><strong>Status:</strong> <span style="color:red;"><?php echo isset($voter['status'])? htmlspecialchars($voter['status']): 'Not Available'; ?></span></p>
            <p style="color:blue;">Your OTP has been sent to your registered mobile number.</p>

            <form action="otp_verification.php" method="POST" class="otp-form">
                <input type="text" name="otp" placeholder="Enter OTP" required>
                <input type="hidden" name="voter_id" value="<?php echo htmlspecialchars($voter['id']); ?>">
                <button type="submit">Verify</button>
            </form>
        </div>

        <!-- Candidate List Section -->
        <div class="right-section">
            <h2>Candidate List</h2>
            <table>
                <tr>
                    <th>#</th>
                    <th>name</th>
                    <th>position</th>
                    <th>logo</th>
                </tr>
                <?php
                if ($candidate_result->num_rows > 0) {
                    $i = 1;
                    while ($row = $candidate_result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$i}</td>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td>" . htmlspecialchars($row['position']) . "</td>
                            <td> <img src="<?php echo 'PBL/'. htmlspecialchars($row['logo']); ?>" width='30' height='30' onerror="this.src='PBL/default.png';"> </td>
                        </tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='4' style='color:red;'>No candidate found!</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>