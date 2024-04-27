<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>

<?php include 'inc/sidebar.php'?>

<body class="bg-gray-100 ">
    <div class="container mx-auto ml-64 px-10 bg-white mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Sales Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Total Sales</h2>
            <?php
            // Database connection
            $host = 'localhost';
            $dbname = 'onlinepharmacy_db';
            $username = 'root';
            $password = '';

            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Calculate total sales amount
                $stmt = $pdo->query("SELECT SUM(total_amount) as total_sales FROM orders WHERE order_status = 'Delivered'");
                $total_sales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];

                // If there are no sales, display 0
                $total_sales = $total_sales ? $total_sales : 0;

                echo "<p class='text-3xl font-bold text-green-500'>₱ $total_sales</p>";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </div>

        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Total Users</h2>
            <?php
            try {
                // Count total users
                $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
                $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

                echo "<p class='text-3xl font-bold text-blue-500'>$total_users</p>";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </div>

        <!-- Total Products Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Total Products</h2>
            <?php
            try {
                // Count total products
                $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
                $total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

                echo "<p class='text-3xl font-bold text-purple-500'>$total_products</p>";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </div>
    </div>
</body>

</html>
