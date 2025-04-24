<?php
session_start();
include '../includes/db.php'; // Ensure correct database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in. Please log in again.");
}

$userId = $_SESSION['user_id'];

try {
    // Prepare and execute query safely
    $stmt = $conn->prepare("SELECT username, email, phone, address, profile_pic FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>Edit Your Profile</h2>
    
    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <label>Profile Picture:</label>
        <input type="file" name="profile_pic">
        <?php if (!empty($user['profile_pic'])): ?>
            <img src="../images/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture" width="100">
        <?php endif; ?>

        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Phone:</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

        <label>Address:</label>
        <textarea name="address" required><?= htmlspecialchars($user['address']) ?></textarea>

        <button type="submit" name="update_profile">Save Changes</button>
    </form>
</body>
</html>
