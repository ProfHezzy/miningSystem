<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']); // Now it's safe to use


// Check if the user has accessed index.php first
if (!isset($_SESSION['visited_index'])) {
    header("Location: index.php"); // Redirect to index.php
    exit;
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit;
}

// Session timeout (30 minutes)
$timeout_duration = 1800; // 1800 seconds = 30 minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset(); // Unset session variables
    session_destroy(); // Destroy session
    header("Location: login.php?message=session_expired");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Reset last activity time



// Get last mined time
$query = "SELECT last_mined FROM mining WHERE user_id = ? ORDER BY last_mined DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$last_mined = $row['last_mined'] ?? "Never";

// Get total points earned
$query = "SELECT SUM(points_earned) AS total_points FROM mining WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$points_earned = $row['total_points'] ?? 0;


$can_mine = true;
if ($last_mined) {
    $last_mined_time = strtotime($last_mined);
    $current_time = time();
    $time_difference = $current_time - $last_mined_time;
    if ($time_difference < 86400) { // 24 hours in seconds
        $can_mine = false;
    }
}

// Get user social media status
$query = "SELECT facebook_followed, twitter_followed, instagram_followed, linkedin_followed, total_points FROM users WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Error: No user data found for user_id = $user_id.");
}

// Corrected variable names
$facebook_followed = $user['facebook_followed'] ?? 'Not Available';
$twitter_followed = $user['twitter_followed'] ?? 'Not Available';
$instagram_followed = $user['instagram_followed'] ?? 'Not Available';
$linkedin_followed = $user['linkedin_followed'] ?? 'Not Available';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
    <style>
        .mine-button:disabled {
            background: gray;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-72 bg-gray-900 text-white p-6 flex flex-col justify-between min-h-screen">
            <div>
                <h2 class="text-2xl font-bold text-center mb-6">Dashboard</h2>
                <ul class="space-y-4">
                    <li class="flex items-center p-2 hover:bg-gray-700 rounded-lg cursor-pointer">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </li>
                    <li class="flex items-center p-2 hover:bg-gray-700 rounded-lg cursor-pointer">
                        <i class="fas fa-user mr-3"></i> Profile
                    </li>
                    <li class="flex items-center p-2 hover:bg-gray-700 rounded-lg cursor-pointer">
                        <a href="withdrawal.php" class="flex items-center w-full text-white">
                            <i class="fas fa-user mr-3"></i> Withdrawal
                        </a>
                    </li>

                </ul>
            </div>
            <a href="logout.php" class="flex items-center justify-center text-red-500 hover:text-red-700 py-2">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <h2 class="text-3xl font-semibold mb-6">Welcome, User</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow-md rounded-lg p-6 text-center transition-transform transform hover:scale-105">
                    <h3 class="text-xl font-semibold">Last Mined</h3>
                    <p id="last-mined" class="text-gray-600 text-lg"> <?php echo $last_mined; ?> </p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6 text-center transition-transform transform hover:scale-105">
                    <h3 class="text-xl font-semibold">Points Earned</h3>
                    <p id="total-points" class="text-green-500 text-lg"> <?php echo $points_earned; ?> Points</p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6 text-center transition-transform transform hover:scale-105">
                    <h3 class="text-xl font-semibold">Mining Status</h3>
                    <p id="mining-status" class="text-gray-600 text-lg">Active</p>
                </div>
            </div>

            <div class="mt-8 flex flex-col items-center">
                <button id="mine-btn" class="bg-blue-500 text-white px-6 py-3 rounded-lg text-lg hover:bg-blue-600 transition disabled:bg-gray-500" onclick="startMining()" <?php echo $can_mine ? '' : 'disabled'; ?>>Start Mining</button>
                <?php if (!$can_mine): ?>
                    <p id="countdown" class="text-red-500 mt-2"></p>
                    <script>
                        function startCountdown(lastMinedTime) {
                            let now = new Date().getTime();
                            let lastMined = new Date(lastMinedTime).getTime();
                            let nextMineTime = lastMined + (24 * 60 * 60 * 1000);
                            function updateCountdown() {
                                let currentTime = new Date().getTime();
                                let timeLeft = nextMineTime - currentTime;
                                if (timeLeft > 0) {
                                    let hours = Math.floor(timeLeft / (1000 * 60 * 60));
                                    let minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                                    let seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                                    document.getElementById("countdown").innerText = `You can mine again in ${hours}h ${minutes}m ${seconds}s`;
                                } else {
                                    document.getElementById("countdown").innerText = "You can mine now!";
                                    document.getElementById("mine-btn").removeAttribute("disabled");
                                }
                            }
                            updateCountdown();
                            setInterval(updateCountdown, 1000);
                        }
                        startCountdown("<?php echo $last_mined; ?>");
                    </script>
                <?php endif; ?>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6 text-center mt-10">
                <h3 class="text-xl font-semibold">Follow & Earn Bonus</h3>
                <ul class="mt-4 space-y-3">
                    <li>
                        <a href="https://facebook.com/yourpage" target="_blank" class="text-blue-600 underline" onclick="followSocial('facebook')">
                            <i class="fab fa-facebook"></i> Follow on Facebook
                        </a>
                        <?php if ($user['facebook_followed']): ?> ✅ <?php endif; ?>
                    </li>
                    <li>
                        <a href="https://twitter.com/yourprofile" target="_blank" class="text-blue-500 underline" onclick="followSocial('twitter')">
                            <i class="fab fa-twitter"></i> Follow on Twitter
                        </a>
                        <?php if ($user['twitter_followed']): ?> ✅ <?php endif; ?>
                    </li>
                    <li>
                        <a href="https://instagram.com/yourprofile" target="_blank" class="text-pink-600 underline" onclick="followSocial('instagram')">
                            <i class="fab fa-instagram"></i> Follow on Instagram
                        </a>
                        <?php if ($user['instagram_followed']): ?> ✅ <?php endif; ?>
                    </li>
                    <li>
                        <a href="https://linkedin.com/in/yourprofile" target="_blank" class="text-blue-700 underline" onclick="followSocial('linkedin')">
                            <i class="fab fa-linkedin"></i> Follow on LinkedIn
                        </a>
                        <?php if ($user['linkedin_followed']): ?> ✅ <?php endif; ?>
                    </li>
                </ul>
            </div>
        </main>
    </div>

    <script>
        function startMining() {
            fetch("mine.php")
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert("You can mine again after 24 hours.");
                        return;
                    }
                    document.getElementById("last-mined").innerHTML = data.last_mined;
                    document.getElementById("total-points").innerHTML = data.total_points + " Points";
                    document.getElementById("mine-btn").disabled = true;
                })
                .catch(error => console.error("Mining error:", error));
        }
    </script>
</body>
</html>
