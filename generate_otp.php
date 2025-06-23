<?php
session_start();
include 'db.php';  // Your database connection file

if (!isset($_SESSION['voter_id'])) {
    echo "Voter not logged in.";
    exit;
}

$voter_id = $_SESSION['voter_id'];

// Fetch voter's mobile number
$sql = "SELECT mobile FROM voters WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $voter_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $mobile = $row['mobile'];
} else {
    echo "Voter not found.";
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;

// Send OTP via SMS (using any SMS Gateway API like Fast2SMS, Twilio, TextLocal etc.)
// Example dummy SMS API (you will replace this with actual API)
$sms_message = "Your voting OTP is $otp. Please enter it to proceed.";

// For real case, replace this with actual SMS API call.
$api_url = "https://www.fast2sms.com/dev/bulkV2?authorization=YOUR_API_KEY&route=otp&variables_values=$otp&flash=0&numbers=$mobile";

// Uncomment this in real project
// file_get_contents($api_url);

echo "<script>alert('OTP has been sent to your registered mobile number: $mobile'); window.location='dashboard.php';</script>";
?>