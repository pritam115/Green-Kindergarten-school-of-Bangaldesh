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
    $courses = implode(",", $_POST['courses']);
    $instructors = implode(",", $_POST['instructors']);
    $instructor_emails = implode(",", $_POST['instructor_emails']);
    $grades = implode(",", $_POST['grades']);
    $total_bill = $_POST['total_bill'];
    $amount_paid = $_POST['amount_paid'];
    $amount_to_pay = $total_bill - $amount_paid;
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $father_occupation = $_POST['father_occupation'];
    $hometown = $_POST['hometown'];
    $current_city = $_POST['current_city'];
    $guardian_phone = $_POST['guardian_phone'];
    $religion = $_POST['religion'];


    $sql = "INSERT INTO students (name, email, phone, address, courses, instructors, instructor_emails, grades, total_bill, 
            amount_paid, amount_to_pay, father_name, mother_name, father_occupation, hometown, current_city, guardian_phone, religion) 
            VALUES ('$name', '$email', '$phone', '$address', '$courses', '$instructors', '$instructor_emails', '$grades', 
                    '$total_bill', '$amount_paid', '$amount_to_pay', '$father_name', '$mother_name', '$father_occupation', 
                    '$hometown', '$current_city', '$guardian_phone', '$religion')";

    if ($conn->query($sql) === TRUE) {
        $message = "New student added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

if (isset($_GET['update_id'])) {
    $id = $_GET['update_id'];

    $sql = "SELECT * FROM students WHERE id = $id";
    $result = $conn->query($sql);
    $student = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_student'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $courses = implode(",", $_POST['courses']);
        $instructors = implode(",", $_POST['instructors']);
        $instructor_emails = implode(",", $_POST['instructor_emails']);
        $grades = implode(",", $_POST['grades']);
        $total_bill = $_POST['total_bill'];
        $amount_paid = $_POST['amount_paid'];
        $amount_to_pay = $total_bill - $amount_paid;
        $father_name = $_POST['father_name'];
        $mother_name = $_POST['mother_name'];
        $father_occupation = $_POST['father_occupation'];
        $hometown = $_POST['hometown'];
        $current_city = $_POST['current_city'];
        $guardian_phone = $_POST['guardian_phone'];
        $religion = $_POST['religion'];

        $sql = "UPDATE students SET name='$name', email='$email', phone='$phone', address='$address', 
                courses='$courses', instructors='$instructors', instructor_emails='$instructor_emails', grades='$grades', 
                total_bill='$total_bill', amount_paid='$amount_paid', amount_to_pay='$amount_to_pay', 
                father_name='$father_name', mother_name='$mother_name', father_occupation='$father_occupation', 
                hometown='$hometown', current_city='$current_city', guardian_phone='$guardian_phone', 
                religion='$religion' WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            $message = "Student updated successfully!";
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $sql = "DELETE FROM students WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        $message = "Student deleted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

$sql = "SELECT * FROM students";
$result = $conn->query($sql);

if ($result === false) {
    die("Error: " . $conn->error);
}

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
        font-family:'Times New Roman', Times, serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
    }
    .container {
        max-width: 1200px;
        margin: auto;
        padding: 20px;
        background: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    h1, h2 {
        text-align: center;
        color: #333;
    }
    .form-container {
        margin: 20px 0;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fafafa;
    }
    .form-container h2 {
        margin-bottom: 20px;
    }
    form input, form textarea, form button {
        display: block;
        width: 100%;
        margin-bottom: 15px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    form button {
        background-color: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
    }
    form button:hover {
        background-color: #0056b3;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    table th, table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    table th {
        background-color: #f4f4f9;
    }
    .message {
        color: green;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }
    .back-button {
        display: inline-block;
        margin-bottom: 15px;
        color: #007bff;
        text-decoration: none;
        font-size: 16px;
    }
    .back-button:hover {
        text-decoration: underline;
    }
    #course-container .course {
        margin-bottom: 10px;
        padding: 10px;
        border: 1px dashed #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
    #course-container .course input {
        width: calc(100% - 20px);
        margin-bottom: 5px;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Admin Dashboard</h1>
    <a href="javascript:history.back()" class="back-button">Go Back</a>

    <?php if (isset($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <div class="form-container">
        <h2>Add or Update Student</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Student Name" value="<?= isset($student) ? htmlspecialchars($student['name']) : '' ?>" required>
            <input type="email" name="email" placeholder="Student Email" value="<?= isset($student) ? htmlspecialchars($student['email']) : '' ?>" required>
            <input type="text" name="phone" placeholder="Phone Number" value="<?= isset($student) ? htmlspecialchars($student['phone']) : '' ?>" required>
            <textarea name="address" placeholder="Address" required><?= isset($student) ? htmlspecialchars($student['address']) : '' ?></textarea>
            <input type="text" name="father_name" placeholder="Father's Name" value="<?= isset($student) ? htmlspecialchars($student['father_name']) : '' ?>" required>
            <input type="text" name="mother_name" placeholder="Mother's Name" value="<?= isset($student) ? htmlspecialchars($student['mother_name']) : '' ?>" required>
            <input type="text" name="father_occupation" placeholder="Father's Occupation" value="<?= isset($student) ? htmlspecialchars($student['father_occupation']) : '' ?>" required>
            <input type="text" name="hometown" placeholder="Hometown" value="<?= isset($student) ? htmlspecialchars($student['hometown']) : '' ?>" required>
            <input type="text" name="current_city" placeholder="Current City" value="<?= isset($student) ? htmlspecialchars($student['current_city']) : '' ?>" required>
            <input type="text" name="guardian_phone" placeholder="Guardian Phone Number" value="<?= isset($student) ? htmlspecialchars($student['guardian_phone']) : '' ?>" required>
            <input type="text" name="religion" placeholder="Religion" value="<?= isset($student) ? htmlspecialchars($student['religion']) : '' ?>" required>

            <div id="course-container">
                <h3>Courses</h3>
                <?php
                $courses = isset($student) ? explode(",", $student['courses']) : [];
                $instructors = isset($student) ? explode(",", $student['instructors']) : [];
                $instructor_emails = isset($student) ? explode(",", $student['instructor_emails']) : [];
                $grades = isset($student) ? explode(",", $student['grades']) : [];
                for ($i = 0; $i < count($courses); $i++):
                ?>
                    <div class="course">
                        <input type="text" name="courses[]" placeholder="Course Name" value="<?= htmlspecialchars($courses[$i]) ?>">
                        <input type="text" name="instructors[]" placeholder="Instructor Name" value="<?= htmlspecialchars($instructors[$i]) ?>">
                        <input type="text" name="instructor_emails[]" placeholder="Instructor Email" value="<?= htmlspecialchars($instructor_emails[$i] ?? '') ?>">
                        <input type="text" name="grades[]" placeholder="Grade" value="<?= htmlspecialchars($grades[$i]) ?>">
                    </div>
                <?php endfor; ?>
            </div>

            <button type="button" onclick="addCourse()">Add More Courses</button>
            <input type="number" name="total_bill" placeholder="Total Bill" value="<?= isset($student) ? htmlspecialchars($student['total_bill']) : '' ?>" required>
            <input type="number" name="amount_paid" placeholder="Amount Paid" value="<?= isset($student) ? htmlspecialchars($student['amount_paid']) : '' ?>" required>
            <button type="submit" name="<?= isset($student) ? 'update_student' : 'add_student' ?>"><?= isset($student) ? 'Update Student' : 'Add Student' ?></button>
        </form>
    </div>

    <h2>All Students</h2>
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Father's Name</th>
            <th>Mother's Name</th>
            <th>Father's Occupation</th>
            <th>Hometown</th>
            <th>Current City</th>
            <th>Guardian Phone</th>
            <th>Religion</th>
            <th>Courses</th>
            <th>Total Bill</th>
            <th>Amount Paid</th>
            <th>Amount To Pay</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['father_name']) ?></td>
                <td><?= htmlspecialchars($row['mother_name']) ?></td>
                <td><?= htmlspecialchars($row['father_occupation']) ?></td>
                <td><?= htmlspecialchars($row['hometown']) ?></td>
                <td><?= htmlspecialchars($row['current_city']) ?></td>
                <td><?= htmlspecialchars($row['guardian_phone']) ?></td>
                <td><?= htmlspecialchars($row['religion']) ?></td>
                <td><?= htmlspecialchars($row['courses']) ?></td>
                <td><?= htmlspecialchars($row['total_bill']) ?></td>
                <td><?= htmlspecialchars($row['amount_paid']) ?></td>
                <td><?= htmlspecialchars($row['amount_to_pay']) ?></td>
                <td>
                    <a href="admin_dashboard.php?update_id=<?= $row['id'] ?>">Update</a>
                    <a href="admin_dashboard.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php
            endwhile;
        else:
            echo "<tr><td colspan='16'>No students found.</td></tr>";
        endif;
        ?>
        </tbody>
    </table>
</div>

<script>
    function addCourse() {
        const container = document.getElementById('course-container');
        const newCourse = document.createElement('div');
        newCourse.classList.add('course');
        newCourse.innerHTML = `
            <input type="text" name="courses[]" placeholder="Course Name">
            <input type="text" name="instructors[]" placeholder="Instructor Name">
            <input type="text" name="instructor_emails[]" placeholder="Instructor Email">
            <input type="text" name="grades[]" placeholder="Grade">
        `;
        container.appendChild(newCourse);
    }
</script>
</body>
</html>
