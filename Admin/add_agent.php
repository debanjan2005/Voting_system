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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $agent_name = $_POST['agent_name'];
    $dob = $_POST['dob'];
    $agent_email = $_POST['agent_email'];
    $name_of_party = $_POST['name_of_party'];

    // ✅ Check if email already exists
    $check = $conn->prepare("SELECT id FROM agent WHERE email = ?");
    $check->bind_param("s", $agent_email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('This email is already registered. Please use another email.'); window.history.back();</script>";
        exit;
    }
    $check->close();

    // Upload files
    $symbol_path = '';
    $image_path = '';

    if (isset($_FILES['symbol']) && $_FILES['symbol']['error'] == 0) {
        $symbol_path = 'uploads/' . basename($_FILES['symbol']['name']);
        move_uploaded_file($_FILES['symbol']['tmp_name'], $symbol_path);
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_path = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // Insert into DB (using prepared statement for safety)
    $stmt = $conn->prepare("INSERT INTO agent (name, dob, email, name_of_party, symbol, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $agent_name, $dob, $agent_email, $name_of_party, $symbol_path, $image_path);

    if ($stmt->execute()) {
        echo "<script>alert('Agent added successfully!'); window.location.href = 'admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding agent: " . addslashes($stmt->error) . "');</script>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Agent - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            height: 100vh;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-indigo-100 to-indigo-200">

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

    <!-- Centered Form -->
    <div class="flex items-center justify-center" style="height: calc(100vh - 64px); margin-top: 64px;">
        <!-- <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-5xl h-[550px] overflow-auto border border-gray-200"> -->
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-5xl h-[550px] border border-gray-200 overflow-hidden">
            <!-- <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-5xl border border-gray-200 overflow-auto"> -->
                <!-- <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-5xl h-[550px] border border-gray-200 overflow-auto"> -->
                    <!-- <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-5xl border border-gray-200"> -->
    <!-- <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-5xl  h-[550px] border border-gray-200"> -->




            <h2 class="text-2xl font-bold mb-4 flex items-center">
                <i class="fas fa-user-plus text-indigo-600 mr-2"></i> Add New Agent
            </h2>

            <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Agent Name</label>
                    <input type="text" name="agent_name" class="w-full border-gray-300 rounded-lg p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Date of Birth</label>
                    <input type="date" name="dob" class="w-full border-gray-300 rounded-lg p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Agent Email</label>
                    <input type="email" name="agent_email" class="w-full border-gray-300 rounded-lg p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Name of Party</label>
                    <input type="text" name="name_of_party" class="w-full border-gray-300 rounded-lg p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Party Symbol</label>
                    <input type="file" name="symbol" id="symbol" class="w-full border-gray-300 rounded-lg p-2" accept="image/*" required>
                    <img id="symbolPreview" src="" alt="Symbol Preview" class="w-28 h-28 object-contain border rounded-md" />

                </div>
                <div>
                    <label class="block text-sm font-medium">Agent Photo</label>
                    <input type="file" name="image" id="agentImage" class="w-full border-gray-300 rounded-lg p-2" accept="image/*" required>
                    <img id="imagePreview" src="" alt="Agent Image Preview" class="w-28 h-28 object-cover border rounded-md" />
                
                </div>
                <div class="col-span-2">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">
                        Add Agent
                    </button>
                </div>
            </form>
            <a href="admin_dashboard.php" class="block mt-4 text-indigo-600 hover:underline">
                ← Back to Dashboard
            </a>
        </div>
        
    </div>

    <script>
        // Symbol preview
        document.getElementById('symbol').addEventListener('change', function (event) {
            const file = event.target.files[0];
            const preview = document.getElementById('symbolPreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
            }
        });

        // Agent image preview
        document.getElementById('agentImage').addEventListener('change', function (event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
            }
        });
    </script>

</body>

</html>
