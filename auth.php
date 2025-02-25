<?php
session_start();
require 'config.php'; // Database connection

// Registration Logic
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $created_at = date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("ssss", $username, $email, $password, $created_at);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please login.'); window.location.href='login.html';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}



// Login Logic
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare SQL query
    $stmt = $conn->prepare("SELECT id, username, password_hash, status FROM users WHERE email = ?");
    
    if (!$stmt) {
        trigger_error("SQL Error: " . $conn->error, E_USER_ERROR);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $status);
        $stmt->fetch();

        if ($status !== 'active') {
            echo "<script>alert('Invalid Credentials or Blocked Account.'); window.location.href='login.html';</script>";
            exit();
        }

        if (password_verify($password, $hashed_password)) {
            session_regenerate_id(true); // Prevent session fixation attacks
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['visited_index'] = true; // Ensure they accessed the index page
            $_SESSION['LAST_ACTIVITY'] = time(); // Track last activity time

            echo "<script>alert('Login Successful!'); window.location.href='profile.php';</script>";
            exit();
        } else {
            echo "<script>alert('Invalid Password.'); window.location.href='login.html';</script>";
            exit();
        }
    } else {
        echo "<script>alert('User Not Found!'); window.location.href='login.html';</script>";
        exit();
    }
    
    $stmt->close();
}

?>
