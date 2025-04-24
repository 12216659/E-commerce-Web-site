<?php
session_start();
include 'includes/db.php'; // Ensure database connection

// Fetch slider images
$sliderStmt = $conn->query("SELECT * FROM slider_images ORDER BY id DESC");
$sliderImages = $sliderStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch products
$productStmt = $conn->query("SELECT * FROM products");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <link rel="stylesheet" href="css/style.css">
     
    <style>
        /* General Body and Layout */
    body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f7f7f7;
    color: #333;
    }

/* Header */
header {
    background-color:rgb(200, 10, 243);
    color: white;
    padding: 20px;
    text-align: center;
    position: relative;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    width: 100%; /* Ensures full width */
}

header h1 {
    margin: 0;
    font-size: 2em;
}

/* Updated navigation styling to align buttons in one row */
nav {
    display: flex;
    align-items: center; /* Align buttons vertically centered */
}

nav a, .logout-button {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    font-size: 1em;
    text-transform: uppercase;
    display: inline-block;
}

nav a:hover, .logout-button:hover {
    text-decoration: underline;
}

.logout-button {
    background-color: #ff5733;
    color: white;
    border:#f7f7f7;
    padding: 8px 12px;
    border-radius: 40px;
    cursor: pointer;
    transition: background-color 0.3s;
   
}

.logout-button:hover {
    background-color: #e84e2f;
}

/* Cart Link - Fixed beside Register button */
.cart-link {
    color: white;
    display: flex;
    align-items:center;
    text-decoration: none;
    font-size: 1.1em;
}

.cart-link:hover {
    color: #16a085;
}

.cart-icon {
    width: 25px;
    height: 25px;
    margin-right: 8px;
}

/* Main Container */
.main-container {
    padding: 20px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap; /* Allows responsiveness */
}

/* Product Listing */
.product-list {
    display: flex;
    flex-wrap: wrap;
    justify-content:center;
    gap: 20px;
    width: 80%;
    margin: auto;
}

.product {
    background-color: #fff;
    padding: 50px;
    box-shadow: 0 4px 10px rgba(0, 1, 0, 0.49);
    border-radius: 15px;
    text-align: center;
    width: 23%;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    margin-bottom: 20px;
    margin: auto;
    position: relative;
}

.product:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 50px rgb(106, 198, 132);
}

.product h3 {
    margin-bottom: 10px;
    font-size: 1.3em;
    color: #333;
}

.product p {
    font-size: 1em;
    color: #777;
    margin-bottom: 10px;
}

/* Image Responsive */
.product-image {
    width: 100%;
    max-height: 200px;
    object-fit: contain; /* Adjusts image proportionally */
    border-radius: 8px;
    margin: 10px 0;
}

/* Add to Cart Button */
.add-to-cart-button {
    background-color: #2ecc71;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 6px;
    font-size: 1.2em;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.add-to-cart-button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 300%;
    height: 300%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%) scale(0);
    border-radius: 50%;
    transition: transform 0.5s ease-out;
}

.add-to-cart-button:hover::before {
    transform: translate(-50%, -50%) scale(1);
}

