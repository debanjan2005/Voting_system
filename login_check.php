<?php
session_start();
include("db_conn.php");

if (isset($_POST['login_check'])) {
    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$pass'";
    $result = mysqli_query($conn, $sql);
    
    // ✅ Check if user exists FIRST, then set session
    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        echo "<script>alert('Login Successful'); window.location.href='dashboard.php';</script>";
        exit;
    } else {
        echo "<script>alert('Invalid email or password'); window.location.href='login_form.php';</script>";
        exit;
    }
}
?>