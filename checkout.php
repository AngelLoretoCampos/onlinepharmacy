
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

if (isset($_POST['confirm_order'])) {
    $user_id = $_SESSION['user_id'];
    $payment_method = "Cash on Delivery";

    $product_names = $_POST['product_name'];
    $quantities = $_POST['quantity'];

    // Initialize total amount
    $total_amount = 0;

    // Handle prescription image upload
    $prescription_image = '';

    if (isset($_FILES['prescription_image']) && $_FILES['prescription_image']['error'] === 0) {
        $prescription_image = file_get_contents($_FILES['prescription_image']['tmp_name']);
    }

    // Begin transaction
    $conn->begin_transaction();

    // Insert order details into orders table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, payment_method, total_amount, prescription_image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $payment_method, $total_amount, $prescription_image);

    if ($stmt->execute()) {
        $order_id = $conn->insert_id; // Get the last inserted order ID

        // Insert each product into order_items table and update product quantity
        for ($i = 0; $i < count($product_names); $i++) {
            $product_name = $product_names[$i];
            $quantity = $quantities[$i];

            // Fetch product details including product ID
            $check_product = $conn->prepare("SELECT id, price, quantity as available_quantity, prescription_required FROM products WHERE product_name = ?");
            $check_product->bind_param("s", $product_name);
            $check_product->execute();
            $result = $check_product->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $product_id = $row['id'];
                $price = $row['price'];
                $available_quantity = $row['available_quantity'];
                $prescription_required = $row['prescription_required'];

                if ($quantity > $available_quantity) {
                    echo " <script>
    alert('Not enough quantity available for the selected product.');
    window.history.back();
</script>";

                    $conn->rollback();
                    exit;
                }

                // Calculate total amount for the order
                $total_amount += $price * $quantity;

                // Insert into order_items table
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iisdd", $order_id, $product_id, $product_name, $quantity, $price);

                if ($stmt->execute()) {
                    // Update product quantity
                    $new_quantity = $available_quantity - $quantity;
                    $update_product = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
                    $update_product->bind_param("ii", $new_quantity, $product_id);
                    $update_product->execute();
                } else {
                    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                    $conn->rollback();
                    exit;
                }

                if ($prescription_required == 1 && empty($prescription_image)) {
                    echo $error_missing_prescription = "<script>
    alert('Prescription is required for  " . $product_name . ", Please upload the prescription image.');
    window.history.back();
</script>";
                    $conn->rollback();
                    exit;
                }
            } else {
                echo "Product not found: " . $product_name;
                $conn->rollback();
                exit;
            }

            $check_product->close();
        }


        // Update total amount in orders table
        $stmt = $conn->prepare("UPDATE orders SET total_amount = ? WHERE id = ?");
        $stmt->bind_param("di", $total_amount, $order_id);
        $stmt->execute();

        // Remove items from cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Display a JavaScript popup message
        echo "<script>alert('Order confirmed! Total Amount: " . $total_amount . "');</script>";

        // Redirect to dashboard after order confirmation
       // Redirect to my_orders.php after order confirmation with a query parameter indicating success
        header("Location: my_orders.php?orderConfirmed=true");
        exit();

    } else {
        // Error occurred
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        $conn->rollback();
    }

    $stmt->close();
    $conn->close();
} else {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Fetch user details
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Fetch cart items
    $sql = "SELECT p.product_name, p.price, p.prescription_required, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_amount = 0; // Initialize total amount

    // Flag to check if prescription upload section needs to be displayed
    $prescription_required = false;

    while ($row = $result->fetch_assoc()) {
        $total_amount += $row['price'] * $row['quantity'];

        // Check if prescription is required for any product
        if ($row['prescription_required'] == 1) {
            $prescription_required = true;
        }
    }

    // HTML content
    include 'inc/header.php';
    ?>

    

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Checkout</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> 
    </head>
    <body class="bg-gray-100">

    <main class="p-4 mt-40">
        <div class="container mx-auto p-4">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h1 class="text-2xl text-center font-semibold mb-4">Checkout</h1>
                <div class="mb-4">
                    <p class="text-xl font-semibold mb-2">Shipping Address:</p>
                    <i class="m-10">
                        <?php echo htmlspecialchars($user['province']); ?>,
                        <?php echo htmlspecialchars($user['city']); ?>,
                        <?php echo htmlspecialchars($user['barangay']); ?>,
                        <?php echo htmlspecialchars($user['additional_address']); ?>
                    </i>
                </div>
                <h2 class="text-xl font-semibold mb-2">Order Summary</h2>
                <form action="checkout.php" method="post" enctype="multipart/form-data">
                    <table class="min-w-full mb-4">
                        <thead>
                            <tr>
                                <th class="border px-4 py-2">Product Name</th>
                                <th class="border px-4 py-2">Price</th>
                                <th class="border px-4 py-2">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $result->data_seek(0); // Reset result set pointer
                            while ($row = $result->fetch_assoc()) { 
                                ?>
                                <tr>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['product_name']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['price']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['quantity']); ?></td>
                                </tr>
                                <input type="hidden" name="product_name[]" value="<?php echo $row['product_name']; ?>">
                                <input type="hidden" name="quantity[]" value="<?php echo $row['quantity']; ?>">
                                <?php 
                            } 
                            ?>
                        </tbody>
                    </table>
                    <?php if ($prescription_required): ?>
                        <div class="mt-4">
                            <h2 class="text-xl font-semibold mb-2">Products Requiring Prescription:</h2>
                            <ul>
                                <?php 
                                $result->data_seek(0); // Reset result set pointer
                                while ($row = $result->fetch_assoc()) { 
                                    if ($row['prescription_required'] == 1) {
                                        echo "<li><i class='fa-solid fa-prescription-bottle-medical'></i> " . htmlspecialchars($row['product_name']) . "</li>";
                                    }
                                } 
                                ?>
                            </ul>
                        </div>
                        <div class="mt-4">
                            <h2 class="text-xl font-semibold mb-2">Upload Prescription</h2>
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                                <p class="font-bold">Please upload your prescription file:</p>
                                <label for="prescription_image" class="block mt-2">
                                    <input type="file" name="prescription_image" id="prescription_image" class="mt-1 block w-full">
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="mt-4">
                        <h2 class="text-xl font-semibold mb-2">Total Amount: ₱ <?php echo $total_amount; ?></h2>
                        <p><strong><i>Payment Method: </i></strong>Cash On Delivery</p>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" name="confirm_order" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Confirm Order</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    </body>
    </html>

    <?php
}
include 'inc/footer.php';
?>
