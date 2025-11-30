<?php
session_start();
include("db_conn.php");

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch existing user data
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "<script>alert('User not found'); window.location.href='dashboard.php';</script>";
    exit;
}

// ✅ Handle form submission
if (isset($_POST['update_data'])) {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $village = $_POST['village'];
    $po = $_POST['po'];
    $ps = $_POST['ps'];
    $district = $_POST['district'];
    $state = $_POST['state'];
    $pin = $_POST['pincode'];

    $update_sql = "UPDATE users 
                   SET name='$name', dob='$dob', gender='$gender', phone='$phone',
                       village='$village', post_office='$po', police_station='$ps',
                       districe='$district', state='$state', pin_code='$pin'
                   WHERE id='$user_id'";

    $update_result = mysqli_query($conn, $update_sql);

    if ($update_result) {
        echo "<script>alert('Profile Updated Successfully'); window.location.href='dashboard.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating profile');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100 min-h-screen">

  <!-- ✅ Navbar (same as dashboard) -->
  <header class="bg-white shadow fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center justify-between px-4 py-3">
      <!-- Left -->
      <div class="flex items-center">
        <span class="text-xl font-bold">My Dashboard</span>
      </div>
      <!-- Right -->
      <div class="flex items-center space-x-4">
        <!-- Settings -->
        <a href="update_user.php" 
           class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600 transition">
          Settings
        </a>
        <!-- Logout -->
        <form action="logout.php" method="POST">
          <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
            Logout
          </button>
        </form>
      </div>
    </div>
  </header>

  <!-- ✅ Profile Update Form -->
  <div class="flex items-center justify-center pt-24 pb-10">
    <div class="bg-white p-4 rounded-lg shadow-lg w-[90%] max-w-3xl">
      <h1 class="text-xl font-bold text-center text-blue-700 mb-3">Update Your Profile</h1>

      <form method="post" class="grid grid-cols-2 gap-3">
        <!-- Name -->
        <div>
          <label class="block text-sm font-medium">Name</label>
          <input type="text" name="name" class="w-full border rounded p-2 text-sm"
                 value="<?php echo htmlspecialchars($user['name']); ?>">
        </div>

        <!-- Email (Readonly, cannot be changed) -->
        <div>
          <label class="block text-sm font-medium">Email</label>
          <input type="email" name="email"
                 class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed text-sm"
                 value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
        </div>

        <!-- DOB -->
        <div>
          <label class="block text-sm font-medium">Date of Birth</label>
          <input type="date" name="dob" class="w-full border rounded p-2 text-sm"
                 value="<?php echo htmlspecialchars($user['dob']); ?>">
        </div>

        <!-- Gender -->
        <div>
          <label class="block text-sm font-medium">Gender</label>
          <div class="flex items-center gap-3 p-2 border rounded">
            <label><input type="radio" name="gender" value="male" <?php if($user['gender']=="male") echo "checked"; ?>> Male</label>
            <label><input type="radio" name="gender" value="female" <?php if($user['gender']=="female") echo "checked"; ?>> Female</label>
          </div>
        </div>

        <!-- Phone -->
        <div>
          <label class="block text-sm font-medium">Phone</label>
          <input type="text" name="phone" class="w-full border rounded p-2 text-sm"
                 value="<?php echo htmlspecialchars($user['phone']); ?>">
        </div>

        <!-- Address -->
        <div class="col-span-2 grid grid-cols-3 gap-3">
          <div>
            <label class="block text-sm font-medium">Village</label>
            <input type="text" name="village" class="w-full border rounded p-2 text-sm"
                   value="<?php echo htmlspecialchars($user['village']); ?>">
          </div>
          <div>
            <label class="block text-sm font-medium">Post Office</label>
            <input type="text" name="po" class="w-full border rounded p-2 text-sm"
                   value="<?php echo htmlspecialchars($user['post_office']); ?>">
          </div>
          <div>
            <label class="block text-sm font-medium">Police Station</label>
            <input type="text" name="ps" class="w-full border rounded p-2 text-sm"
                   value="<?php echo htmlspecialchars($user['police_station']); ?>">
          </div>
          <div>
            <label class="block text-sm font-medium">District</label>
            <input type="text" name="district" class="w-full border rounded p-2 text-sm"
                   value="<?php echo htmlspecialchars($user['districe']); ?>">
          </div>
          <div>
            <label class="block text-sm font-medium">State</label>
            <input type="text" name="state" class="w-full border rounded p-2 text-sm"
                   value="<?php echo htmlspecialchars($user['state']); ?>">
          </div>
          <div>
            <label class="block text-sm font-medium">Pincode</label>
            <input type="text" name="pincode" class="w-full border rounded p-2 text-sm"
                   value="<?php echo htmlspecialchars($user['pin_code']); ?>">
          </div>
        </div>

        <!-- ✅ Removed Password field -->

        <!-- Submit -->
        <div class="col-span-2 text-center">
          <button type="submit" name="update_data"
                  class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition text-sm">
            Update Profile
          </button>
        </div>
      </form>
    </div>
  </div>

</body>
</html>
