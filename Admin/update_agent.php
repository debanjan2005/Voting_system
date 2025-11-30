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

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$agent_id = intval($_GET['id']);

// Fetch existing agent
$stmt = $conn->prepare("SELECT * FROM agent WHERE id = ?");
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();
$agent = $result->fetch_assoc();

if (!$agent) {
    die("Agent not found");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $agent_name    = $_POST['agent_name'];
    $dob           = $_POST['dob'];
    $agent_email   = trim($_POST['agent_email']); // may be empty
    $name_of_party = $_POST['name_of_party'];

    $symbol_path = $agent['symbol'];
    $image_path  = $agent['image'];

    // Handle symbol upload
    if (isset($_FILES['symbol']) && $_FILES['symbol']['error'] == 0) {
        $symbol_path = 'uploads/' . basename($_FILES['symbol']['name']);
        move_uploaded_file($_FILES['symbol']['tmp_name'], $symbol_path);
    }

    // Handle agent image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_path = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // Decide SQL based on email input
    if (!empty($agent_email)) {
        // Update including email
        $update = $conn->prepare("UPDATE agent SET name=?, dob=?, email=?, name_of_party=?, symbol=?, image=? WHERE id=?");
        $update->bind_param("ssssssi", $agent_name, $dob, $agent_email, $name_of_party, $symbol_path, $image_path, $agent_id);
    } else {
        // Update without changing email
        $update = $conn->prepare("UPDATE agent SET name=?, dob=?, name_of_party=?, symbol=?, image=? WHERE id=?");
        $update->bind_param("sssssi", $agent_name, $dob, $name_of_party, $symbol_path, $image_path, $agent_id);
    }

    if ($update->execute()) {
        echo "<script>alert('Agent updated successfully!'); window.location.href = 'admin_dashboard.php';</script>";
    } else {
        echo "Error: " . $update->error;
    }
    $update->close();
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Agent - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-5xl h-[550px] overflow-auto border border-gray-200">
            <h2 class="text-2xl font-bold mb-4 flex items-center">
                <i class="fas fa-user-edit text-indigo-600 mr-2"></i> Update Agent
            </h2>

            <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Agent Name</label>
                    <input type="text" name="agent_name" value="<?php echo htmlspecialchars($agent['name']); ?>" class="w-full border-gray-300 rounded-lg p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Date of Birth</label>
                    <input type="date" name="dob" value="<?php echo htmlspecialchars($agent['dob']); ?>" class="w-full border-gray-300 rounded-lg p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Agent Email</label>
                    <input type="email" name="agent_email"
                        value="<?php echo htmlspecialchars($agent['email']); ?>"
                        class="w-full border-gray-300 rounded-lg p-2 bg-gray-100 cursor-not-allowed"
                        disabled>
                </div>

                <!-- <div>
                    <label class="block text-sm font-medium">Agent Email</label>
                    <input type="email" name="agent_email" placeholder="Leave blank to keep old email"
                           value="
                           
                           class="w-full border-gray-300 rounded-lg p-2">
                </div> -->
                <?php
                // echo htmlspecialchars($agent['email']);
                ?>
                <div>
                    <label class="block text-sm font-medium">Name of Party</label>
                    <input type="text" name="name_of_party" value="<?php echo htmlspecialchars($agent['name_of_party']); ?>" class="w-full border-gray-300 rounded-lg p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Party Symbol</label>
                    <input type="file" name="symbol" id="symbol" class="w-full border-gray-300 rounded-lg p-2" accept="image/*">
                    <!-- Current Symbol -->
                    <img src="<?php echo $agent['symbol']; ?>" alt="Current Symbol"
                        class="w-28 h-28 object-contain border mt-2 rounded-md" id="symbolPreview">
                </div>

                <div>
                    <label class="block text-sm font-medium">Agent Photo</label>
                    <input type="file" name="image" id="agentImage" class="w-full border-gray-300 rounded-lg p-2" accept="image/*">
                    <!-- Current Image -->
                    <img src="<?php echo $agent['image']; ?>" alt="Current Photo"
                        class="w-28 h-28 object-cover border mt-2 rounded-md" id="imagePreview">
                </div>

                <div class="col-span-2">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">
                        Update Agent
                    </button>
                </div>
            </form>

            <a href="admin_dashboard.php" class="block mt-4 text-indigo-600 hover:underline">
                ← Back to Dashboard
            </a>
        </div>
    </div>


    <script>
            // Preview for Party Symbol
            document.getElementById('symbol').addEventListener('change', function(event) {
                const [file] = event.target.files;
                if (file) {
                    document.getElementById('symbolPreview').src = URL.createObjectURL(file);
                }
            });

        // Preview for Agent Image
        document.getElementById('agentImage').addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                document.getElementById('imagePreview').src = URL.createObjectURL(file);
            }
        });
    </script>

</body>

</html>