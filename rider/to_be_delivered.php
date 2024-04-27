<?php
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

// Get order details based on the provided ID
if (isset($_GET['id'])) {
    $orderId = $_GET['id'];
    
    $sql = "SELECT o.id, o.user_id, CONCAT(u.firstname, ' ', u.lastname) AS name, CONCAT(u.province, ', ', u.city, ', ', u.barangay, ', ', u.additional_address) AS address,
            u.contact, o.payment_method, o.ref_code, o.total_amount, o.order_date, o.order_status, o.delivery_image 
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    // Check if order status is delivered to hide the upload form
    if ($order['order_status'] === 'Delivered') {
        $uploadHidden = true;
    } else {
        $uploadHidden = false;
    }
} else {
    die("Invalid request.");
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_order'])) {
    $image = file_get_contents($_FILES['image']['tmp_name']);
    
    $updateSql = "UPDATE orders SET order_status = 'Delivered', delivery_image = ? WHERE id = ?";
            
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $image, $orderId);
    $stmt->send_long_data(0, $image);
    $stmt->execute();
    
    // Refresh the page after updating
    header("Location: {$_SERVER['PHP_SELF']}?id={$orderId}");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Back button -->
    <a href="dashboard.php" class="mt-4 inline-block  text-green-500 px-4 py-2 rounded hover:bg-green-600">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="bg-white rounded-lg mt-5 shadow-md mx-auto p-4 max-w-screen-md">

        <h2 class="text-xl font-semibold mb-4 text-green-600">Order Details</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <tbody>
                    <tr>
                        <td class="border px-4 py-2 font-semibold">Name</td>
                        <td class="border px-4 py-2"><?php echo $order['name']; ?></td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2 font-semibold">Address</td>
                        <td class="border px-4 py-2"><?php echo $order['address']; ?></td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2 font-semibold">Contact</td>
                        <td class="border px-4 py-2"><?php echo $order['contact']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <ul>
                <li><strong>COD Amount:</strong> ₱<?php echo $order['total_amount']; ?></li>
                <li><strong>Order Status:</strong> <?php echo $order['order_status']; ?></li>
            </ul>
        </div>

        <!-- Form to upload image and update order status -->
        <form method="post" enctype="multipart/form-data" <?php if ($uploadHidden) echo 'hidden'; ?>>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Upload Image</label>
                <input type="file" name="image" required>
            </div>
            <button type="submit" name="confirm_order" class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Order Delivered
            </button>
        </form>

    </div>

</body>
</html>
