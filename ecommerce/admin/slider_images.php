<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$message = ""; // Variable to store success or error messages

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_slide'])) {
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($image_ext, $allowed_ext)) {
            $message = "<p style='color: red;'>Invalid file format. Only JPG, JPEG, PNG, and GIF are allowed.</p>";
        } elseif ($image_size > 5 * 1024 * 1024) { // 5MB limit
            $message = "<p style='color: red;'>File size must be less than 5MB.</p>";
        } else {
            $new_image_name = time() . "_" . $image; // Rename file to avoid duplicates
            $upload_path = "../images/" . $new_image_name;

            if (move_uploaded_file($image_tmp, $upload_path)) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO slider_images (image_name) VALUES (?)");
                $stmt->execute([$new_image_name]);
                $message = "<p style='color: green;'>Slider image added successfully!</p>";
            } else {
                $message = "<p style='color: red;'>Failed to upload image.</p>";
            }
        }
    } else {
        $message = "<p style='color: red;'>Please select an image to upload.</p>";
    }
}

// Fetch slider images
$sliderStmt = $conn->query("SELECT * FROM slider_images ORDER BY id DESC");
$sliderImages = $sliderStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle image deletion
if (isset($_POST['delete_slide'])) {
    $image_id = $_POST['image_id'];
    $image_name = $_POST['image_name'];
    $image_path = "../images/" . $image_name;
    
    if (file_exists($image_path)) {
        if (unlink($image_path)) {
            $deleteStmt = $conn->prepare("DELETE FROM slider_images WHERE id = ?");
            if ($deleteStmt->execute([$image_id])) {
                $message = "<p style='color: green;'>Slider image deleted successfully!</p>";
            } else {
                $message = "<p style='color: red;'>Failed to delete image from database.</p>";
            }
        } else {
            $message = "<p style='color: red;'>Failed to delete image file.</p>";
        }
    } else {
        $message = "<p style='color: red;'>Image file does not exist.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Slider Images</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007BFF;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: #007BFF;
        }
        .slider-images {
            margin-top: 30px;
        }
        .slider-images img {
            width: 100px;
            height: auto;
            margin: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .slider-images form {
            display: inline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Slider Images</h2>
        
        <div class="message">
            <?= $message; ?>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <label for="image">Select Image:</label>
            <input type="file" name="image" id="image" required>
            <button type="submit" name="add_slide">Add Image</button>
        </form>

        <div class="slider-images">
            <h3>Current Slider Images</h3>
            <?php if (!empty($sliderImages)) : ?>
                <?php foreach ($sliderImages as $slide) : ?>
                    <div>
                        <img src="../images/<?= htmlspecialchars($slide['image_name']); ?>" alt="Slider Image">
                        <form method="POST">
                            <input type="hidden" name="image_id" value="<?= $slide['id']; ?>">
                            <input type="hidden" name="image_name" value="<?= htmlspecialchars($slide['image_name']); ?>">
                            <button type="submit" name="delete_slide" style="background-color: red;">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No slider images available.</p>
            <?php endif; ?>
        </div>

        <div class="back-link">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
