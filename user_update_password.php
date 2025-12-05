<?php
session_start();
include("db_conn.php");

$message = "";
$error = "";
$email = "";

// Check if email is passed via URL (forgot password flow)
if (isset($_GET['email'])) {
    $email = trim($_GET['email']);
    
    // Verify email exists in database - FIXED: Use prepared statement
    $check_sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    
    if (!$check_result || mysqli_num_rows($check_result) == 0) {
        $error = "Email not found in our system.";
        $email = "";
    }
    mysqli_stmt_close($stmt);
} 
// If no email in URL, check if user is logged in
elseif (isset($_SESSION['user_id'])) {
    // Get email from session user - FIXED: Use prepared statement
    $user_id = $_SESSION['user_id'];
    $get_email_sql = "SELECT email FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $get_email_sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $email_result = mysqli_stmt_get_result($stmt);
    
    if ($email_result && mysqli_num_rows($email_result) > 0) {
        $row = mysqli_fetch_assoc($email_result);
        $email = $row['email'];
    }
    mysqli_stmt_close($stmt);
} 
else {
    // No email and not logged in - redirect to login
    header("Location: login_form.php");
    exit();
}

// Handle password update form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['email'])) {
    $new_password     = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $post_email       = trim($_POST['email']);

    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // FIXED: Update password using prepared statement
        $update_sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "ss", $new_password, $post_email);
        $result = mysqli_stmt_execute($stmt);

        if ($result && mysqli_stmt_affected_rows($stmt) > 0) {
            mysqli_stmt_close($stmt);
            // FIXED: Clear session if user is logged in (force re-login with new password)
            if (isset($_SESSION['user_id'])) {
                session_destroy();
            }
            // Redirect to login with email pre-filled
            echo "<script>
                alert('Password updated successfully! Please login with your new password.');
                window.location.href = 'login_form.php?email=" . urlencode($post_email) . "';
            </script>";
            exit();
        } else {
            $error = "Failed to update password. Please try again.";
            mysqli_stmt_close($stmt);
        }
    }
    $email = $post_email; // Keep email in form
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-white p-6 rounded-lg shadow-lg w-[90%] max-w-md">
    <h1 class="text-2xl font-bold text-center text-blue-700 mb-4">Update Password</h1>

    <?php if ($error): ?>
      <p class="text-red-600 text-center mb-3 bg-red-50 p-2 rounded"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!empty($email)): ?>
    <form id="updateForm" method="post" action="" class="space-y-4">
      
      <!-- Email (readonly) -->
      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
               class="w-full border rounded p-2 bg-gray-100" readonly>
      </div>

      <!-- New Password -->
      <div>
        <label class="block text-sm font-medium">New Password</label>
        <input type="password" name="new_password" id="new_password" class="w-full border rounded p-2" required>
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-sm font-medium">Confirm New Password</label>
        <input type="password" name="confirm_password" class="w-full border rounded p-2" required>
      </div>

      <!-- Submit -->
      <div class="text-center">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition transform hover:scale-105">
          Update Password
        </button>
      </div>

      <p class="text-center text-sm mt-4">
        <a href="login_form.php" class="text-blue-600 hover:underline">← Back to Login</a>
      </p>
    </form>
    <?php else: ?>
      <p class="text-center text-gray-600 mb-4">Unable to process password reset.</p>
      <div class="text-center">
        <a href="login_form.php" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 inline-block">
          Go to Login
        </a>
      </div>
    <?php endif; ?>
  </div>

  <script>
    $(document).ready(function() {
      $("#updateForm").validate({
        rules: {
          new_password: { required: true, minlength: 6 },
          confirm_password: { required: true, equalTo: "#new_password" }
        },
        messages: {
          new_password: {
            required: "Enter a new password",
            minlength: "Password must be at least 6 characters"
          },
          confirm_password: {
            required: "Confirm your new password",
            equalTo: "Passwords do not match"
          }
        }
      });
    });
  </script>

</body>
</html>