<?php
session_start();
include 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit;
}

$user_id = $_SESSION['user_id'];

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Mining Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- External CSS file -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: #f4f4f4;
            color: #333;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #222;
            color: white;
            padding: 20px;
            position: fixed;
            left: -250px;
            transition: 0.3s;
            top: 0;
        }
        .sidebar.active {
            left: 0;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style: none;
        }
        .sidebar ul li {
            padding: 15px;
            border-bottom: 1px solid #444;
            cursor: pointer;
        }
        .sidebar ul li:hover {
            background: #333;
        }
        .hamburger {
            display: none;
            font-size: 24px;
            cursor: pointer;
            padding: 15px;
        }
        .header {
            width: 100%;
            background: #fff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .main-content {
            margin-top: 80px;
            padding: 20px;
            width: 100%;
            transition: 0.3s;
            text-align: center;
        }
        .profile {
            position: relative;
        }
        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }
        .dropdown {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            width: 150px;
            border-radius: 5px;
            overflow: hidden;
        }
        .dropdown a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
        }
        .dropdown a:hover {
            background: #ddd;
        }
        .profile:hover .dropdown {
            display: block;
        }
        .dashboard {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 30%;
            min-width: 250px;
            text-align: center;
        }
        .mine-button {
            background: #28a745;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
        }
        .mine-button:hover {
            background: #218838;
        }
        @media screen and (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .hamburger {
                display: block;
            }
            .dashboard {
                flex-direction: column;
                align-items: center;
            }
            .card {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h2>Welcome, User</h2>
        <div class="dashboard">
            <div class="card">
                <h3>Last Mined</h3>
                <p id="last-mined"><?php echo $last_mined; ?></p>
            </div>
            <div class="card">
                <h3>Points Earned</h3>
                <p id="total-points"><?php echo $points_earned; ?> Points</p>
            </div>
            <div class="card">
                <h3>Mining Status</h3>
                <p id="mining-status">Active</p>
            </div>
        </div>
        <button class="mine-button" onclick="startMining()" <?php echo $can_mine ? '' : 'disabled'; ?>>Start Mining</button>
        <?php if (!$can_mine): ?>
            <p style="color: red;">You can mine again after 24 hours.</p>
        <?php endif; ?>
    </div>

    <script>
        function startMining() {
            fetch("mine.php")
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById("last-mined").innerHTML = data.last_mined;
                    document.getElementById("total-points").innerHTML = data.total_points + " Points";
                })
                .catch(error => console.error("Mining error:", error));
        }
    </script>
</body>
</html>