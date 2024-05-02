<?php
include 'database/dbconnection.php';

// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_name = "";
$login_logout_text = "";
$login_logout_url = "";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_name = $user['firstname'];
    $login_logout_text = "Logout";
    $login_logout_url = "logout.php";
} else {
    $user_name = "";
    $login_logout_text = "Login";
    $login_logout_url = "login.php";
}

// Fetch the first four categories from the database
$stmt = $conn->prepare("SELECT categoryName FROM category LIMIT 5");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch logo image from the database
$stmt = $conn->prepare("SELECT image FROM systemsetting");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$image = $row['image']; // Assuming the logo is stored as a longblob in the database

// Convert the binary data to base64 encoding
if ($image) {
    $logoBase64 = base64_encode($image);
}

// Fetch the first four categories from the database
$stmt = $conn->prepare("SELECT categoryName FROM category LIMIT 5");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lyfe Pharmacy</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Include FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<header class="bg-green-500 text-white p-4 flex justify-between items-center fixed top-0 left-0 w-full z-50">
    <div class="flex items-center">
        <a href="dashboard.php">
            <img src="data:image/jpeg;base64,<?php echo $logoBase64; ?>" alt="Logo" class="h-24 mr-10">
        </a>
        <!-- Search bar -->
        <form action="dashboard.php" method="GET" class="relative">
            <input type="text" name="query" placeholder="Search..." class="border text-black rounded-md px-3 py-1 w-96 placeholder-gray-400">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                <button type="submit" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <i class="fas fa-search"></i>
                </button>
            </span>
        </form>
    </div>

    <nav>
        <ul class="flex space-x-6">
            <?php foreach ($categories as $category): ?>
                <li><a href="dashboard.php?category=<?php echo urlencode($category); ?>" class="text-black-500 hover:text-black-700"><?php echo $category; ?></a></li>
            <?php endforeach; ?>
            <li><a href="all_categories.php" class="text-black-500 hover:text-black-700">All Categories</a></li>
            <?php if (!empty($user_name)): ?>
                <div>|</div>
                <li><a href="dashboard.php" class="text-black-500 hover:text-black-700">Home</a></li>
                <li><a href="profile_manager.php" class="text-black-500 hover:text-black-700"><?php echo $user_name; ?></a></li>
                <li><a href="cart.php" class="text-black-500 hover:text-black-700"><i class="fas fa-shopping-cart"></i></a></li>
                <li><a href="logout.php" class="text-black-500 hover:text-black-700"><i class="fas fa-sign-out-alt"></i></a></li>
            <?php else: ?>
            <div>|</div>
                <li><a href="dashboard.php" class="text-black-500 hover:text-black-700">Home</a></li>
                <li><a href="login.php" class="text-black-500 hover:text-black-700">Login</a></li>
                <li><a href="register.php" class="text-black-500 hover:text-black-700">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

</body>
</html>
