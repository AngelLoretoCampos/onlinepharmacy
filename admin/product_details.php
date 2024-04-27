<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check if all required fields are provided
        if (isset($_POST['id'], $_POST['product_name'], $_POST['product_description'], $_POST['quantity'], $_POST['price'])) {
            // Update product details
            $id = $_POST['id'];
            $productName = $_POST['product_name'];
            $productDescription = $_POST['product_description'];
            $quantity = $_POST['quantity'];
            $price = $_POST['price'];

            // Check if a new image is uploaded
            if (!empty($_FILES['new_product_image']['tmp_name'])) {
                $newProductImage = file_get_contents($_FILES['new_product_image']['tmp_name']);
                $stmt = $pdo->prepare("UPDATE products SET product_name = ?, product_description = ?, quantity = ?, price = ?, product_image = ? WHERE id = ?");
                $stmt->execute([$productName, $productDescription, $quantity, $price, $newProductImage, $id]);
            } else {
                // If no new image is uploaded, update product details without changing the image
                $stmt = $pdo->prepare("UPDATE products SET product_name = ?, product_description = ?, quantity = ?, price = ? WHERE id = ?");
                $stmt->execute([$productName, $productDescription, $quantity, $price, $id]);
            }
        } else {
            // Redirect or handle the case when required fields are missing
            // For example:
            // header("Location: error_page.php");
            // exit();
        }
    }

    // Fetch product details based on ID
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindValue(':id', $_GET['id']);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Redirect or handle the case when no ID is provided
        header("Location: product_list.php");
        exit();
    }
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>

<?php include 'inc/sidebar.php'; ?>

<body class="bg-gray-100 ml-64">

    <div class="flex justify-center">
        <!-- Main Content -->
        <div class="container mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
            <div class="w-full max-w-md">
                <h1 class="text-2xl font-semibold mb-4">Edit Product</h1>

                <form action="" method="post" enctype="multipart/form-data" class="w-full">
                    <!-- Hidden input to pass product ID -->
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                    <!-- Populate form fields with product details -->
                    <div class="mb-4">
                        <label for="product_name" class="block text-sm font-medium text-gray-600">Product Name:</label>
                        <input type="text" name="product_name" id="product_name" class="mt-1 p-2 w-full border rounded-md" value="<?php echo $product['product_name']; ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="product_description" class="block text-sm font-medium text-gray-600">Product Description:</label>
                        <textarea name="product_description" id="product_description" rows="3" class="mt-1 p-2 w-full border rounded-md" required><?php echo $product['product_description']; ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-600">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" class="mt-1 p-2 w-full border rounded-md" value="<?php echo $product['quantity']; ?>">
                    </div>

                    <div class="mb-4">
                        <label for="price" class="block text-sm font-medium text-gray-600">Price:</label>
                        <input type="number" name="price" id="price" class="mt-1 p-2 w-full border rounded-md" value="<?php echo $product['price']; ?>">
                    </div>

                    <!-- Product Image -->
                    <div class="mb-4">
                        <label for="product_image" class="block text-sm font-medium text-gray-600">Product Image:</label>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($product['product_image']); ?>" alt="Product Image" class="mt-2 w-full" />
                    </div>

                    <!-- Upload New Image -->
                    <div class="mb-4">
                        <label for="new_product_image" class="block text-sm font-medium text-gray-600">Upload New Image:</label>
                        <input type="file" name="new_product_image" id="new_product_image" class="mt-1 p-2 w-full border rounded-md">
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-4">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
