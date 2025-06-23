<?php 
session_start(); 
include '../db.php'; // Database connection

// Handle verification code generation
if (isset($_POST['generate_code'])) {
    $code = rand(1000, 9999);
    $_SESSION['verification_code'] = $code;
}

// Handle code verification
if (isset($_POST['verify_code'])) {
    $enteredCode = $_POST['user_code'];
    if (isset($_SESSION['verification_code']) && $enteredCode == $_SESSION['verification_code']) {
        unset($_SESSION['verification_code']); // clear code after successful verification
        header("Location: nextpage.php");
        exit;
    } else {
        $code_error = "Incorrect code! Please try again.";
    }
}

// Handle voting actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'start') {
        $_SESSION['voting_status'] = 'started';
    } elseif ($action == 'reset') {
        mysqli_query($conn, "UPDATE voters SET has_voted = 0, voted_for = NULL");
        $_SESSION['voting_status'] = 'not_started';
    } elseif ($action == 'edit' && isset($_POST['new_title'])) {
        $_SESSION['dashboard_title'] = $_POST['new_title'];
    }
}

// Fetch voters
$result = mysqli_query($conn, "SELECT * FROM voters");
$users = []; 
$votesDone = 0;
while ($row = mysqli_fetch_assoc($result)) { 
    $users[] = $row; 
    if (isset($row['has_voted']) && $row['has_voted'] == 1) { 
        $votesDone++; 
    } 
} 
$totalVotes = count($users);

$dashboard_title = $_SESSION['dashboard_title'] ?? "Admin Dashboard"; 
$voting_status = $_SESSION['voting_status'] ?? 'unknown';

$results_query = mysqli_query($conn, "SELECT voted_for, COUNT(*) as vote_count FROM voters WHERE has_voted = 1 GROUP BY voted_for");
$voteResults = [];
while ($row = mysqli_fetch_assoc($results_query)) {
    $voteResults[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voting Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        .container {
            width: 95%;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
        }
        h1 { color: #28a745; }
        h2 { font-size: 28px; margin: 10px 0; }
        .remaining { color: red; font-weight: bold; }
        table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ccc; }
        th { background: #eaeaea; }
        form { margin: 10px; display: inline-block; }
        input, button { padding: 10px; margin: 5px; }
        button { cursor: pointer; }
        .logout-btn {
            background: black;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
            border-radius: 5px;
        }
        .button-row button {
            padding: 12px 24px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .start-btn { background-color: green; color: white; }
        .reset-btn { background-color: yellow; color: black; }
        .edit-btn { background-color: lightblue; color: black; }
        .status-box {
            border: 2px solid green;
            margin: 20px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f9fff9;
        }
        #piechart {
            width: 700px;
            height: 400px;
            margin: auto;
        }
        .code-box {
            font-size: 28px;
            letter-spacing: 5px;
            background-color: #eee;
            padding: 10px;
            display: inline-block;
            border-radius: 8px;
            margin: 10px 0;
        }
        .error { color: red; font-weight: bold; }
    </style>

    <!-- Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Candidate', 'Votes'],
                <?php foreach ($voteResults as $row) {
                    $candidate = $row['voted_for'] ? $row['voted_for'] : 'Unknown';
                    echo "['{$candidate}', {$row['vote_count']}],";
                } ?>
            ]);
            var options = {
                title: 'Recent Voting Results',
                is3D: true,
                pieSliceText: 'label',
                backgroundColor: 'transparent'
            };
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>

<div class="button-row">
    <form method="post">
        <input type="hidden" name="action" value="start">
        <button class="start-btn" type="submit">Start</button>
    </form>
    <form method="post">
        <input type="hidden" name="action" value="reset">
        <button class="reset-btn" type="submit">Reset</button>
    </form>
    <form method="post">
        <input type="text" name="new_title" placeholder="New Dashboard Title" required>
        <input type="hidden" name="action" value="edit">
        <button class="edit-btn" type="submit">Edit Title</button>
    </form>
</div>

<div class="status-box">
    <h2>Voting Status</h2>
    <?php if ($voting_status == 'started'): ?>
        <p style="color: green;"><strong>Voting is Started.</strong></p>
    <?php elseif ($voting_status == 'not_started'): ?>
        <p style="color: red;"><strong>Voting is Not Started.</strong></p>
    <?php else: ?>
        <p>No Status available.</p>
    <?php endif; ?>
    <p><small>Soon data will be available when election process is started.</small></p>
    <p>
        Click on <span style="color: green;">Start </span> button to start election process.<br>
        Click on <span style="color: red;">Reset</span> button to reset election process.
    </p>
</div>

<!-- Candidate Management Section -->
<div class="container">
    <h2>Candidate Management</h2>

    <!-- Add Candidate Form -->
    <form method="post">
        <input type="text" name="candidate_name" placeholder="Enter Candidate Name" required>
        <button type="submit" name="add_candidate">Add Candidate</button>
    </form>

    <!-- Candidate List -->
    <h3>Existing Candidates</h3>
    <table>
        <tr><th>ID</th><th>Name</th><th>Action</th></tr>
        <?php
        // Handle Add Candidate
        if (isset($_POST['add_candidate']) && !empty($_POST['name'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            mysqli_query($conn, "INSERT INTO candidate (name,position,logo) VALUES ('$name',('$position'),('$logo')");
            header("Location: Dashboard.php");
            exit;
        }

        // Handle Remove Candidate
        if (isset($_GET['remove_candidate'])) {
            $id = intval($_GET['remove_candidate']);
            mysqli_query($conn, "DELETE FROM candidate WHERE id = $id");
            header("Location: Dashboard.php");
            exit;
        }

        // Fetch and display candidates
        $candidates = mysqli_query($conn, "SELECT * FROM candidate");
        while ($row = mysqli_fetch_assoc($candidates)):
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><a href="?remove_candidate=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to remove this candidate?');">Remove</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Voting Info -->
<div class="container">
    <h1><?php echo $dashboard_title; ?></h1>
    <h2>Votes Done: <?php echo $votesDone . '/' . $totalVotes; ?></h2>
    <p class="remaining">Remaining Votes: <?php echo ($totalVotes - $votesDone); ?></p>

    <h2>Voter List</h2>
    <table>
        <tr><th>Sr No</th><th>Name</th><th>Mobile</th></tr>
        <?php $sr = 1; foreach ($users as $user): ?>
            <tr><td><?= $sr++ ?></td><td><?= $user['full_name'] ?></td><td><?= $user['mobile'] ?></td></tr>
        <?php endforeach; ?>
    </table>

    <h2> Voted Voters (Full Info)</h2>
    <table>
        <tr><th>ID</th><th>Name</th><th>DOB</th><th>Email</th><th>Mobile</th><th>Voted Candidate ID</th></tr>
        <?php foreach ($users as $row): if ($row['has_voted'] == 1): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['full_name'] ?></td>
                <td><?= $row['dob'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['mobile'] ?></td>
                <td><?= $row['voted_for'] ?></td>
            </tr>
        <?php endif; endforeach; ?>
    </table>

    <h2>Recent Voting Results</h2>
    <table>
        <tr><th>Candidate</th><th>Total Votes</th></tr>
        <?php foreach ($voteResults as $res): ?>
            <tr><td><?= $res['voted_for'] ?: 'Unknown' ?></td><td><?= $res['vote_count'] ?></td></tr>
        <?php endforeach; ?>
    </table>

    <div id="piechart"></div>

    <a class="logout-btn" href="logout.php">Logout</a>
</div>

</body>
</html>