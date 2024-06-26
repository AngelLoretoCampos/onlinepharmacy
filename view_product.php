<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<?php include 'inc/header.php'; ?>

<body class="bg-gray-100">

<main class="mt-40">
    <div class="container mx-auto p-4">
        <div class="bg-white rounded-lg shadow-lg p-4">
            <div id="messageContainer" class="fixed top-0 left-0 w-full h-full flex justify-center items-center hidden">
                <div class="bg-green-500 text-white py-2 px-4 rounded-md">
                    Product added to cart successfully!
                </div>
            </div>

            <?php
            // Database connection
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "onlinepharmacy_db";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check if id is set and valid
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $product_id = $_GET['id'];

                // Fetch product details from products table
                $stmt = $conn->prepare("SELECT product_name, price, product_image, product_description, brand, prescription_required, quantity FROM products WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if ($row) {
                    $product_name = $row['product_name'];
                    $price = $row['price'];
                    $product_image = base64_encode($row['product_image']);
                    $product_description = $row['product_description'];
                    $brand_name = $row['brand'];
                    $prescription_required = $row['prescription_required'];
                    $quantity = $row['quantity'];

                    echo '
<div class="flex">
    <div class="w-1/2">
        <img src="data:image/jpeg;base64,' . $product_image . '" alt="' . $product_name . '" class="w-full object-cover border border-green-500 object-center">
    </div>
    <div class="w-1/2 ml-4">
        <h1 class="text-2xl font-semibold mb-2">' . $product_name . '</h1>
        <p class="text-gray-600 mb-2">₱ ' . $price . '</p>
        <p class="text-gray-600 mb-2">Manufacturer: ' . $brand_name . '</p>
        <p class="text-gray-600 mb-2">Stocks: ' . $quantity . '</p>';

                    if ($prescription_required) {
                        echo '<p class="text-red-600 mb-2">* This product requires a prescription.</p>';
                    }

                    echo '
        <!-- Add to Cart Form -->
        <form action="add_to_cart.php" method="post" class="mt-4">
            <input type="hidden" name="product_id" value="' . $product_id . '">
            
            <!-- Quantity Input -->
            <div class="flex items-center mb-2">
                <button type="button" onclick="decrementQuantity()" class="bg-gray-200 px-3 py-1 rounded-l">-</button>
                <input type="number" name="quantity" id="quantity" value="1" class="border text-center w-16">
                <button type="button" onclick="incrementQuantity()" class="bg-gray-200 px-3 py-1 rounded-r">+</button>
            </div>';

                    // Disable the "Add to Cart" button if the product quantity is zero
                    if ($quantity > 0) {
                        echo '<button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" id="addToCartBtn">Add to Cart</button>';
                    } else {
                        echo '<button type="button" class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed" disabled>Add to Cart</button>';
                    }

                    echo '
        </form>
        <p class="text-gray-700 mt-4 w-3/4 text-lg"><hr><strong>Description:</strong><br>' . $product_description . '</p>
    </div>
</div>';
                } else {
                    echo '<p class="text-center">Product not found.</p>';
                }
            } else {
                echo '<p class="text-center">Invalid product ID.</p>';
            }

            $conn->close();
            ?>

        </div>
    </div>
</main>

<script>
    // Get the URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const added = urlParams.get('added');

    // Show the message container if "added" is true
    if (added) {
        const messageContainer = document.getElementById('messageContainer');
        messageContainer.classList.remove('hidden');

        // Hide the message after 2 seconds
        setTimeout(() => {
            messageContainer.classList.add('hidden');
        }, 2000);
    }

    function incrementQuantity() {
        var quantityInput = document.getElementById('quantity');
        var maxQuantity = <?php echo $quantity; ?>;
        if (parseInt(quantityInput.value) < parseInt(maxQuantity)) {
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }
    }

    function decrementQuantity() {
        var quantityInput = document.getElementById('quantity');
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    }
</script>

<?php include 'inc/footer.php'; ?>

</body>
</html>
