<?php

session_start();
$admins = [
    'kaziemad' => 'kaziemad',
    'saifulislam' => 'saifulislam',
    'tanpiatasnim' => 'tanpiatasnim',
    'kamrulhasan' => 'kamrulhasan',
    
];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

   
    if (isset($admins[$username]) && $admins[$username] === $password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username; 
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
            background-color: #f0f0f0;
            background-image: url('background.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }

        .login-container {
            text-align: center;
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        .login-container h1 {
            margin-bottom: 20px;
        }

        .login-container label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
        }

        .login-container input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

    </style>
</head>

<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (isset($error)): ?>
            <p class="error"> <?= $error ?> </p>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>