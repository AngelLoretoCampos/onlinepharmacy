<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<?php include 'inc/header.php'; ?>

<main class="p-4 mt-40">

    <div class="container mx-auto p-4">
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
<img src="data:image/jpeg;base64,' . $product_image . '" alt="' . $product_name . '" class="w-1/3">
<div class="ml-4">
    <h1 class="text-2xl font-semibold mb-2">' . $product_name . '</h1>
    <p class="text-gray-600 mb-2">₱ ' . $price . '</p>
    <p class="text-gray-600 mb-2">Brand: ' . $brand_name . '</p>
    <p class="text-gray-600 mb-2">Stocks: ' . $quantity . '</p>';

if ($prescription_required) {
echo '<p class="text-red-600 mb-2">* This product requires a prescription.</p>';
}

echo '
    <!-- Add to Cart Button -->
    <form action="add_to_cart.php" method="post" class="mt-4">
        <input type="hidden" name="product_id" value="' . $product_id . '">';

// Disable the "Add to Cart" button if the product quantity is zero
if ($quantity > 0) {
echo '<button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="return checkLogin()">Add to Cart</button>';
} else {
echo '<button type="button" class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed" disabled>Add to Cart</button>';
}

echo '
    </form>
    <p class="text-gray-700 mt-4 text-lg"><hr><strong>Description:</strong><br>' . $product_description . '</p>
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

    <script>
        function checkLogin() {
            // Check if user is logged in (you can use your own method to check this)
            var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

            if (!isLoggedIn) {
                alert('Please login to add items to the cart.');
                window.location.href = 'login.php';
                return false; // Stop form submission
            }

            return true; // Allow form submission
        }
    </script>

</main>

<?php include 'inc/footer.php'; ?>

</body>
</html>
