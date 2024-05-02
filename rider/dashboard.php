<?php

// Start session
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['rider_id'])) {
    header("Location: login.php");
    exit;
}


// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinepharmacy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch data from the onlinepharmacy_db table
$sql = "SELECT o.id, o.user_id, CONCAT(u.firstname, ' ', u.lastname) AS name, CONCAT(u.province, ', ', u.city, ', ', u.barangay, ', ', u.additional_address) AS address, 
        o.payment_method, o.ref_code, o.total_amount, o.order_date, o.order_status 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.order_status = 'Out for Delivery'";



$result = $conn->query($sql);

// Fetch logo image from the database
$sqlLogo = "SELECT image FROM systemsetting";
$resultLogo = $conn->query($sqlLogo);
$rowLogo = $resultLogo->fetch_assoc();
$imageData = $rowLogo['image']; // Assuming the image is stored as a longblob in the database

// Convert image data to base64 encoding
$logoBase64 = base64_encode($imageData);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Pharmacy Orders</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex justify-between items-center px-4 py-2">
    <img src="data:image/jpeg;base64,<?php echo $logoBase64; ?>" alt="Lyfe Pharmacy Logo" class="h-20">
        <!-- History and Logout icons -->
        <div class="ml-auto flex space-x-4">
            <a href="history.php" class="inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                <i class="fas fa-history"></i>
            </a>
            <a href="logout.php" class="inline-block bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md mx-auto p-4 max-w-screen-md mt-4">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-green-600">Lyfe Pharmacy (Out for Delivery)</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-green-500 text-white">
                    <tr>
                        <th class="px-4 py-2">Payment Method</th>
                        <th class="px-4 py-2">Total Amount</th>
                        <th class="px-4 py-2">Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='border-b'>";
                            echo "<td class='border text-center px-4 py-2 sm:table-cell'>" . $row["payment_method"] . "</td>";
                            echo "<td class='border text-center px-4 py-2 sm:table-cell'>₱" . $row["total_amount"] . "</td>";
                            echo "<td class='border text-center px-4 py-2 sm:table-cell'><a href='to_be_delivered.php?id=" . $row["id"] . "' class='text-blue-500 hover:underline'>View</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='border px-4 py-2 text-center'>No records with status 'Out for Delivery' found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    // Close the connection
    $conn->close();
    ?>

</body>
</html>
