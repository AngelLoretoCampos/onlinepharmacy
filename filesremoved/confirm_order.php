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

    // Loop through POST data to get product_id and quantity
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'product_id') !== false) {
            $product_id = $value;
        }
        if (strpos($key, 'quantity') !== false) {
            $quantity = $value;
        }
    }

    // Check if product_id exists in products table
    $check_product = $conn->prepare("SELECT id, price FROM products WHERE id = ?");
    $check_product->bind_param("i", $product_id);
    $check_product->execute();
    $result = $check_product->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $price = $row['price'];

        // Check if prescription file is uploaded
        if (isset($_FILES['prescription']) && $_FILES['prescription']['error'] === 0) {
            $prescription_file = $_FILES['prescription']['tmp_name'];
            $prescription_data = file_get_contents($prescription_file);

            // Generate a unique reference code
            $ref_code = uniqid('REF') . $user_id . time();

            // Get payment method from form
            $payment_method = "Cash on Delivery";

            // Calculate total amount
            $total_amount = $price * $quantity;

            // Insert order into database
            $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, payment_method, ref_code, prescription, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                $stmt->bind_param("iiissid", $user_id, $product_id, $quantity, $payment_method, $ref_code, $prescription_data, $total_amount);
                
                if ($stmt->execute()) {
                    // Order confirmed
                    echo "Order confirmed! Reference code: " . $ref_code;

                    // Remove products from cart after order confirmation
                    $delete_cart_items = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                    $delete_cart_items->bind_param("i", $user_id);
                    $delete_cart_items->execute();
                    $delete_cart_items->close();

                } else {
                    // Error occurred
                    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                }

                $stmt->close();
            } else {
                // Error preparing statement
                echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
            }
        } else {
            echo "Prescription file is required.";
        }
    } else {
        echo "Product not found!";
    }

    $check_product->close();
    $conn->close();
}
?>
