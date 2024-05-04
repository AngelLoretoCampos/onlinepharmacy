<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

$perPage = 10; // Number of items per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1; // Current page number
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Build the SQL query for searching
    $sql = "SELECT * FROM users";

    if (!empty($searchTerm)) {
        $sql .= " WHERE ";
        $columns = ['email', 'firstname', 'lastname', 'contact', 'gender', 'city', 'province', 'barangay', 'additional_address'];
        $conditions = [];
        foreach ($columns as $column) {
            $conditions[] = "$column LIKE :searchTerm";
        }
        $sql .= implode(" OR ", $conditions);
    }

    // Prepare and execute the SQL query
    $stmt = $pdo->prepare($sql);
    
    if (!empty($searchTerm)) {
        $searchTerm = "%$searchTerm%";
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
    }

    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total number of clients
    $total = count($clients);

    // Calculate total pages
    $totalPages = ceil($total / $perPage);

    // Ensure current page is within valid range
    $page = max(1, min($totalPages, $page));

    // Calculate the offset for the query
    $offset = ($page - 1) * $perPage;

    // Fetch clients for the current page with pagination
    $sql .= " LIMIT :offset, :perPage";
    $stmt = $pdo->prepare($sql);
    if (!empty($searchTerm)) {
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Client List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100 font-sans ml-64 leading-normal tracking-normal">
    <!-- Sidebar -->
    <?php include 'inc/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold">User List</h1>
            <!-- Search Bar -->
            <form action="" method="GET" class="flex">
                <input type="text" name="search" placeholder="Search..." class="border rounded-l py-2 px-4 outline-none">
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-r hover:bg-blue-600 focus:outline-none"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <!-- Clients Table -->
        <div class="overflow-x-auto" style="max-height: 500px;">
            <table class="min-w-full bg-white border rounded">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="w-1/11 text-center py-2 px-4">Email</th>
                        <th class="w-1/11 text-center py-2 px-4">First Name</th>
                        <th class="w-1/11 text-center py-2 px-4">Last Name</th>
                        <th class="w-1/11 text-center py-2 px-4">Contact</th>
                        <th class="w-1/11 text-center py-2 px-4">Gender</th>
                        <th class="w-1/11 text-center py-2 px-4">Address</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700" id="clientTable">
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo $client['email']; ?></td>
                            <td class="border px-4 py-2"><?php echo $client['firstname']; ?></td>
                            <td class="border px-4 py-2"><?php echo $client['lastname']; ?></td>
                            <td class="border px-4 py-2"><?php echo $client['contact']; ?></td>
                            <td class="border px-4 py-2"><?php echo $client['gender']; ?></td>
                            <td class="border px-4 py-2"><?php echo $client['city'] . ', ' . $client['province'] . ', ' . $client['barangay']  . ', ' . $client['additional_address']; ?></td>
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
    </div>

</body>

</html>
