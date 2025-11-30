<?php
session_start();
include("db_conn.php"); // DB connection

// ✅ Fetch result status
$res = mysqli_query($conn, "SELECT result_status FROM setting WHERE id=1 LIMIT 1");
$row = mysqli_fetch_assoc($res);
$result_status = $row['result_status'] ?? 'unpublished';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Result</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen">

    <div class="pt-16 px-6 max-w-7xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-poll text-indigo-600 mr-3"></i>
                Election Result
            </h2>

            <?php if ($result_status !== 'published'): ?>
                <!-- Before publish -->
                <div class="p-6 bg-yellow-100 border-l-4 border-yellow-500 rounded">
                    <p class="text-yellow-700 font-semibold">
                        ⚠️ Results are not published yet. Please check back later.
                    </p>
                </div>
            <?php else: ?>
                <?php
                // ✅ Fetch votes per agent from `vote` table
                $sql = "SELECT a.id, a.name, a.name_of_party, a.symbol, a.image,
                               COUNT(v.id) AS total_votes
                        FROM agent a
                        LEFT JOIN vote v ON a.id = v.agent_id
                        GROUP BY a.id
                        ORDER BY total_votes DESC";
                $result = mysqli_query($conn, $sql);

                // Collect results
                $max_votes = 0;
                $total_votes_all = 0;
                $all_results = [];

                while ($row = mysqli_fetch_assoc($result)) {
                    $all_results[] = $row;
                    $total_votes_all += $row['total_votes'];
                    if ($row['total_votes'] > $max_votes) {
                        $max_votes = $row['total_votes'];
                    }
                }
                ?>

                <table class="w-full border-collapse border border-gray-300">
                    <thead class="bg-indigo-600 text-white">
                        <tr>
                            <th class="py-3 px-4 border border-gray-300 text-left">Image</th>
                            <th class="py-3 px-4 border border-gray-300 text-left">Candidate</th>
                            <th class="py-3 px-4 border border-gray-300 text-left">Party</th>
                            <th class="py-3 px-4 border border-gray-300 text-left">Symbol</th>
                            <th class="py-3 px-4 border border-gray-300 text-center">Total Votes</th>
                            <th class="py-3 px-4 border border-gray-300 text-center">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_results as $row): 
                            $percentage = ($total_votes_all > 0) ? round(($row['total_votes'] / $total_votes_all) * 100, 2) : 0;
                        ?>
                            <tr class="hover:bg-gray-50">
                                <!-- Candidate image -->
                                <td class="py-3 px-4 border border-gray-300 text-center">
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="admin/<?php echo htmlspecialchars($row['image']); ?>"
                                             alt="Candidate" class="w-12 h-12 rounded-full mx-auto">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle text-gray-400 text-3xl"></i>
                                    <?php endif; ?>
                                </td>

                                <!-- Candidate name -->
                                <td class="py-3 px-4 border border-gray-300 font-medium text-gray-700">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                    <?php if ($row['total_votes'] == $max_votes && $max_votes > 0): ?>
                                        <span class="ml-2 text-yellow-500"><i class="fas fa-crown"></i></span>
                                    <?php endif; ?>
                                </td>

                                <!-- Party -->
                                <td class="py-3 px-4 border border-gray-300 text-gray-600">
                                    <?php echo htmlspecialchars($row['name_of_party']); ?>
                                </td>

                                <!-- Symbol -->
                                <td class="py-3 px-4 border border-gray-300 text-center">
                                    <?php if (!empty($row['symbol'])): ?>
                                        <img src="admin/<?php echo htmlspecialchars($row['symbol']); ?>"
                                             alt="Symbol" class="w-12 h-12 rounded-full mx-auto">
                                    <?php else: ?>
                                        <span class="text-gray-400">N/A</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Votes -->
                                <td class="py-3 px-4 border border-gray-300 text-center font-bold text-indigo-600">
                                    <?php echo $row['total_votes']; ?>
                                </td>

                                <!-- Percentage -->
                                <td class="py-3 px-4 border border-gray-300 text-center font-semibold text-green-600">
                                    <?php echo $percentage; ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="mt-6 p-4 bg-green-100 border-l-4 border-green-600 rounded">
                    <p class="text-green-700 font-semibold">
                        ✅ Result has been officially published by the admin.
                    </p>
                    <p class="text-gray-700 mt-2">
                        Total Votes Cast: <strong><?php echo $total_votes_all; ?></strong>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
