<?php
session_start();

// Include database connection
include('config.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to fetch the user based on the username
    $query = "SELECT id, username, password, role_id FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    // Check if the user exists
    if (mysqli_num_rows($result) > 0) {
        // Fetch user data
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['id'];
        $db_username = $row['username'];
        $db_password = $row['password'];
        $role_id = $row['role_id'];
        
        // Compare plain text password
        if ($password === $db_password) {
            // Password is correct, log the user in
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role_id'] = $role_id;

            // Redirect to appropriate dashboard
            if ($role_id == 1) {
                header("Location: admin_dashboard.php");
                exit();
            }
            else if ($role_id == 2) {
                header("Location: instructor_dashboard.php");
                exit();
            } 
            else if ($role_id == 3) {
                echo "help";
                header("Location: dashboard.php");
                exit();
            }  else {
                echo "Unauthorized access.";
            }
        } else {
            // Redirect back with an error if password is incorrect
            header("Location: login.php?error=invalid");
            exit();
        }
    } else {
        // Redirect back with an error if no user is found
        header("Location: login.php?error=invalid");
        exit();
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Internal CSS for styling -->
    <style>
        body {
            background: #f5f5f5;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>