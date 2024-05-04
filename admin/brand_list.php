<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

$perPage = 10; // Number of items per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1; // Current page number

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Count total number of brands
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM brands");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Calculate total pages
    $totalPages = ceil($total / $perPage);

    // Ensure current page is within valid range
    $page = max(1, min($totalPages, $page));

    // Calculate the offset for the query
    $offset = ($page - 1) * $perPage;

    // Fetch brands for the current page
    $stmt = $pdo->prepare("SELECT * FROM brands LIMIT :offset, :perPage");
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manufacturer List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<?php include 'inc/sidebar.php' ?>

<body class="bg-gray-100 ml-64">

    <div class="flex justify-center">
        <!-- Main Content -->
        <div class="container mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold">Manufacturer List</h1>
                <!-- Modal Button -->
                <button id="openModalButton" class="bg-green-500 text-white px-4 py-2 rounded-md">Add Manufacturer</button>
            </div>

            <!-- Search Bar -->
            <div class="mb-4 flex justify-end items-center">
                <div class="flex">
                    <input type="text" id="search" placeholder="Search..." class="border rounded-md px-4 py-2 w-64">
                    <button type="button" class="bg-green-500 text-white hover:bg-green-300 px-4 py-2 rounded-md ml-2">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
          <!-- Brands Table -->
<div class="overflow-x-auto" style="max-height: 500px;">
    <table class="min-w-full bg-white border rounded" style="width: 50%;">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="w-1/2 text-center py-2 px-4">Manufacturer Name</th>
                <th class="w-1/2 text-center py-2 px-4">Action</th>
            </tr>
        </thead>
        <tbody class="text-gray-700" id="brandTable">
            <?php foreach ($brands as $brand): ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $brand['brand_name']; ?></td>
                    <td class="border px-4 py-2 text-center">
                        <a href="edit_brand.php?id=<?php echo $brand['brand_id']; ?>" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-edit"></i></a>
                        <a href="delete_brand.php?id=<?php echo $brand['brand_id']; ?>" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


            <!-- Pagination -->
            <div class="flex justify-between mt-4">
                <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Prev</a>
                <div>Page <?php echo $page; ?> of <?php echo $totalPages; ?></div>
                <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page >= $totalPages) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Next</a>
            </div>

            <!-- Modal -->
            <div id="myModal" class="fixed inset-0 bg-gray-700 bg-opacity-50 hidden flex justify-center items-center">
                <div class="bg-white p-6 rounded-lg w-1/2">
                    <h2 class="text-2xl font-semibold mb-4">Add Manufacturer</h2>
                    <form action="add_brand_process.php" method="post">
                        <!-- Add Brand Form Inputs -->
                        <div class="mb-4">
                            <label for="brand_name" class="block text-sm font-medium text-gray-600">Manufacturer Name:</label>
                            <input type="text" name="brand_name" id="brand_name" class="mt-1 p-2 w-full border rounded-md" required>
                        </div>
                        <div class="mb-4 text-right">
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Add Brand</button>
                            <button type="button" id="closeModalButton" class="bg-red-500 text-white px-4 py-2 rounded ml-2">Close</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                const searchInput = document.getElementById('search');
                const brandTable = document.getElementById('brandTable');
                const openModalButton = document.getElementById('openModalButton');
                const closeModalButton = document.getElementById('closeModalButton');
                const modal = document.getElementById('myModal');

                searchInput.addEventListener('keyup', function() {
                    const searchTerm = searchInput.value.toLowerCase();

                    Array.from(brandTable.getElementsByTagName('tr')).forEach(function(row) {
                        const brandName = row.getElementsByTagName('td')[0];
                        const brandDescription = row.getElementsByTagName('td')[1];

                        if (brandName && brandDescription) {
                            const nameText = brandName.textContent || brandName.innerText;
                            const descriptionText = brandDescription.textContent || brandDescription.innerText;

                            if (nameText.toLowerCase().includes(searchTerm) || descriptionText.toLowerCase().includes(searchTerm)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });
                });

                openModalButton.addEventListener('click', function() {
                    modal.classList.remove('hidden');
                });

                closeModalButton.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            </script>

        </div>
    </div>

</body>

</html>
