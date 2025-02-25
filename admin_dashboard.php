<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all users with their mined points
$sql = "SELECT u.id, u.username, u.status, COALESCE(m.points_earned, 0) AS points
        FROM users u
        LEFT JOIN mining m ON u.id = m.user_id";

$result = $conn->query($sql);
?>

<h2>Admin Dashboard</h2>
<table border="1">
    <tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Points</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['points'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <?php if ($row['status'] == 'active') { ?>
                    <a href="block_user.php?id=<?= $row['id'] ?>">Block</a>
                <?php } else { ?>
                    <a href="unblock_user.php?id=<?= $row['id'] ?>">Unblock</a>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>
<a href="logout.php">Logout</a>
