<?php
// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$full_name = $_SESSION['full_name'];
include 'inc/logodisplay.php';
?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css">
</head>

<!-- Sidebar -->
<div class="bg-green-600 text-black w-64 space-y-6 fixed top-0 left-0 h-full font-sans" style="background-color: #54CC7B;">
    <!-- Logo and Title -->
    <div class="flex flex-row items-center justify-center bg-gray-800 p-4 ">
        <img src="data:image/jpeg;base64,<?php echo $logoBase64; ?>" alt="Logo" class="h-10 mr-2">

  
        <span class="text-xl text-white"><?php echo $shortName; ?></span>
    </div>

    <!-- Navigation Links -->
    <nav>
        <ul class="">
            <li>
                <a href="dashboard.php" class="flex items-center px-4 py-2 hover:bg-gray-800 hover:text-red-500 rounded transition duration-200">
                    <i class="bi bi-speedometer2 mr-2"></i>Dashboard
                </a>
            </li>
            <li>
                <a href="product_list.php" class="flex items-center px-4 py-2 hover:bg-gray-800 hover:text-red-500 rounded transition duration-200">
                    <i class="bi bi-bag-fill mr-2"></i>Product List
                </a>
            </li>
            <li>
                <a href="order_list.php" class="flex items-center px-4 py-2 hover:bg-gray-800 hover:text-red-500 rounded transition duration-200">
                    <i class="bi bi-receipt mr-2"></i>Order List
                </a>
            </li>
            <li>
                <a href="client_list.php" class="flex items-center px-4 py-2 hover:bg-gray-800 hover:text-red-500 rounded transition duration-200">
                    <i class="bi bi-people-fill mr-2"></i>User List
                </a>
            </li>
            <li>
                <a href="sales_report.php" class="flex items-center px-4 py-2 hover:bg-gray-800 hover:text-red-500 rounded transition duration-200">
                    <i class="bi bi-bar-chart-line mr-2"></i>Sales Report
                </a>
            </li>
            <li>
                <a href="brand_list.php" class="flex items-center px-4 py-2 hover:bg-gray-800 hover:text-red-500 rounded transition duration-200">
                    <i class="bi bi-tag-fill mr-2"></i>Brand List
                </a>
            </li>
            <li>
                <a href="category_list.php" class="flex items-center px-4 py-2 hover:bg-gray-800 hover:text-red-500 rounded transition duration-200">
                    <i class="bi bi-list-task mr-2"></i>Category List
                </a>
            </li>
            <li>
                <a href="settings.php" class="flex items-center px-4 py-2 hover:bg-gray-800 hover:text-red-500 rounded transition duration-200">
                    <i class="bi bi-gear-fill mr-2"></i>Settings
                </a>
            </li>
            <li>
                <a href="logout.php" class="flex items-center px-4 py-2 hover:bg-gray-800 hover:text-red-500 rounded transition duration-200">
                    <i class="bi bi-box-arrow-right mr-2"></i>Logout
                </a>
            </li>
        </ul>
    </nav>
</div>
