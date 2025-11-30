<?php
session_start();
include("db_conn.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

$voter_id = $_SESSION['user_id'];

// ✅ Get voting period from DB
$sql = "SELECT start_date, start_time, end_date, end_time FROM setting ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $sql);
$setting = mysqli_fetch_assoc($result);

$start_datetime = $setting['start_date'] . ' ' . $setting['start_time'];
$end_datetime   = $setting['end_date']   . ' ' . $setting['end_time'];
$current_time   = date("Y-m-d H:i:s");

// ✅ Check if already voted
$alreadyVoted = false;
$check = mysqli_query($conn, "SELECT * FROM vote WHERE voter_id='$voter_id'");
if (mysqli_num_rows($check) > 0) {
    $alreadyVoted = true;
}

// ✅ Handle vote submit (only if not voted)
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agent_id']) && !$alreadyVoted) {
    $agent_id = $_POST['agent_id'];
    $vote_date = date("Y-m-d");
    $vote_time = date("H:i:s");

    $insert = "INSERT INTO vote (voter_id, agent_id, vote_date, vote_time) 
               VALUES ('$voter_id', '$agent_id', '$vote_date', '$vote_time')";
    if (mysqli_query($conn, $insert)) {
        // $message = "✅ Your vote has been recorded!";
        $alreadyVoted = true; // update status
    } else {
        $message = "❌ Error saving vote.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Vote</title>
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
            <div class="flex items-center">
                <span class="text-xl font-bold">Voting System</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="dashboard.php" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                <form action="logout.php" method="POST">
                    <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Vote Section -->
    <div class="flex items-center justify-center min-h-screen pt-20">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-3xl text-center animate-fadeInScale">
            <h2 class="text-2xl font-semibold mb-6">Cast Your Vote</h2>

            <?php if (!empty($message)) : ?>
                <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
                    <?= htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($alreadyVoted): ?>
                <!-- ✅ Already Voted Message -->
                <div class="p-4 bg-green-100 text-green-800 rounded shadow">
                    ✅ You have already voted.<br>
                    Thank you for your participation!
                </div>

            <?php elseif ($current_time < $start_datetime): ?>
                <div class="p-4 bg-blue-100 text-blue-800 rounded">
                    🕒 Voting has not started yet.<br>
                    <span class="font-semibold">Starts:</span> <?= $start_datetime; ?>
                </div>

            <?php elseif ($current_time > $end_datetime): ?>
                <div class="p-4 bg-red-100 text-red-800 rounded">
                    ⛔ Voting period has ended.<br>
                    <span class="font-semibold">Ended:</span> <?= $end_datetime; ?>
                </div>

            <?php else: ?>
                <!-- ✅ Voting Form -->
                <form method="POST" class="space-y-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <?php
                        $agents = mysqli_query($conn, "SELECT * FROM agent");
                        while ($row = mysqli_fetch_assoc($agents)) {
                            echo "
                        <label class='block border rounded-lg p-4 text-left cursor-pointer hover:bg-green-50 shadow'>
                            <input type='radio' name='agent_id' value='{$row['id']}' class='mr-2' required>
                            <div class='flex items-center space-x-4'>
                                <img src='Admin/{$row['image']}' alt='symbol' class='w-16 h-16 object-contain border rounded'>
                                <div>
                                    <h3 class='font-bold text-lg'>{$row['name']}</h3>
                                    <p class='text-sm text-gray-600'><span class='font-semibold'>Party:</span> {$row['name_of_party']}</p>
                                  <img src='Admin/{$row['symbol']}' alt='symbol' class='w-16 h-16 object-contain border rounded'>

                                    <p class='text-xs text-gray-500'>DOB: {$row['dob']}</p>
                                    <p class='text-xs text-gray-500'>Email: {$row['email']}</p>
                                </div>
                            </div>
                        </label>";
                        }
                        ?>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition transform hover:scale-105">
                        Submit Vote
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>