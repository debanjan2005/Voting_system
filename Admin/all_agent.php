<?php
session_start();

// ✅ Check session before showing page
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include('../db_conn.php');
$admin_name = !empty($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
$admin_initial = strtoupper(substr($admin_name, 0, 1));

// Handle deletion
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM agent WHERE id = $delete_id");
    header("Location: all_agent.php");
    exit;
}

// Fetch agents
$sql = "SELECT * FROM agent ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Agents - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-indigo-100 to-indigo-200 min-h-screen">

<!-- Navbar -->
<header class="bg-white shadow fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center space-x-3">
            <i class="fas fa-vote-yea text-2xl text-indigo-600"></i>
            <span class="text-xl font-bold">Admin Panel</span>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-medium text-gray-900"><?php echo $admin_name; ?></p>
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

<!-- Page Content -->
<div class="pt-24 px-6">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-7xl mx-auto border border-gray-200">
        <h2 class="text-2xl font-bold mb-4 flex items-center">
            <i class="fas fa-users text-indigo-600 mr-2"></i> Registered Agents
        </h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left border border-gray-300 rounded">
                    <thead class="bg-indigo-100 text-gray-700">
                        <tr>
                            <th class="py-2 px-3 border">#</th>
                            <th class="py-2 px-3 border">Name</th>
                            <th class="py-2 px-3 border">Email</th>
                            <th class="py-2 px-3 border">DOB</th>
                            <th class="py-2 px-3 border">Party</th>
                            <th class="py-2 px-3 border">Symbol</th>
                            <th class="py-2 px-3 border">Photo</th>
                            <th class="py-2 px-3 border text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-indigo-50">
                                <td class="py-2 px-3 border"><?php echo $i++; ?></td>
                                <td class="py-2 px-3 border"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="py-2 px-3 border"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="py-2 px-3 border"><?php echo htmlspecialchars($row['dob']); ?></td>
                                <td class="py-2 px-3 border"><?php echo htmlspecialchars($row['name_of_party']); ?></td>
                                <td class="py-2 px-3 border">
                                    <?php if (!empty($row['symbol'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['symbol']); ?>" alt="Symbol" class="w-12 h-12 object-contain border rounded">
                                    <?php else: ?>
                                        <span class="text-gray-400">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-3 border">
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Agent" class="w-12 h-12 object-cover border rounded">
                                    <?php else: ?>
                                        <span class="text-gray-400">No Photo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-3 border text-center">
                                    <a href="update_agent.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-800 mx-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="all_agent.php?delete=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-800 mx-1" onclick="return confirm('Are you sure you want to delete this agent?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-600">No agents found.</p>
        <?php endif; ?>

        <a href="admin_dashboard.php" class="inline-block mt-6 text-indigo-600 hover:underline">
            ← Back to Dashboard
        </a>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
