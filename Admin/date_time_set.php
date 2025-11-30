<?php
session_start();
include("../db_conn.php"); // DB connection

$message = "";
$admin_name = !empty($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $start_time = $_POST['start_time'];
    $end_date   = $_POST['end_date'];
    $end_time   = $_POST['end_time'];

    // ✅ Check if any setting already exists
    $sql_check = "SELECT id FROM setting LIMIT 1";
    $result = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result) > 0) {
        // Update existing setting (always id=1 in your table)
        $sql = "UPDATE setting 
                SET start_date='$start_date', start_time='$start_time',
                    end_date='$end_date', end_time='$end_time'
                WHERE id=1";
    } else {
        // Insert first time
        $sql = "INSERT INTO setting (id, start_date, start_time, end_date, end_time) 
                VALUES (1, '$start_date', '$start_time', '$end_date', '$end_time')";
    }

    if (mysqli_query($conn, $sql)) {
        // Save values in session for display
        $_SESSION['start_date'] = $start_date;
        $_SESSION['start_time'] = $start_time;
        $_SESSION['end_date']   = $end_date;
        $_SESSION['end_time']   = $end_time;

        echo "<script>alert('Settings saved successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// ✅ Fetch current settings from DB (always first row)
$res = mysqli_query($conn, "SELECT * FROM setting LIMIT 1");
if ($row = mysqli_fetch_assoc($res)) {
    $_SESSION['start_date'] = $row['start_date'];
    $_SESSION['start_time'] = $row['start_time'];
    $_SESSION['end_date']   = $row['end_date'];
    $_SESSION['end_time']   = $row['end_time'];
}

// Fallback admin info (so navbar works)
$admin_name = $_SESSION['admin_name'] ?? "Admin";
$admin_initial = strtoupper(substr($admin_name, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @keyframes fadeInScale {
      0% { opacity: 0; transform: scale(0.9); }
      100% { opacity: 1; transform: scale(1); }
    }
    .animate-fadeInScale {
      animation: fadeInScale 0.6s ease-out forwards;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-indigo-100 to-indigo-200 min-h-screen">

  <!-- Navbar -->
  <header class="bg-white shadow fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center justify-between px-4 py-3">
      <div class="flex items-center space-x-3">
        <i class="fas fa-cog text-2xl text-indigo-600"></i>
        <span class="text-xl font-bold">Settings</span>
      </div>
      <div class="flex items-center space-x-4">
        <div class="text-right hidden sm:block">
          <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($admin_name); ?></p>
          <p class="text-xs text-gray-500">Administrator</p>
        </div>
        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
          <span class="text-white font-bold text-lg"><?php echo $admin_initial; ?></span>
        </div>
      </div>
    </div>
  </header>
  <!-- Content -->
  <div class="pt-28 px-6 max-w-4xl mx-auto">
      <div class="bg-white shadow-lg rounded-2xl p-8 animate-fadeInScale">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
              <i class="fas fa-calendar-alt text-indigo-600 mr-3"></i>
              Set Election Date & Time
          </h2>

          <form method="POST" class="space-y-8">

              <!-- Start -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="bg-indigo-50 p-4 rounded-lg shadow-inner">
                      <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                          <i class="fas fa-calendar-day text-indigo-600 mr-1"></i> Start Date
                      </label>
                      <input type="date" name="start_date" id="start_date"
                             value="<?php echo $_SESSION['start_date'] ?? ''; ?>"
                             class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                  </div>
                  <div class="bg-indigo-50 p-4 rounded-lg shadow-inner">
                      <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">
                          <i class="fas fa-clock text-indigo-600 mr-1"></i> Start Time
                      </label>
                      <input type="time" name="start_time" id="start_time"
                             value="<?php echo $_SESSION['start_time'] ?? ''; ?>"
                             class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                  </div>
              </div>

              <!-- End -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                      <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                          <i class="fas fa-calendar-day text-yellow-600 mr-1"></i> End Date
                      </label>
                      <input type="date" name="end_date" id="end_date"
                             value="<?php echo $_SESSION['end_date'] ?? ''; ?>"
                             class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500">
                  </div>
                  <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                      <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">
                          <i class="fas fa-clock text-yellow-600 mr-1"></i> End Time
                      </label>
                      <input type="time" name="end_time" id="end_time"
                             value="<?php echo $_SESSION['end_time'] ?? ''; ?>"
                             class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500">
                  </div>
              </div>

              <!-- Save -->
              <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-xl font-semibold text-lg hover:bg-indigo-700 transition">
                  <i class="fas fa-save mr-2"></i> Save Settings
              </button>

              <!-- Back to Dashboard -->
              <a href="admin_dashboard.php" 
                 class="w-full inline-block text-center bg-gray-600 text-white py-3 px-4 rounded-xl font-semibold text-lg hover:bg-gray-700 transition mt-4">
                 <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
              </a>
          </form>

          <!-- Current Settings -->
          <?php 
          if (isset($_SESSION['start_date'], $_SESSION['start_time'], $_SESSION['end_date'], $_SESSION['end_time'])): 
          ?>
              <!-- <div class="mt-8 p-6 bg-gray-100 rounded-xl shadow-inner">
                  <h3 class="font-bold text-gray-700 mb-3 flex items-center">
                      <i class="fas fa-info-circle text-indigo-600 mr-2"></i> Current Settings
                  </h3>
                  <p><strong>Start:</strong> <?php echo date("d M Y, h:i A", strtotime($_SESSION['start_date']." ".$_SESSION['start_time'])); ?></p>
                  <p><strong>End:</strong> <?php echo date("d M Y, h:i A", strtotime($_SESSION['end_date']." ".$_SESSION['end_time'])); ?></p>
              </div> -->
          <?php 
          endif;
           ?>
      </div>
  </div>

</body>
</html>
