<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("db_conn.php");

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_password     = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_id          = $_SESSION['user_id'];

    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {

        $sql = "UPDATE users SET password=$new_password WHERE id=$user_id";
        $result = mysqli_query($conn,$sql);

        if ($result) {
            $message = "✅ Password updated successfully.";
            // Optional: log user out after password change
            // session_destroy();
            // header("Location: login.php");
            // exit();
        } else {
            $error = "⚠️ Something went wrong. Please try again.";
        }

    }
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

    <?php if ($message): ?>
      <p class="text-green-600 text-center mb-3"><?php echo $message; ?></p>
    <?php elseif ($error): ?>
      <p class="text-red-600 text-center mb-3"><?php echo $error; ?></p>
    <?php endif; ?>

    <form id="updateForm" method="post" action="#" class="space-y-4">
      
      <!-- New Password -->
      <div>
        <label class="block text-sm font-medium">New Password</label>
        <input type="password" name="new_password" class="w-full border rounded p-2" required>
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
    </form>
  </div>

  <script>
    $(document).ready(function() {
      $("#updateForm").validate({
        rules: {
          new_password: { required: true, minlength: 6 },
          confirm_password: { required: true, equalTo: "[name='new_password']" }
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
