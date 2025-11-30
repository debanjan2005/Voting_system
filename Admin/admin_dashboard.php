<?php
session_start();

// ✅ Check session before showing page
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fallbacks to avoid warnings
$admin_name = isset($_SESSION['admin_name']) && !empty($_SESSION['admin_name']) 
    ? $_SESSION['admin_name'] 
    : 'Admin';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
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
<body class="bg-gradient-to-br from-indigo-100 to-indigo-200">

  <!-- Navbar -->
  <header class="bg-white shadow fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center justify-between px-4 py-3">
      <div class="flex items-center space-x-3">
        <i class="fas fa-vote-yea text-2xl text-indigo-600"></i>
        <span class="text-xl font-bold">Admin Dashboard</span>
      </div>
      <div class="flex items-center space-x-4">
        <div class="text-right hidden sm:block">
          <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($admin_name); ?></p>
          <p class="text-xs text-gray-500">Administrator</p>
        </div>
        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
          <span class="text-white font-bold text-lg"><?php echo $admin_initial; ?></span>
        </div>
        <form action="admin_logout.php" method="POST">
          <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition flex items-center space-x-1">
            <i class="fas fa-sign-out-alt"></i><span>Logout</span>
          </button>
        </form>
      </div>
    </div>
  </header>

  <!-- Dashboard Content -->
  <div class="pt-24 px-6 max-w-7xl mx-auto">
      <!-- Stats Cards -->
      <!-- <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
          <div class="bg-white shadow-lg rounded-lg p-6 text-center animate-fadeInScale border-l-4 border-indigo-500">
              <i class="fas fa-users text-indigo-500 text-3xl mb-2"></i>
              <h2 class="text-lg font-semibold text-gray-700">Total Agents</h2>
              <p class="text-3xl font-bold text-indigo-600 mt-2">12</p>
              <p class="text-green-600 text-sm mt-1"><i class="fas fa-arrow-up"></i> 5% from last week</p>
          </div>

          <div class="bg-white shadow-lg rounded-lg p-6 text-center animate-fadeInScale border-l-4 border-green-500">
              <i class="fas fa-vote-yea text-green-500 text-3xl mb-2"></i>
              <h2 class="text-lg font-semibold text-gray-700">Active Elections</h2>
              <p class="text-3xl font-bold text-green-600 mt-2">3</p>
              <p class="text-green-600 text-sm mt-1"><i class="fas fa-arrow-up"></i> New this week</p>
          </div>

          <div class="bg-white shadow-lg rounded-lg p-6 text-center animate-fadeInScale border-l-4 border-purple-500">
              <i class="fas fa-chart-bar text-purple-500 text-3xl mb-2"></i>
              <h2 class="text-lg font-semibold text-gray-700">Total Votes</h2>
              <p class="text-3xl font-bold text-purple-600 mt-2">5,892</p>
              <p class="text-green-600 text-sm mt-1"><i class="fas fa-arrow-up"></i> 8% growth</p>
          </div>

          <div class="bg-white shadow-lg rounded-lg p-6 text-center animate-fadeInScale border-l-4 border-yellow-500">
              <i class="fas fa-clock text-yellow-500 text-3xl mb-2"></i>
              <h2 class="text-lg font-semibold text-gray-700">Pending Approvals</h2>
              <p class="text-3xl font-bold text-yellow-600 mt-2">4</p>
              <p class="text-red-600 text-sm mt-1"><i class="fas fa-arrow-down"></i> 2 from yesterday</p>
          </div>
      </div> -->

      <!-- Action Buttons -->
      <div class="grid gap-6 md:grid-cols-4 mt-8">
          <a href="add_agent.php" class="bg-white shadow-lg rounded-lg p-6 text-center animate-fadeInScale hover:shadow-xl transition border-2 border-dashed border-indigo-300 hover:border-indigo-500">
              <i class="fas fa-user-plus text-3xl text-indigo-600 mb-3"></i>
              <h3 class="text-lg font-semibold">Add Agent</h3>
              <p class="text-sm text-gray-500">Create a new voting agent account.</p>
          </a>
          <a href="all_agent.php" class="bg-white shadow-lg rounded-lg p-6 text-center animate-fadeInScale hover:shadow-xl transition border-2 border-dashed border-green-300 hover:border-green-500">
              <i class="fas fa-users-cog text-3xl text-green-600 mb-3"></i>
              <h3 class="text-lg font-semibold">Show Agents</h3>
              <p class="text-sm text-gray-500">View and edit all agent accounts.</p>
          </a>
          
          <a href="publish_result.php" class="bg-white shadow-lg rounded-lg p-6 text-center animate-fadeInScale hover:shadow-xl transition border-2 border-dashed border-purple-300 hover:border-purple-500">
              <i class="fas fa-chart-pie text-3xl text-purple-600 mb-3"></i>
              <h3 class="text-lg font-semibold">Election Results</h3>
              <p class="text-sm text-gray-500">View the latest voting results.</p>
          </a>
          <!-- ✅ Settings Card -->
          <a href="date_time_set.php" class="bg-white shadow-lg rounded-lg p-6 text-center animate-fadeInScale hover:shadow-xl transition border-2 border-dashed border-yellow-300 hover:border-yellow-500">
              <i class="fas fa-cog text-3xl text-yellow-600 mb-3"></i>
              <h3 class="text-lg font-semibold">Settings</h3>
              <p class="text-sm text-gray-500">Manage system preferences & security.</p>
          </a>
      </div>
  </div>

</body>
</html>
