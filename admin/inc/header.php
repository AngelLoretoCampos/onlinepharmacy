<?php
include '../database/dbconnection.php';

session_start();

$user_name = "";
$login_logout_text = "";
$login_logout_url = "";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE id = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_name = $user['full_name'];
    $login_logout_text = "Logout";
    $login_logout_url = "logout.php";
} else {
    $user_name = "";
    $login_logout_text = "Login";
    $login_logout_url = "login.php";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Header</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Include FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-green-500 text-white p-4 flex justify-between items-center">
    <div class="flex items-center">
        <img src="system images/bgpp 1.png" alt="Logo" class="h-24 mr-10">
        
        <!-- Search bar -->
        <div class="relative">
            <input type="text" placeholder="Search..." class="border rounded-md px-3 py-1 w-96 placeholder-gray-400">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                <button type="submit" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <i class="fas fa-search"></i>
                </button>
            </span>
        </div>
    </div>

    <nav>
        <ul class="flex space-x-6">
            <li><a href="dashboard.php" class="text-black-500 hover:text-black-700">Home</a></li>
            <?php if (!empty($user_name)): ?>
                <li><a href="#" class="text-black-500 hover:text-black-700"><?php echo $user_name; ?></a></li>
            <?php endif; ?>
            <li><a href="<?php echo $login_logout_url; ?>" class="text-black-500 hover:text-black-700"><?php echo $login_logout_text; ?></a></li>
            <?php if (empty($user_name)): ?>
                <li><a href="register.php" class="text-black-500 hover:text-black-700">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

</body>
</html>