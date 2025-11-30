<?php
session_start();
include("../db_conn.php"); // DB connection

// ✅ Ensure only logged-in admin can access
if (!isset($_SESSION['admin_name'])) {
    header("Location: login_form.php");
    exit;
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";
$admin_initial = strtoupper(substr($admin_name, 0, 1));

// ✅ Fetch election setting (end_date & end_time + status)
$setting_res = mysqli_query($conn, "SELECT * FROM setting WHERE id=1 LIMIT 1");
$setting = mysqli_fetch_assoc($setting_res);
$current_status = $setting['result_status'] ?? 'unpublished';
$end_date = $setting['end_date'] ?? null;
$end_time = $setting['end_time'] ?? null;

// ✅ Current DateTime
$current_datetime = new DateTime();
$end_datetime = new DateTime("$end_date $end_time");

// ✅ Handle publish/unpublish request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Only allow publishing after election ends
    if ($action === 'publish') {
        if ($current_datetime >= $end_datetime) {
            $sql = "UPDATE setting SET result_status='published' WHERE id=1";
        } else {
            echo "<script>alert('You can publish results only after election ends on $end_date $end_time'); window.location.href='publish_result.php';</script>";
            exit;
        }
    } elseif ($action === 'unpublish') {
        $sql = "UPDATE setting SET result_status='unpublished' WHERE id=1";
    }

    if (isset($sql) && mysqli_query($conn, $sql)) {
        echo "<script>alert('Result status updated successfully!'); window.location.href='publish_result.php';</script>";
        exit;
    } elseif (isset($sql)) {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// ✅ Total users
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];

// ✅ Users who voted
$voted_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT voter_id) as voted FROM vote"))['voted'];

// ✅ Users who did not vote
$not_voted_users = $total_users - $voted_users;

// ✅ List of voters and their chosen agents
$voters_sql = "
    SELECT u.name AS voter_name, a.name AS agent_name, a.name_of_party 
    FROM vote v
    JOIN users u ON v.voter_id = u.id
    JOIN agent a ON v.agent_id = a.id
";
$voters_res = mysqli_query($conn, $voters_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Publish Result - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-100 to-green-200 min-h-screen">

    <!-- Navbar -->
  <header class="bg-white shadow fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center justify-between px-4 py-3">
      <div class="flex items-center space-x-3">
        <i class="fas fa-bullhorn text-2xl text-green-600"></i>
        <span class="text-xl font-bold">Publish Result</span>
      </div>
      <div class="flex items-center space-x-4">
        <div class="text-right hidden sm:block">
          <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($admin_name); ?></p>
          <p class="text-xs text-gray-500">Administrator</p>
        </div>
        <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center">
          <span class="text-white font-bold text-lg"><?php echo $admin_initial; ?></span>
        </div>
        <!-- ✅ Logout button -->
        <a href="admin_logout.php" 
           class="ml-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
      </div>
    </div>
  </header>


  <!-- Content -->
  <div class="pt-28 px-6 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      <!-- Publish Box -->
      <div class="bg-white shadow-lg rounded-2xl p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
              <i class="fas fa-check-circle text-green-600 mr-3"></i>
              Manage Result Publication
          </h2>
          <p class="mb-4 text-gray-700">
              Current Status: 
              <?php if ($current_status === 'published'): ?>
                  <span class="font-bold text-green-600">Published ✅</span>
              <?php else: ?>
                  <span class="font-bold text-red-600">Unpublished ❌</span>
              <?php endif; ?>
          </p>
          <p class="mb-6 text-gray-600">
              Election End Time: <span class="font-bold"><?php echo $end_date . " " . $end_time; ?></span>
          </p>
          <form method="POST" class="space-y-4">
              <?php if ($current_status === 'published'): ?>
                  <button type="submit" name="action" value="unpublish"
                          class="w-full bg-red-600 text-white py-3 px-4 rounded-xl font-semibold text-lg hover:bg-red-700 transition">
                      <i class="fas fa-times-circle mr-2"></i> Unpublish Result
                  </button>
              <?php else: ?>
                  <button type="submit" name="action" value="publish"
                          class="w-full bg-green-600 text-white py-3 px-4 rounded-xl font-semibold text-lg hover:bg-green-700 transition"
                          <?php echo ($current_datetime < $end_datetime) ? "disabled class='opacity-50 cursor-not-allowed w-full bg-gray-400 py-3 px-4 rounded-xl font-semibold text-lg'" : ""; ?>>
                      <i class="fas fa-bullhorn mr-2"></i> Publish Result
                  </button>
              <?php endif; ?>
          </form>
      </div>
                    
      <!-- Voting Statistics -->
      <div class="bg-white shadow-lg rounded-2xl p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
              <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
              Voting Statistics
          </h2>
          <p class="text-lg text-gray-700 mb-2">Total Users: <span class="font-bold"><?php echo $total_users; ?></span></p>
          <p class="text-lg text-green-700 mb-2">Voted Users: <span class="font-bold"><?php echo $voted_users; ?></span></p>
          <p class="text-lg text-red-700">Not Voted Users: <span class="font-bold"><?php echo $not_voted_users; ?></span></p>
      </div>

      <!-- Voters List -->
      <div class="bg-white shadow-lg rounded-2xl p-8 overflow-y-auto max-h-[500px]">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
              <i class="fas fa-users text-purple-600 mr-3"></i>
              Voters & Their Choice
          </h2>
          <table class="w-full border border-gray-300 text-sm text-left">
              <thead class="bg-gray-100">
                  <tr>
                      <th class="p-2 border">Voter</th>
                      <th class="p-2 border">Agent</th>
                      <th class="p-2 border">Party</th>
                  </tr>
              </thead>
              <tbody>
                  <?php while ($row = mysqli_fetch_assoc($voters_res)) : ?>
                      <tr class="hover:bg-gray-50">
                          <td class="p-2 border"><?php echo htmlspecialchars($row['voter_name']); ?></td>
                          <td class="p-2 border"><?php echo htmlspecialchars($row['agent_name']); ?></td>
                          <td class="p-2 border"><?php echo htmlspecialchars($row['name_of_party']); ?></td>
                      </tr>
                  <?php endwhile; ?>
              </tbody>
          </table>
      </div>

    </div>
  </div>

</body>
</html>
