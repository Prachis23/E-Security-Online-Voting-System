<?php
session_start();

// Handle Generate Code
if (isset($_POST['generate'])) {
    $_SESSION['verification_code'] = rand(1000, 9999);
    echo "<script>alert('Your code is: " . $_SESSION['verification_code'] . "');</script>";
}

// Handle Code Verification
$verified = false;
if (isset($_POST['submit_code'])) {
    $enteredCode = $_POST['code'] ?? '';
    if ($enteredCode == $_SESSION['verification_code']) {
        $verified = true;
        $_SESSION['verified'] = true;
    } else {
        echo "<h3 style='color:red;text-align:center;'>Incorrect Code. Try Again.</h3>";
    }
}

if (isset($_SESSION['verified']) && $_SESSION['verified']) {
    $verified = true;
}

// Initialize candidate list
if (!isset($_SESSION['candidates'])) {
    $_SESSION['candidates'] = [];
}

// Handle Add Candidate
if (isset($_POST['add_candidate'])) {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $logo = $_FILES['logo']['name'];
    $temp = $_FILES['logo']['tmp_name'];

    if ($logo != "") {
        if (!is_dir("uploads")) {
            mkdir("uploads");
        }
        move_uploaded_file($temp, "uploads/" . $logo);
    }

    $_SESSION['candidates'][] = [
        'name' => $name,
        'position' => $position,
        'logo' => $logo
    ];
}

// Handle Remove Candidate
if (isset($_POST['remove'])) {
    $index = $_POST['remove'];
    unset($_SESSION['candidates'][$index]);
    $_SESSION['candidates'] = array_values($_SESSION['candidates']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
        }
        form, table {
            width: 60%;
            margin: 20px auto;
        }
        input, button {
            padding: 8px;
            width: 100%;
            margin: 5px 0;
        }
        table {
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #000;
            text-align: center;
        }
        img {
            width: 50px;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>

<?php if (!$verified): ?>
    <h2 class="center">Admin Verification</h2>
    <form method="post">
        <button type="submit" name="generate">Generate Code</button>
    </form>

    <form method="post">
        <label>Enter Code:</label>
        <input type="text" name="code" required>
        <button type="submit" name="submit_code">Submit Code</button>
    </form>
<?php else: ?>
    <h2 class="center">Add Candidate</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Candidate Name:</label>
        <input type="text" name="name" required>

        <label>Position:</label>
        <input type="text" name="position" required>

        <label>Choose Logo:</label>
        <input type="file" name="logo" required>

        <button type="submit" name="add_candidate">Add Candidate</button>
    </form>

    <h2 class="center">Candidate List</h2>
    <table>
        <tr>
            <th>Sr No</th>
            <th>Name</th>
            <th>Position</th>
            <th>Logo</th>
            <th>Action</th>
        </tr>
        <?php foreach ($_SESSION['candidates'] as $index => $c): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['position']) ?></td>
                <td>
                    <?php if ($c['logo']): ?>
                        <img src="uploads/<?= $c['logo'] ?>">
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="remove" value="<?= $index ?>">Remove</button>
                    </form>
                    <button disabled>Change Logo</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</body>
</html>
