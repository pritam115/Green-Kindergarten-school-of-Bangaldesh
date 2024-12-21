<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'studentportal');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle student login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Universal password
    $default_password = '1234';

    // Query to verify the student's name and password
    $sql = "SELECT * FROM students WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Verify the universal password
        if ($password === $default_password) {
            // Login successful
            $student = $result->fetch_assoc();
            $_SESSION['student_logged_in'] = true;
            $_SESSION['student_data'] = $student;
            header('Location: ?dashboard=true'); // Redirect to dashboard
            exit;
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Student not found!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ?'); // Redirect to login
    exit;
}

// Fetch student data if logged in
$student_data = null;
if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
    $student_data = $_SESSION['student_data'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
            text-align: center;
        }
        .container input, .container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .container button {
            background-color: #28a745;
            color: white;
            border: none;
        }
        .container button:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            text-align: center;
        }
        .logout {
            text-align: center;
            margin: 20px 0;
        }
        .dashboard h1 {
            text-align: center;
        }
        .student-details {
            margin-top: 20px;
        }
        .student-details p {
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <?php if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true): ?>
        <!-- Login Form -->
        <h2>Student Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter your name" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <button type="submit" name="login">Login</button>
        </form>
    <?php else: ?>
        <!-- Student Dashboard -->
        <div class="dashboard">
            <h1>Welcome, <?= htmlspecialchars($student_data['name']) ?></h1>
            <div class="student-details">
                <p><strong>Email:</strong> <?= htmlspecialchars($student_data['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($student_data['phone']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($student_data['address']) ?></p>
                <p><strong>Courses:</strong> <?= htmlspecialchars($student_data['courses']) ?></p>
                <p><strong>Grades:</strong> <?= htmlspecialchars($student_data['grades']) ?></p>
                <p><strong>Billing Information:</strong> <?= htmlspecialchars($student_data['billing_info']) ?></p>
            </div>
            <div class="logout">
                <a href="?logout=true" style="color: #28a745;">Logout</a>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
