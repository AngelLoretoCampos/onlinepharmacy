<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <!-- Assuming you have Font Awesome included in your project -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<?php include 'inc/sidebar.php'?>

<body class="bg-gray-100 ml-64">
    <div class="container bg-white pb-10 px-10 ">
        <div class=" bg-white mt-10 grid  grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Total Sales Card -->
                <div class="bg-white mt-5 rounded-lg shadow-md flex items-center justify-between">
                    <div class="pl-5">
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
                    <div class="text-green-500 mr-5">
                        <i class="fas fa-coins fa-3x"></i>
                    </div>
                </div>

                <!-- Total Users Card -->
                <div class="bg-white mt-5 rounded-lg shadow-md p-6 flex items-center justify-between">
                    <div>
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
                    <div class="text-blue-500 mr-5">
                        <i class="fas fa-user fa-3x"></i>
                    </div>
                </div>

                <!-- Total Products Card -->
                <div class="bg-white mt-5  rounded-lg shadow-md p-6 flex items-center justify-between">
                    <div>
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
                    <div class="text-purple-500 mr-5">
                        <i class="fas fa-boxes-stacked fa-3x"></i>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-10">
                <!-- Total Revenue Chart -->
                <div class="w-3/4 border shadow-lg rounded-md justify-between p-5">
                    <canvas id="totalRevenueChart"></canvas>
                </div>
            </div>

            <script>
                // JavaScript for the chart
                document.addEventListener("DOMContentLoaded", function () {
                    var ctx = document.getElementById('totalRevenueChart').getContext('2d');
                    var chart = new Chart(ctx, {
                        // The type of chart: 'bar', 'horizontalBar', 'pie', 'line', 'doughnut', 'radar', 'polarArea'
                        type: 'line',

                        // The data for our dataset
                        data: {
                            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                            datasets: [{
                                label: 'Total Revenue',
                                backgroundColor: 'rgba(40, 167, 69, 0.2)', // Green background color
                                borderColor: 'rgba(40, 167, 69, 1)', // Change color as needed
                                borderWidth: 1,
                                data: [] // Data will be fetched dynamically
                            }]
                        },

                        // Configuration options
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Fetching data from the database for the chart
                    <?php
                    $weekdays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                    $revenues = array_fill(0, 7, 0); // Initialize revenues array with zeros

                    try {
                        $stmt = $pdo->query("SELECT DAYOFWEEK(order_date) AS day, SUM(total_amount) AS revenue FROM orders WHERE order_status = 'Delivered' GROUP BY DAYOFWEEK(order_date)");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $day = $row['day'] - 1; // Adjusting index to match array
                            $revenues[$day] = $row['revenue'];
                        }
                    } catch (PDOException $e) {
                        echo "console.error('Error: " . $e->getMessage() . "');";
                    }

                    // Convert PHP arrays to JavaScript arrays
                    echo "chart.data.datasets[0].data = " . json_encode($revenues) . ";";
                    echo "chart.update();";
                    ?>
                });
            </script>
        </div>
    </div>

</body>

</html>
