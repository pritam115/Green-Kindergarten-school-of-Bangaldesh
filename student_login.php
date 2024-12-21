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

    // Query to verify the student's name and password (assuming password is universal)
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

    // Parsing courses, instructors, and grades
    $courses = explode(',', $student_data['courses']); // Assuming courses are stored as comma-separated values
    $instructors = explode(',', $student_data['instructors']); // Assuming instructors are stored as comma-separated values
    $grades = explode(',', $student_data['grades']); // Assuming grades are stored as comma-separated values
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
            font-family:'Times New Roman', Times, serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .container h2, .container h1 {
            text-align: center;
        }
        .container input, .container button, .container a {
            display: block;
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
        .button-container {
            text-align: center;
            margin: 20px 0;
        }
        .button-container button {
            margin: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .section {
            display: none;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table th {
            background-color: #007BFF;
            color: white;
        }
        .profile-container p {
            margin: 10px 0;
        }
    </style>
    <script>
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
        }
    </script>
</head>
<body>
<div class="container">
    <?php if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true): ?>
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

        <div class="dashboard">
            <h1>Welcome, <?= htmlspecialchars($student_data['name']) ?></h1>

            <div class="button-container">
                <button onclick="showSection('profile')">Profile</button>
                <button onclick="showSection('courses')">Courses</button>
                <button onclick="showSection('grades')">Grades</button>
                <button onclick="showSection('billing')">Billing Information</button>
            </div>

        
            <div id="profile" class="section">
                <h2>Profile Details</h2>
                <p><strong>Name:</strong> <?= htmlspecialchars($student_data['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($student_data['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($student_data['phone']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($student_data['address']) ?></p>
                <p><strong>Father's Name:</strong> <?= htmlspecialchars($student_data['father_name']) ?></p>
                <p><strong>Mother's Name:</strong> <?= htmlspecialchars($student_data['mother_name']) ?></p>
                <p><strong>Father's Occupation:</strong> <?= htmlspecialchars($student_data['father_occupation']) ?></p>
                <p><strong>Hometown:</strong> <?= htmlspecialchars($student_data['hometown']) ?></p>
                <p><strong>Current City:</strong> <?= htmlspecialchars($student_data['current_city']) ?></p>
                <p><strong>Guardian Phone:</strong> <?= htmlspecialchars($student_data['guardian_phone']) ?></p>
                <p><strong>Religion:</strong> <?= htmlspecialchars($student_data['religion']) ?></p>
            </div>


            <div id="courses" class="section">
                <h2>Your Courses</h2>
                <table>
                    <tr>
                        <th>Course Name</th>
                        <th>Instructor</th>
                    </tr>
                    <?php
                    for ($i = 0; $i < count($courses); $i++):
                        $instructor = isset($instructors[$i]) ? $instructors[$i] : 'N/A';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($courses[$i]) ?></td>
                            <td><?= htmlspecialchars($instructor) ?></td>
                        </tr>
                    <?php endfor; ?>
                </table>
            </div>


            <div id="grades" class="section">
                <h2>Your Grades</h2>
                <table>
                    <tr>
                        <th>Course Name</th>
                        <th>Grade</th>
                    </tr>
                    <?php
                    for ($i = 0; $i < count($courses); $i++):
                        $grade = isset($grades[$i]) ? $grades[$i] : 'N/A';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($courses[$i]) ?></td>
                            <td><?= htmlspecialchars($grade) ?></td>
                        </tr>
                    <?php endfor; ?>
                </table>
            </div>


            <div id="billing" class="section">
                <h2>Billing Information</h2>
                <p><strong>Total Bill:</strong> $<?= htmlspecialchars($student_data['total_bill']) ?></p>
                <p><strong>Amount Paid:</strong> $<?= htmlspecialchars($student_data['amount_paid']) ?></p>
                <p><strong>Amount Remaining:</strong> $<?= htmlspecialchars($student_data['amount_to_pay']) ?></p>
            </div>

            <div class="logout">
                <a href="?logout=true" style="color: #28a745;">Logout</a>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
