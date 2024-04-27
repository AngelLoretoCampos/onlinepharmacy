<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

// Fetch all products
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch products from products table
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    try {
        // Fetch product name based on product_id
        $stmt = $pdo->prepare("SELECT product_name FROM products WHERE id = :product_id");
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("INSERT INTO inventory (product_id, product_name, quantity, price) VALUES (:product_id, :product_name, :quantity, :price)");

        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':product_name', $product['product_name'], PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->execute();

        $message = 'Inventory updated successfully!';
    } catch (PDOException $e) {
        // Handle database errors
        $message = "Error: " . $e->getMessage();
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'inc/sidebar.php'; ?>
<head>
    <meta charset="UTF-8">
    <title>Add Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>

<body class="bg-gray-100 ml-64">

<div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-semibold mb-4">Add Inventory</h1>

    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" class="space-y-4">
        <div class="flex items-center">
            <label for="product_id" class="w-1/4">Product:</label>
            <select name="product_id" id="product_id" class="border rounded-md px-4 py-2 w-1/2">
                <option value="" disabled selected>Select a Product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>"><?php echo $product['product_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex items-center">
            <label for="quantity" class="w-1/4">Quantity:</label>
            <input type="number" name="quantity" id="quantity" class="border rounded-md px-4 py-2 w-1/2">
        </div>

        <div class="flex items-center">
            <label for="price" class="w-1/4">Price:</label>
            <input type="text" name="price" id="price" class="border rounded-md px-4 py-2 w-1/2">
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Add Inventory</button>
        </div>
    </form>
</div>

</body>
</html>
