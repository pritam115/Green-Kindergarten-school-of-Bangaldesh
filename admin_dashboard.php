<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php'); // Redirect to login page if not logged in
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'studentportal');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $courses = $_POST['courses'];
    $grades = $_POST['grades'];
    $billing_info = $_POST['billing_info'];

    // SQL query to insert student data
    $sql = "INSERT INTO students (name, email, phone, address, courses, grades, billing_info) 
            VALUES ('$name', '$email', '$phone', '$address', '$courses', '$grades', '$billing_info')";

    if ($conn->query($sql) === TRUE) {
        $message = "New student added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Update Student
if (isset($_GET['update_id'])) {
    $id = $_GET['update_id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_student'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $courses = $_POST['courses'];
        $grades = $_POST['grades'];
        $billing_info = $_POST['billing_info'];

        // Update query
        $sql = "UPDATE students SET name='$name', email='$email', phone='$phone', address='$address', 
                courses='$courses', grades='$grades', billing_info='$billing_info' WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            $message = "Student updated successfully!";
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $message = "Error: " . $conn->error;
        }
    }

    // Fetch student data for editing
    $sql = "SELECT * FROM students WHERE id = $id";
    $result = $conn->query($sql);
    $student = $result->fetch_assoc();
}

// Handle Delete Student
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // SQL query to delete the student
    $sql = "DELETE FROM students WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        $message = "Student deleted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch all students
$sql = "SELECT * FROM students";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }

        .form-container input,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        .message {
            color: green;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #007BFF;
            color: white;
        }

        .action-links {
            margin-top: 10px;
        }

        .action-links a {
            color: #007BFF;
            margin-right: 10px;
        }

        .back-button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Admin Dashboard</h1>

    <!-- Back to Previous Page or Homepage -->
    <a href="javascript:history.back()" class="back-button">Go Back</a> <!-- Go back to the previous page -->
    <!-- OR, link to the homepage (use your homepage URL) -->
    <!-- <a href="index.php" class="back-button">Go to Homepage</a> -->

    <?php if (isset($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <!-- Add Student Form -->
    <div class="form-container">
        <h2>Add Student</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Student Name" required>
            <input type="email" name="email" placeholder="Student Email" required>
            <input type="text" name="phone" placeholder="Phone Number">
            <textarea name="address" placeholder="Address"></textarea>
            <textarea name="courses" placeholder="Courses (comma-separated)"></textarea>
            <textarea name="grades" placeholder="Grades (comma-separated)"></textarea>
            <textarea name="billing_info" placeholder="Billing Information"></textarea>
            <button type="submit" name="add_student">Add Student</button>
        </form>
    </div>

    <!-- Update Student Form (if editing) -->
    <?php if (isset($student)): ?>
        <div class="form-container">
            <h2>Update Student</h2>
            <form method="POST">
                <input type="text" name="name" value="<?= $student['name'] ?>" required>
                <input type="email" name="email" value="<?= $student['email'] ?>" required>
                <input type="text" name="phone" value="<?= $student['phone'] ?>">
                <textarea name="address"><?= $student['address'] ?></textarea>
                <textarea name="courses"><?= $student['courses'] ?></textarea>
                <textarea name="grades"><?= $student['grades'] ?></textarea>
                <textarea name="billing_info"><?= $student['billing_info'] ?></textarea>
                <button type="submit" name="update_student">Update Student</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Students List -->
    <h2>Students List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Courses</th>
            <th>Grades</th>
            <th>Billing Info</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['phone'] ?></td>
                <td><?= $row['address'] ?></td>
                <td><?= $row['courses'] ?></td>
                <td><?= $row['grades'] ?></td>
                <td><?= $row['billing_info'] ?></td>
                <td>
                    <div class="action-links">
                        <a href="admin_dashboard.php?update_id=<?= $row['id'] ?>">Update</a> |
                        <a href="admin_dashboard.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
