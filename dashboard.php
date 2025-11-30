<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login_form.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes fadeInScale {
      0% {
        opacity: 0;
        transform: scale(0.9);
      }

      100% {
        opacity: 1;
        transform: scale(1);
      }
    }

    .animate-fadeInScale {
      animation: fadeInScale 0.6s ease-out forwards;
    }
  </style>
</head>

<body class="bg-gradient-to-br from-blue-100 to-blue-200">

  <!-- Navbar -->
  <header class="bg-white shadow fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center justify-between px-4 py-3">
      <!-- Left -->
      <div class="flex items-center">
        <span class="text-xl font-bold">My Dashboard</span>
      </div>
      <!-- Right -->
      <div class="flex items-center space-x-4">
        
        <!-- Settings Button -->
        <a href="update_user.php" 
           class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600 transition">
          Settings
        </a>

        <!-- Logout Button -->
        <form action="logout.php" method="POST">
          <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
            Logout
          </button>
        </form>
      </div>
    </div>
  </header>

  <!-- Centered Welcome & Buttons -->
  <div class="flex items-center justify-center min-h-screen pt-20">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md text-center min-h-[350px] flex flex-col justify-center animate-fadeInScale">
      <h2 class="text-2xl font-semibold mb-8">
        Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
      </h2>
      <div class="flex flex-col space-y-4">
        <!-- Link to give_vote.php -->
        <a href="give_vote.php"
          class="bg-green-500 text-white py-2 rounded hover:bg-green-600 transition transform hover:scale-105 text-center">
          Give Vote
        </a>

        <!-- Link to show_result.php -->
        <a href="show_result.php"
          class="bg-purple-500 text-white py-2 rounded hover:bg-purple-600 transition transform hover:scale-105 text-center">
          Show Result
        </a>
      </div>
    </div>
  </div>

</body>
</html>
