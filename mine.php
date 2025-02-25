<?php
session_start();
include 'config.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$mining_points = 5; // Points per mining session

// Insert new mining record
$query = "INSERT INTO mining (user_id, points_earned, last_mined) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $mining_points);
$stmt->execute();

// Calculate total points earned by the user
$query = "SELECT SUM(points_earned) AS total_points FROM mining WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_points = $row['total_points'] ?? 0;

// Get the last mined time
$query = "SELECT last_mined FROM mining WHERE user_id = ? ORDER BY last_mined DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$last_mined = $row['last_mined'] ?? "Never";

// Return updated data as JSON
echo json_encode([
    "last_mined" => $last_mined,
    "total_points" => $total_points
]);
?>