.add-to-cart-button:hover {
    background-color: #27ae60;
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

/* Footer */
footer {
    background-color:rgb(237, 0, 229);
    color: white;
    text-align: center;
    padding: 20px 0;
    margin-top: 40px;
}

footer p {
    margin: 0;
    font-size: 1.1em;
}
        /* Image Scrolling Board */
        .scrolling-board {
            width: 90%;
            max-width: 1200px;
            overflow: hidden;
            margin: 30px auto;
            position: relative;
            border-radius: 15px;
            box-shadow: 0 6px 50px rgb(65, 144, 0);
        }

        .image-slider {
            display: flex;
            transition: transform 0.8s ease-in-out;
        }

        .image-slide {
            min-width: 100%;
            box-sizing: border-box;
        }

        .image-slide img {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: 15px;
        }

        /* Navigation buttons */
        .prev, .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            padding: 15px 20px;
            cursor: pointer;
            border-radius: 50%;
            font-size: 24px;
            transition: background 0.3s ease-in-out;
        }

        .prev { left: 20px; }
        .next { right: 20px; }

        .prev:hover, .next:hover {
            background-color: rgba(0, 0, 0, 0.9);
        }

        /* Dots for Navigation */
        .dots {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .dot {
            width: 12px;
            height: 12px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .dot.active {
            background: white;
        }

/* User Profile Container */
.user-profile {
    position: relative;
    display: inline-block;
}

/* Profile Picture Styling */
.profile-pic {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid white;
    transition: transform 0.2s ease-in-out;
}

.profile-pic:hover {
    transform: scale(1.1); /* Slight zoom effect on hover */
}

/* Profile Popup (Hidden by Default) */
.profile-popup {
    position: absolute;
    top: 180px; /* Appears below the profile picture */
    left: 2pc;
    background: white;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    padding: 10px;
    border-radius: 8px;
    width: 180px;
    display: none;
    z-index: 1000;
}

/* Show popup when hovering over profile picture or popup itself */
.user-profile:hover .profile-popup {
    display: block;
}

/* Profile Popup Links */
.profile-popup a {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: black;
    white-space: nowrap;
    font-size: 14px;
}

.profile-popup a:hover {
    background: #f0f0f0;
    border-radius: 5px;
}

/* Adjust positioning if it overflows */
@media screen and (max-width: 768px) {
    .profile-popup {
        right: -20px; /* Ensure it doesn’t overflow on smaller screens */
    }
}



    </style>
</head>
<body>

<header>
     <div class="header-container">
        <!-- User Profile Picture -->
        <div class="user-profile">
    <?php if (!empty($_SESSION['profile_pic'])) : ?>
        <img src="uploads/<?= htmlspecialchars($_SESSION['profile_pic']); ?>" alt="Profile" class="profile-pic" onclick="toggleProfilePopup()">
    <?php else : ?>
        <img src="images/default-avatar.png" alt="Default Profile" class="profile-pic" onclick="toggleProfilePopup()">
    <?php endif; ?>
</div>

    
        <h1>Welcome to Our Store</h1>
        <nav>
            
            <a href="pages/login.php">Login</a>
            <a href="pages/register.php">Register</a>
            <a href="pages/cart.php" class="cart-link">
                <img src="images/cart-icon.png" alt="Cart" class="cart-icon"> Cart
            </a>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-button">
                    <li><a href="pages/logout.php">Logout</a></li>
                </button>
            </form>
             
        </nav>
    </div>
</header>


<!-- Image Scrolling Board (Dynamic Slider) -->
<div class="scrolling-board">
    <div class="image-slider">
        <?php if (!empty($sliderImages)) : ?>
            <?php foreach ($sliderImages as $slide) : ?>
                <div class="image-slide">
                    <img src="images/<?= htmlspecialchars($slide['image_name']); ?>" alt="Slider Image">
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p style="text-align:center; padding: 20px;">No slider images available.</p>
        <?php endif; ?>
    </div>
    <?php if (count($sliderImages) > 1) : ?>
        <button class="prev" onclick="prevSlide()">&#10094;</button>
        <button class="next" onclick="nextSlide()">&#10095;</button>
    <?php endif; ?>
    <div class="dots"></div>
</div>

<div class="main-container">
    <main>
        <h2>Products</h2>
        <div class="product-list">
            <?php if (empty($products)) : ?>
                <p>No products available.</p>
            <?php else : ?>
                <?php foreach ($products as $product) : ?>
                    <div class="product">
                        <h3><?= htmlspecialchars($product['name']); ?></h3>
                        <p>Price: $<?= number_format($product['price'], 2); ?></p>
                        <p><?= htmlspecialchars($product['description']); ?></p>
                        <?php if (!empty($product['image'])) : ?>
                            <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                        <?php endif; ?>
                        <form method="POST" action="pages/cart.php">
                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<footer>
    <p>&copy; <?= date('Y'); ?> Online Store. All rights reserved.</p>
</footer>

<script>
    let index = 0;
    const slides = document.querySelectorAll(".image-slide");
    const totalSlides = slides.length;
    const dotsContainer = document.querySelector(".dots");

    // Create dots dynamically based on number of slides
    slides.forEach((_, i) => {
        const dot = document.createElement("div");
        dot.classList.add("dot");
        dot.addEventListener("click", () => showSlide(i));
        dotsContainer.appendChild(dot);
    });

    function updateDots() {
        const dots = document.querySelectorAll(".dot");
        dots.forEach((dot, i) => {
            dot.classList.toggle("active", i === index);
        });
    }

    function showSlide(i) {
        const slider = document.querySelector(".image-slider");
        index = (i + totalSlides) % totalSlides; // Keep index within bounds
        slider.style.transform = `translateX(-${index * 100}%)`;
        updateDots();
    }

    function nextSlide() {
        showSlide(index + 1);
    }

    function prevSlide() {
        showSlide(index - 1);
    }

    // Auto-scroll every 4 seconds
    if (totalSlides > 1) {
        setInterval(nextSlide, 4000);
    }

    // Initialize first dot as active
    updateDots();
    </script>
    <script>
function toggleProfilePopup() {
    let profilePopup = document.getElementById("profilePopup");
    profilePopup.style.display = profilePopup.style.display === "block" ? "none" : "block";
}
</script>

<!-- Profile Menu Popup -->
<div id="profilePopup" class="profile-popup" style="display: none;">
    <a href="pages/edit_profile.php">Edit Profile</a>
    <a href="view_orders.php">View Orders</a>
    <a href="address.php">Manage Address</a>
</div>

</body>
</html>
