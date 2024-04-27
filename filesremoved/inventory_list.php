<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

$perPage = 10; // Number of products per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1; // Current page number

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Count total number of products in the inventory
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM inventory");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Calculate total pages
    $totalPages = ceil($total / $perPage);

    // Calculate the offset for the query
    $offset = ($page - 1) * $perPage;

    // Fetch products for the current page
    $stmt = $pdo->prepare("SELECT * FROM inventory LIMIT :offset, :perPage");
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>
<!-- ... Your PHP code remains unchanged ... -->

<!DOCTYPE html>
<html lang="en">
<?php include 'inc/sidebar.php'?>
<head>
    <meta charset="UTF-8">
    <title>Inventory List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100 ml-64">

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold">Inventory List</h1>
            <a href="add_inventory.php" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Add Inventory</a>
        </div>

        <!-- Search Bar -->
        <div class="mb-4 flex justify-end items-center">
            <div class="flex">
                <input type="text" id="search" placeholder="Search..." class="border rounded-md px-4 py-2 w-64">
                <button type="button" id="searchButton" class="bg-blue-500 text-white hover:bg-blue-300 px-4 py-2 rounded-md ml-2">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table id="productTable" class="min-w-full bg-white border rounded">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="w-1/4 text-center py-2 px-4">Product Name</th>
                        <th class="w-1/6 text-center py-2 px-4">Variation</th>
                        <th class="w-1/6 text-center py-2 px-4">Quantity</th>
                        <th class="w-1/6 text-center py-2 px-4">Price</th>
                        <th class="w-1/6 text-center py-2 px-4">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo $product['product_name']; ?></td>
                            <td class="border px-4 py-2"><?php echo $product['variation']; ?></td>
                            <td class="border px-4 py-2"><?php echo $product['quantity']; ?></td>
                            <td class="border px-4 py-2"><?php echo $product['price']; ?></td>
                            <td class="border px-4 py-2 text-center">
                                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-edit"></i></a>
                                <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center">
            <ul class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <li><a href="?page=<?php echo $page - 1; ?>" class="bg-blue-500 text-white px-4 py-2 rounded-md">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li><a href="?page=<?php echo $i; ?>" class="bg-blue-500 text-white px-4 py-2 rounded-md <?php echo $page == $i ? 'bg-blue-700' : ''; ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li><a href="?page=<?php echo $page + 1; ?>" class="bg-blue-500 text-white px-4 py-2 rounded-md">Next</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const productTable = document.getElementById('productTable');
        const searchButton = document.getElementById('searchButton');

        searchButton.addEventListener('click', function() {
            const searchTerm = searchInput.value.toLowerCase();

            Array.from(productTable.getElementsByTagName('tr')).forEach(function(row) {
                const productName = row.getElementsByTagName('td')[0];
                const variation = row.getElementsByTagName('td')[1];

                if (productName && variation) {
                    const nameText = productName.textContent || productName.innerText;
                    const variationText = variation.textContent || variation.innerText;

                    if (nameText.toLowerCase().includes(searchTerm) || variationText.toLowerCase().includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    </script>

</body>

</html>
