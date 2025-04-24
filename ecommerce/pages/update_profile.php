<?php
session_start();
var_dump($_SESSION);
exit();

session_start();
include("../test_db.php");

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Error: User not logged in! Please log in again.'); window.location.href='../login.php';</script>";
    exit();
}

$email = $_SESSION['email'];

// Debugging: Check if session exists
if (empty($email)) {
    echo "<script>alert('Error: Session email is empty! Please log in again.'); window.location.href='../login.php';</script>";
    exit();
}

try {
    // Fetch user details
    $query = $conn->prepare("SELECT profile_pic FROM users WHERE email = ?");
    $query->execute([$email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<script>alert('Error: User not found!'); window.location.href='../dashboard.php';</script>";
        exit();
    }

    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Validate file type and size
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        $file_size = $_FILES['profile_pic']['size'];

        if (!in_array($file_extension, $allowed_types)) {
            echo "<script>alert('Error: Invalid file type. Only JPG, PNG, and GIF allowed.'); window.location.href='../edit_profile.php';</script>";
            exit();
        }
        if ($file_size > 2 * 1024 * 1024) { // 2MB limit
            echo "<script>alert('Error: File size must be less than 2MB.'); window.location.href='../edit_profile.php';</script>";
            exit();
        }

        // Generate unique filename
        $new_filename = uniqid("profile_", true) . "." . $file_extension;
        $target_file = $upload_dir . $new_filename;

        // Move file to uploads folder
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            // Delete old profile pic if not default
            if ($user['profile_pic'] !== 'default-avatar.png' && file_exists($upload_dir . $user['profile_pic'])) {
                unlink($upload_dir . $user['profile_pic']);
            }

            // Update profile picture in database
            $updateQuery = $conn->prepare("UPDATE users SET profile_pic = ? WHERE email = ?");
            $updateQuery->execute([$new_filename, $email]);
        } else {
            echo "<script>alert('Error: Failed to upload profile picture.'); window.location.href='../edit_profile.php';</script>";
            exit();
        }
    }

    // Update phone and address
    if (!empty($_POST['phone']) && !empty($_POST['address'])) {
        $phone = htmlspecialchars($_POST['phone']);
        $address = htmlspecialchars($_POST['address']);

        $updateQuery = $conn->prepare("UPDATE users SET phone = ?, address = ? WHERE email = ?");
        $updateQuery->execute([$phone, $address, $email]);
    }

    // Show success message and redirect
    echo "<script>
        alert('Profile updated successfully!');
        window.location.href='../index.php';
    </script>";
    exit();
} catch (PDOException $e) {
    echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "'); window.location.href='../edit_profile.php';</script>";
    exit();
}
?>
