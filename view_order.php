<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinepharmacy_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['order_id'])) {
        $order_id = $_GET['order_id'];

        // Fetch order details
        $stmt = $conn->prepare("SELECT o.*, CONCAT(u.firstname, ' ', u.lastname) as name, CONCAT(u.province, ', ', u.city, ', Albay, ', u.additional_address) as address, u.contact FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if order is found
        if ($order) {
            // Fetch order items with product names
            $stmt = $conn->prepare("SELECT oi.product_name, oi.quantity, oi.price FROM order_items oi WHERE oi.order_id = ?");
            $stmt->execute([$order_id]);
            $items_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            echo "Order not found!";
            exit;
        }
    } else {
        echo "Order ID not provided!";
        exit;
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

include 'inc/header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Order</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

<main>
    <div class="container mt-40 mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            
                <!-- Font Awesome Icon -->
                <a href="my_orders.php" class="text-green-500 hover:text-green-700">
                    <i class="fas fa-arrow-left text-lg mr-2"></i>
                </a>

                <h1 class="text-2xl text-center font-semibold mb-4">Order Details</h1>
          
     
                <div>
                    <h2 class="text-xl font-semibold mb-2">Customer Details</h2>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($order['contact']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                </div>
            

            <table class="mt-4">
                <tr>
                    <th>Prescription Image</th>
                </tr>
                <tr>
                    <td> <!-- Customer Details -->
                        <div class="mb-4 flex items-start">
                            <!-- Display Prescription Image -->
                            <?php if (!empty($order['prescription_image'])): ?>
                                <?php
                                    $imageData = base64_encode($order['prescription_image']);
                                    $imageSrc = 'data:image/jpeg;base64,' . $imageData;
                                ?>
                                <img src="<?php echo $imageSrc; ?>" alt="Prescription Image" class="w-32 h-auto mr-4 cursor-pointer" onclick="showModal('<?php echo $imageSrc; ?>')">
                            <?php endif; ?>
                            </div>
                    </td>
                </tr>
            </table>

            

            <!-- Order Items -->
            <h2 class="text-xl font-semibold mb-2">Order Items</h2>
            <table class="min-w-full mb-4">
                <th class="border">Items</th>
                <th class="border">Quantity</th>
                <th class="border">Price</th>
                <th class="border">Amount</th>
                <tbody>
                    <?php foreach ($items_result as $item): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td class="border px-4 py-2">₱<?php echo htmlspecialchars($item['price']); ?></td>
                            <td class="border px-4 py-2">₱<?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                        <tr>
                            <td class="border-t border-l border-b "></td>
                            <td class="border-t border-b "></td>
                            <td class="border-t border-r border-b  px-4 py-2">
                                <div>
                                    <strong>Total Amount: </strong>                                    
                                </div>
                            </td>
                            <td class="border px-4 py-2">
                                        ₱<?php echo htmlspecialchars($order['total_amount']); ?>
                                        </td>
                        </tr>
                </tbody>
            </table>
            <p class="text-xl"><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
            <br>

            <!-- Cancel Order Button -->
            <?php if (!in_array($order['order_status'], ['Cancelled', 'Delivered', 'Out for Delivery', 'Packed'])): ?>
                <div class="mb-4 text-right">
                    <form action="update_order_status.php" method="post">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="order_status" value="Cancelled">
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">Cancel Order</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Delivery Image -->
            <div class="mb-4 flex items-start">
                <table>
                    <tr>
                        <th> 
                            <div>
                                <p class="text-xl mb-2"><strong> Delivery Image: </p>
                                <?php if (!empty($order['delivery_image'])): ?>
                                <?php else: ?>
                                    <p>No delivery image available.</p>
                                <?php endif; ?>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?php if (!empty($order['delivery_image'])): ?>
                                <?php
                                    $imageData = base64_encode($order['delivery_image']);
                                    $imageSrc = 'data:image/jpeg;base64,' . $imageData;
                                ?>
                                <img src="<?php echo $imageSrc; ?>" alt="Delivery Image" class="w-32 h-auto mr-4 cursor-pointer" onclick="showModal('<?php echo $imageSrc; ?>')">
                            <?php endif; ?>
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

    <!-- Display Confirmation Message -->
    <?php if (isset($_SESSION['confirmation_message'])): ?>
        <div class="fixed bottom-0 left-0 right-0 bg-green-500 text-white text-center py-2">
            <?php echo $_SESSION['confirmation_message']; ?>
        </div>
        <?php unset($_SESSION['confirmation_message']); ?>
    <?php endif; ?>

</main>

<?php
$conn = null; // Close the PDO connection
include 'inc/footer.php';
?>

</body>
</html>
