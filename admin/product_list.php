<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Product List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<?php 
include 'inc/sidebar.php';

// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    // Check if delete request is sent
    if (isset($_POST['delete_id'])) {
        // Begin a transaction to ensure atomicity
        $pdo->beginTransaction();
        
        try {
            // Delete the product from the products table
            $delete_id = $_POST['delete_id'];
            $stmtDeleteProduct = $pdo->prepare("DELETE FROM products WHERE id = :id");
            $stmtDeleteProduct->bindValue(':id', $delete_id);
            $stmtDeleteProduct->execute();
                
            // Commit the transaction if the deletion succeeds
            $pdo->commit();
                
            // Redirect to product list page after deletion
            header("refresh:1; url=product_list.php");
            echo "Product deleted successfully.";
            exit();
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $pdo->rollBack();
            
            // Display an alert box with the error message
            echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
        }
    }

    // Pagination
    $limit = 10; // Number of products per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
    $offset = ($page - 1) * $limit; // Offset

    // Filter
    $search = isset($_GET['search']) ? $_GET['search'] : ''; // Search query

    // SQL query with pagination, filter, and condition for not deleted products
    $sql = "SELECT * FROM products WHERE product_name LIKE :search AND is_deleted = FALSE LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total products for pagination
    $sqlCount = "SELECT COUNT(*) as count FROM products WHERE product_name LIKE :search AND is_deleted = FALSE";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    $stmtCount->execute();
    $totalResults = $stmtCount->fetch(PDO::FETCH_ASSOC)['count'];
    $totalPages = ceil($totalResults / $limit);
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>

<body class="bg-gray-100 ml-64 flex justify-center">
    <div class="container mt-10 text-center justify-center items-center overflow-hidden">
        <div class="bg-white w-4/4 rounded-md p-6 h-full">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Product List</h1>
                <!-- Add Product Button -->
                <div>
                    <a href="add_product.php" class="bg-green-500 text-white px-4 py-2 rounded">Add Product</a>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative mb-6">
                <form action="" method="get" class="flex justify-end">
                    <input type="text" name="search" id="search" placeholder="Search products" class="border rounded-md p-2 mr-2" value="<?php echo $search; ?>">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Product List Table -->
            <table class="min-w-full ">
                <thead  class="bg-gray-800 text-white">
                    <tr>
                        <th class="border px-4 py-2">Product Name</th>
                        <th class="border px-4 py-2">Price</th>
                        <th class="border px-4 py-2">Quantity</th>
                        <th class="border px-4 py-2">Brand</th>
                        <th class="border px-4 py-2">Category</th>
                        <th class="border px-2 py-2">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($result as $row) { ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['price']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['brand']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['category']); ?></td>
                            <!-- Inside the loop that generates the table rows -->
                            <td class="border px-4 py-2">
                                <div class="flex justify-center">
                                    <a href="product_details.php?id=<?php echo $row['id']; ?>" class="text-green-500 mr-2"><i class="fas fa-edit"></i></a>
                                    <form action="" method="post" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="text-red-500"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex justify-between mt-4">
                <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Prev</a>
                <div>Page <?php echo $page; ?> of <?php echo $totalPages; ?></div>
                <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page >= $totalPages) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Next</a>
            </div>

        </div>
    </div>
</body>

</html>
