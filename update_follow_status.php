<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You need to log in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$platform = $_POST['platform'];
$column = "";

switch ($platform) {
    case "facebook": $column = "facebook_followed"; break;
    case "twitter": $column = "twitter_followed"; break;
    case "instagram": $column = "instagram_followed"; break;
    case "linkedin": $column = "linkedin_followed"; break;
    default:
        echo json_encode(["success" => false, "message" => "Invalid platform"]);
        exit;
}

// Check if the user has already followed
$query = "SELECT $column FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row[$column] == 1) {
    echo json_encode(["success" => false, "message" => "Already followed"]);
    exit;
}

// Update follow status and add points
$query = "UPDATE users SET $column = 1, total_points = total_points + 10 WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Followed successfully!"]);
?>
