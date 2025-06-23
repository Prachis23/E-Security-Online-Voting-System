<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Voting System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
        }

        .left-panel {
            width: 50%;
            background-color: #f9f9f9;
            padding: 80px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .left-panel h1 {
            font-size: 40px;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .left-panel p {
            font-size: 18px;
            color: #555;
            margin-bottom: 40px;
            text-align: center;
            max-width: 400px;
        }

        .left-panel a {
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .left-panel a:hover {
            background-color: #218838;
        }

        .right-panel {
            width: 50%;
            background: url('vote.jpg') no-repeat center center;
            background-size: cover;
        }

        @media(max-width: 768px) {
            body {
                flex-direction: column;
            }
            .left-panel, .right-panel {
                width: 100%;
                height: 50%;
            }
        }
    </style>
</head>
<body>

    <div class="left-panel">
        <h1>Welcome to the Online Voting System</h1>
        <p>Your vote matters! Join in making decisions that count.Be the voice of change and shape the future. Click below to register and begin.</p>
        <a href="register.php">Register to Vote</a>
    </div>

    <div class="right-panel">
        <img src="vote.jpg" alt="vote Image">
    </div>

</body>
</html>
