 <?php
session_start();
include("../db_conn.php");
if (isset($_POST['login_check'])) {
    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);

    $sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$pass'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $_SESSION['admin_id'] = $row['id'];
    $_SESSION['admin_name'] = $row['name'];
    if($row) {
        echo "<script>alert('Login Successful'); window.location.href='admin_dashboard.php';</script>";
        exit;
    } else {
        echo "<script>alert('Invalid email or password'); window.location.href='admin_login.php';</script>";
        exit;
    }
}
?>

