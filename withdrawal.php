<?php
// fetch_points.php
session_start();
include 'config.php'; // Include database connection file

$user_id = $_SESSION['user_id']; // to ensure the user is logged in

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$sql = "SELECT points_earned FROM mining WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($points);
$stmt->fetch();
$stmt->close();
$conn->close();

echo json_encode(['points' => $points]);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal | Hezzy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    
    <div class="bg-gray-800 text-white p-6 rounded-xl shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-4">Withdraw Funds</h2>
        
        <!-- Points Balance -->
        <div class="bg-gray-700 p-3 rounded-lg mb-4 flex justify-between">
            <span>Your Points:</span>
            <span id="pointsBalance" class="font-bold text-white">Loading...</span>
        </div>

        <!-- Crypto Exchange Address -->
        <label class="block mb-2 text-gray-300">Crypto Exchange Address</label>
        <input type="text" id="cryptoAddress" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your wallet address">
        
        <!-- Memo ID -->
        <label class="block mt-4 mb-2 text-gray-300">Memo ID</label>
        <input type="text" id="memoId" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Optional Memo ID">
        
        <!-- Converted Amount -->
        <div class="mt-4 bg-gray-700 p-3 rounded-lg flex justify-between">
            <span>Amount in $Hezzy:</span>
            <span id="hezzyAmount" class="font-bold text-green-400">$10</span>
        </div>

        <!-- Withdraw Button -->
        <button onclick="processWithdrawal()" class="w-full mt-5 bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg font-bold transition">
            Withdraw
        </button>
    </div>

    <script>
        function processWithdrawal() {
            let cryptoAddress = document.getElementById("cryptoAddress").value;
            if (cryptoAddress === "") {
                alert("Please enter your crypto exchange address.");
                return;
            }
            alert("Withdrawal request submitted successfully!");
        }

        function fetchPoints() {
            fetch('withdrawal.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById("pointsBalance").textContent = data.points || 0;
                })
                .catch(error => {
                    console.error("Error fetching points:", error);
                });
        }

        // Load points on page load
        fetchPoints();

    </script>

</body>
</html>
