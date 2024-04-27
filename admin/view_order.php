<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinepharmacy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order details
    $stmt = $conn->prepare("SELECT o.*, CONCAT(u.firstname, ' ', u.lastname) as name, CONCAT(u.province, ', ', u.city, ', Albay, ', u.additional_address) as address, u.contact FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order_result = $stmt->get_result();

    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();

        // Fetch order items
        $stmt = $conn->prepare("SELECT oi.product_name, oi.quantity, oi.price FROM order_items oi WHERE oi.order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $items_result = $stmt->get_result();
    } else {
        echo "Order not found!";
        exit;
    }
} else {
    echo "Order ID not provided!";
    exit;
}

// Include sidebar
include 'inc/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Order</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 ml-64">

<main>
    <div class="container mt-20">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl text-center font-semibold mb-4">Order Details</h1>
            
            <!-- Customer Details -->
            <div class="mb-4">
                <h2 class="text-xl font-semibold mb-2">Customer Details</h2>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($order['contact']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
            </div>

            <!-- Prescription Image -->
            <div class="mb-4">
                <h2 class="text-xl font-semibold mb-2">Prescription Image:</h2>
                <?php if (!empty($order['prescription_image'])) {
                    $imageData = base64_encode($order['prescription_image']);
                    $imageSrc = 'data:image/jpeg;base64,' . $imageData;
                ?>
                    <img src="<?php echo $imageSrc; ?>" alt="Prescription Image" class="w-32 h-auto mb-4 cursor-pointer" onclick="showModal('<?php echo $imageSrc; ?>')">
                <?php } else { ?>
                    <p>No prescription image available.</p>
                <?php } ?>
            </div>



            <!-- Order Items -->
            <h2 class="text-xl font-semibold mb-2">Order Items</h2>
            <table class="min-w-full mb-4">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">Product Name</th>
                        <th class="border px-4 py-2">Price</th>
                        <th class="border px-4 py-2">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $items_result->fetch_assoc()) { ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td class="border px-4 py-2">₱ <?php echo htmlspecialchars($item['price']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($item['quantity']); ?></td>
                        </tr>
                    <?php } ?>
                        <tr>
                            <td class="border-l border-b px-4 py-2"></td>
                            <td class="border-b border-r  px-4 py-2">Total Amount:</td>
                            <td class="border px-4 py-2"><strong>₱ <?php echo htmlspecialchars($order['total_amount']); ?></strong></td>
                        </tr>
                </tbody>
            </table>
            <!-- Update Order Status Form -->
            <div class="mb-4">
                <table class="item-center">
                        <tr >
                            <!-- Order Status -->
                            <td class="pl-10 pr-10">
                                <div class="mb-2">
                                    <h2 class="text-xl font-semibold mb-2">Order Status: <small><?php echo htmlspecialchars($order['order_status']); ?></small></h2>
                                    <?php if ($order['order_status'] != 'Cancelled' && $order['order_status'] != 'Delivered') { ?>
                                        <form action="update_order_status.php" method="post">
                                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                            <select name="order_status" class="mr-4">
                                                <option value="Packed" <?php echo ($order['order_status'] == 'Packed') ? 'selected' : ''; ?>>Packed</option>
                                                <option value="Out for Delivery" <?php echo ($order['order_status'] == 'Out for Delivery') ? 'selected' : ''; ?>>Out for Delivery</option>
                                                <option value="Cancelled" <?php echo ($order['order_status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update Status</button>
                                        </form>
                                    <?php } ?>
                                </div>
                            </td>
                            <!-- Reason for Cancellation -->
                            <td class="pl-10 pr-10">
                                <?php if ($order['order_status'] == 'Cancelled') { ?>
                                    <div class="mb-2">
                                        <h2 class="text-xl font-semibold mb-2">Reason for Cancellation: <small><?php echo htmlspecialchars($order['cancellation_reason']); ?></small></h2>
                                        <?php if (empty($order['cancellation_reason'])) { ?>
                                            <h2 class="text-xl font-semibold mb-2">Select Reason:</h2>
                                            <form action="update_cancellation_reason.php" method="post">
                                                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                                <select name="cancellation_reason" class="mr-4">
                                                    <option value="Unreadable/Illegible letters">Unreadable/Illegible letters (please go back to the doctor to have a new clear written prescription)</option>
                                                    <option value="Blurry Image">Blurry Image</option>
                                                    <!-- Add more choices here if needed -->
                                                </select>
                                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Send</button>
                                            </form>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </td>
                            <td>
                                
                            <!-- Delivery Image -->
                            <div class="mb-4">
                                <h2 class="text-xl font-semibold mb-2">Delivery Image</h2>
                                <?php if (!empty($order['delivery_image'])) {
                                    $imageData = base64_encode($order['delivery_image']);
                                    $imageSrc = 'data:image/jpeg;base64,' . $imageData;
                                ?>
                                    <img src="<?php echo $imageSrc; ?>" alt="Delivery Image" class="w-32 h-auto mx-auto mb-4 cursor-pointer" onclick="showModal('<?php echo $imageSrc; ?>')">
                                <?php } else { ?>
                                    <p>No delivery image available.</p>
                                <?php } ?>  
                            </div>
                            </td>
                        </tr>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative p-4 bg-white rounded-lg">
                <span class="absolute top-0 right-0 mt-4 mr-4 text-2xl cursor-pointer" onclick="hideModal()">×</span>
                <img id="modalImage" src="" alt="Modal Image" class="w-full h-auto">
            </div>
        </div>
    </div>

    <script>
        function showModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');

            modalImage.src = imageSrc;
            modal.classList.remove('hidden');
        }

        function hideModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
        }
    </script>

</main>

<?php
$stmt->close();
$conn->close();
?>

</body>
</html>
