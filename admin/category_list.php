<?php
// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$full_name = $_SESSION['full_name'];
?>

<head>
    <meta charset="UTF-8">
    <title>Category List</title>
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

$search = '';
$categories = [];
$perPage = 5; // Number of categories per page

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if search query is set
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        // Fetch categories based on search query
        $sql = "SELECT * FROM category WHERE categoryName LIKE :search OR cat_desc LIKE :search";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Fetch all categories
        $sql = "SELECT * FROM category";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Pagination
    $totalCategories = count($categories);
    $totalPages = ceil($totalCategories / $perPage);
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $currentPage = max(1, min($totalPages, $currentPage));
    $offset = ($currentPage - 1) * $perPage;

    if (isset($_GET['search'])) {
        $sql = "SELECT * FROM category WHERE categoryName LIKE :search OR cat_desc LIKE :search LIMIT :perPage OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    } else {
        $sql = "SELECT * FROM category LIMIT :perPage OFFSET :offset";
        $stmt = $pdo->prepare($sql);
    }
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>

<body class="bg-gray-100 ml-64 mt-20 flex">
    <div class=" items-center w-full">
        <div class="bg-white w-full  rounded-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Category List</h1>
                <!-- Add Category Button -->
                <button id="addCategoryBtn" class="bg-green-500 text-white px-4 py-2 rounded">Add Category</button>
            </div>
            
            <!-- Search Bar -->
            <form action="" method="GET" class="mb-6 flex justify-end">
                <input type="text" name="search" id="search" placeholder="Search" class="border rounded-md p-2 w-1/4 mr-2" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <!-- Category List Table -->
            <table class="min-w-full text-center">
                <thead  class="bg-gray-800 text-white">
                    <tr>
                        <th class="border px-4 py-2">Category</th>
                        <th class="border px-4 py-2">Category Description</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category) { ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($category['categoryName']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($category['cat_desc']); ?></td>
                            <td class="border px-4 py-2">
                                <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="text-blue-500 mr-2"><i class="fas fa-edit"></i></a>
                                <a href="delete_category.php?id=<?php echo $category['id']; ?>" class="text-red-500"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
           <!-- Pagination -->
        <div class="flex justify-between mt-4">
            <a href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo htmlspecialchars($search); ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($currentPage <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Prev</a>
            
            <div>
                Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
            </div>
            
            <a href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo htmlspecialchars($search); ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($currentPage >= $totalPages) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Next</a>
        </div>


            <!-- Add Category Modal -->
            <div id="addCategoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden">
                <div class="bg-white w-full max-w-md rounded-md p-6">
                    <h2 class="text-2xl font-semibold mb-4">Enter a Category</h2>
                    <form id="categoryForm" action="" method="post" class="space-y-4">
                        <div class="flex flex-col mb-4">
                            <label for="category" class="text-sm font-medium text-gray-600 mb-1">Category:</label>
                            <input type="text" id="category" name="category" required class="border rounded-md p-2">
                        </div>
                        <div class="flex flex-col mb-4">
                            <label for="cat_desc" class="text-sm font-medium text-gray-600 mb-1">Category Description:</label>
                            <input type="text" id="cat_desc" name="cat_desc" required class="border rounded-md p-2">
                        </div>
                        <div class="text-right">
                            <input type="submit" value="Submit" class="bg-green-500 text-white px-4 py-2 rounded">
                        </div>
                    </form>
                </div>
            </div>

            <script>
                // Show modal when Add Category button is clicked
                document.getElementById('addCategoryBtn').addEventListener('click', function() {
                    document.getElementById('addCategoryModal').classList.remove('hidden');
                });

                // Close modal when anywhere outside the modal is clicked
                document.addEventListener('click', function(event) {
                    const modal = document.getElementById('addCategoryModal');
                    if (event.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            </script>
        </div>
    </div>
</body>

</html>
