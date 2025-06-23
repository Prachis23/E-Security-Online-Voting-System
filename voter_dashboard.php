<?php
session_start();
include('db.php'); // Ensure your database connection file is included

// Fetch voter details
$mobile = $_SESSION['mobile']; // Assuming it's stored during login, Change this to dynamic input from login
$stmt = $conn->prepare("SELECT * FROM voters WHERE mobile = ?");
$query="select * from voters where mobile ='$mobile'";
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();
$voter = $result->fetch_assoc();
$result = mysqli_query($conn, $query);
$voter = mysqli_fetch_assoc($result);

// Generate OTP if requested
if (isset($_POST['generate_otp'])) {
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_verified'] = false; // Reset OTP verification status
    mysqli_query($conn, "UPDATE voters SET otp = '$otp' WHERE mobile = '$mobile'");
    echo "<script>alert('OTP sent: $otp');</script>"; // Remove this in production
}

// Verify OTP
if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp_entered'];
    if ($entered_otp == $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true;
        echo "<script>alert('OTP Verified! You can now vote.');</script>";
    } else {
        echo "<script>alert('Incorrect OTP!');</script>";
    }
}

// Fetch candidates
$candidateQuery = "SELECT * FROM candidate";
$candidateResult = mysqli_query($conn, $candidateQuery);

// Handle voting securely
if (isset($_GET['candidate_id']) && $_SESSION['otp_verified'] && !$voter['has_voted']) {
    $candidate_id = $_GET['candidate_id'];
    $voter_id = $voter['id'];
    mysqli_query($conn, "UPDATE voters SET has_voted = 1, voted_for = '$candidate_id' WHERE id = '$voter_id'");
    mysqli_query($conn, "UPDATE candidate SET votes = votes + 1 WHERE id = '$candidate_id'");
    
    $_SESSION['vote_success'] = true; // Store success message session
    unset($_SESSION['otp_verified']); // Reset OTP verification
    header("Location: voter_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .voter-info { background: #f8f9fa; padding: 20px; border-radius: 10px; }
        .candidate-table th, .candidate-table td { text-align: center; }
        .otp-section { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4 voter-info">
                <h4>Voter Information</h4>
                <p><strong>Name:</strong> <?php echo $voter['full_name']; ?></p>
                <p><strong>Mobile:</strong> <?php echo $voter['mobile']; ?></p>
                <p><strong>Address:</strong> <?php echo $voter['address']; ?></p>
                <p><strong>DOB:</strong> <?php echo $voter['dob']; ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge <?php echo $voter['has_voted'] ? 'badge-success' : 'badge-warning'; ?>">
                        <?php echo $voter['has_voted'] ? 'voted' : 'not voted'; ?>
                    </span>
                </p>
                <form method="POST">
                    <button type="submit" name="generate_otp" class="btn btn-primary">Generate OTP</button>
                </form>
            </div>
            <div class="col-md-8">
                <h4>Candidate List</h4>
                <table class="table table-bordered candidate-table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Sr No.</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Symbol</th>
                            <th>Vote</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($candidate = mysqli_fetch_assoc($candidateResult)) { ?>
                        <tr>
                            <td><?php echo $candidate['id']; ?></td>
                            <td><?php echo $candidate['name']; ?></td>
                            <td><?php echo $candidate['position']; ?></td>
                            <td><img src="<?php echo $candidate['logo']; ?>" width="40" height="40"></td>
                            <td>
                                <?php if (!$voter['has_voted'] && $_SESSION['otp_verified']) { ?>
                                    <a href="?candidate_id=<?php echo $candidate['id']; ?>" class="btn btn-success">Vote</a>
                                <?php } else { ?>
                                    <button class="btn btn-secondary" disabled>Vote</button>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="otp-section">
                    <h5>Verify OTP</h5>
                    <form method="POST">
                        <input type="text" name="otp_entered" class="form-control mb-2" placeholder="Enter OTP" required>
                        <button type="submit" name="verify_otp" class="btn btn-success">Verify</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['vote_success'])) { ?>
        <script>
            alert("Vote successfully, thank you for your vote!"); window.location.href='thankyou.php';</script>";
        </script>
        <?php unset($_SESSION['vote_success']); ?>
    <?php } ?>
</body>
</html>