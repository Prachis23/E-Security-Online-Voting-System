<?php
session_start();

$logout_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_logout'])) {
    session_destroy();
    $logout_message = "Logout successful!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <style>
        body {
            font-family: Arial;
            padding: 30px;
        }

        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border: 1px solid #c3e6cb;
            width: fit-content;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php if ($logout_message): ?>
    <div class="message"><?php echo $logout_message; ?></div>
<?php else: ?>
    <script>
        let confirmLogout = confirm("Are you sure you want to logout?");
        if (confirmLogout) {
            // Create a form and auto-submit it to trigger logout
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "";

            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "confirm_logout";
            input.value = "yes";
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        } else {
            // Go back to dashboard
            window.location.href = "admin/Dashboard.php";
        }
    </script>
<?php endif; ?>

</body>
</html>