<?php
session_start();
include("../db.php"); // Correct path to db.php from admin folder

// Redirect if not verified
if (!isset($_SESSION['code_verified']) || $_SESSION['code_verified'] !== true) {
    header("Location:Dashboard.php");
    exit;
}

// Handle Add Candidate
if (isset($_POST['add_candidate'])) {
    $name = $_POST['name'];
    $position = $_POST['position'];

    // Check for duplicate name
    $check_query = "SELECT * FROM candidates WHERE name = '$name'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Candidate with this name already exists!";
    } else {
        // Handle logo upload
        $logo = '';
        if (!empty($_FILES['logo']['name'])) {
            $target_dir = "../upload/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $logo = $target_dir . basename($_FILES["logo"]["name"]);
            move_uploaded_file($_FILES["logo"]["tmp_name"], $logo);
        }

        // Insert candidate
        $query = "INSERT INTO candidates (name, position, logo) VALUES ('$name', '$position', '$logo')";
        mysqli_query($conn, $query);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Remove logo file
    $get = mysqli_query($conn, "SELECT logo FROM candidates WHERE id=$id");
    $data = mysqli_fetch_assoc($get);
    if (file_exists($data['logo'])) {
        unlink($data['logo']);
    }

    mysqli_query($conn, "DELETE FROM candidates WHERE id = $id");
    header("Location: nextpage.php");
    exit;
}

// Fetch candidates
$result = mysqli_query($conn, "SELECT * FROM candidates");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial;
            background: #f0f0f0;
            margin: 0;
        }
        .header {
            background: black;
            color: white;
            padding: 20px;
            font-size: 20px;
        }
        .logout {
            float: right;
            background: red;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
        }
        .container {
            padding: 30px;
        }
        input, select {
            padding: 10px;
            margin: 5px;
            width: 200px;
        }
        .add-btn {
            background: green;
            color: white;
            padding: 10px 20px;
            border: none;
            margin-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table, th, td {
            border: 1px solid gray;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        .remove-btn {
            background: red;
            color: white;
            padding: 5px 12px;
            border: none;
            cursor: pointer;
        }
        img {
            width: 60px;
            height: 60px;
            object-fit: cover;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    Voting | Admin Dashboard [Welcome - <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?>]
    <a class="logout" href="logout.php">Logout</a>
</div>

<div class="container">
    <h2>Add Candidate</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Candidate Name" required>
        <input type="text" name="position" placeholder="Position" required>
        <input type="file" name="logo" accept="image/*">
        <button class="add-btn" type="submit" name="add_candidate">+ Add</button>
    </form>

    <h2>Candidate List</h2>
    <table>
        <tr>
            <th>Sr No</th>
            <th>Name</th>
            <th>Position</th>
            <th>Logo</th>
            <th>Action</th>
        </tr>
        <?php
        $i = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>{$i}</td>
                <td>{$row['name']}</td>
                <td>{$row['position']}</td>
                <td><img src='../upload/" . basename($row['logo']) . "' alt='logo'></td>
                <td><a href='nextpage.php?delete={$row['id']}'><button class='remove-btn'>REMOVE</button></a></td>
            </tr>";
            $i++;
        }
        ?>
    </table>
</div>

</body>
</html>