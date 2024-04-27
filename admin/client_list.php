<?php
include '../database/dbconnection.php';

// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_name = "";
$login_logout_text = "";
$login_logout_url = "";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_name = $user['firstname'];
    $login_logout_text = "Logout";
    $login_logout_url = "logout.php";
} else {
    $user_name = "";
    $login_logout_text = "Login";
    $login_logout_url = "login.php";
}

// Fetch all users from the database
if(isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email LIKE :search OR firstname LIKE :search OR lastname LIKE :search OR contact LIKE :search OR gender LIKE :search OR city LIKE :search OR province LIKE :search OR barangay LIKE :search OR additional_address LIKE :search");
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
} else {
    $stmt = $conn->prepare("SELECT * FROM users");
}
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination variables
$perPage = 10; // Number of items per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1; // Current page number
$totalUsers = count($users); // Total number of users
$totalPages = ceil($totalUsers / $perPage); // Calculate total pages

include 'inc/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Users Info</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>


<body class="bg-gray-100 ml-64">

<main class="container mt-10 p-6 flex bg-white rounded-lg shadow-md">
    <div class="mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">All Users</h1>
            <form action="" method="get" class="flex">
                <input type="text" name="search" placeholder="Search..." class="border rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded-md ml-2"> <i class="fas fa-search"></i></button>
            </form>
        </div>
        <table class="w-full table-auto">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">First Name</th>
                    <th class="border px-4 py-2">Last Name</th>
                    <th class="border px-4 py-2">Contact</th>
                    <th class="border px-4 py-2">Gender</th>
                    <th class="border px-4 py-2">City</th>
                    <th class="border px-4 py-2">Province</th>
                    <th class="border px-4 py-2">Barangay</th>
                    <th class="border px-4 py-2">Additional Address</th>
                    <!--<th class="border px-4 py-2">Status</th>--> <!-- New column -->
                    <th class="border px-4 py-2">Manage</th> <!-- New column -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo $user['email']; ?></td>
                        <td class="border px-4 py-2"><?php echo $user['firstname']; ?></td>
                        <td class="border px-4 py-2"><?php echo $user['lastname']; ?></td>
                        <td class="border px-4 py-2"><?php echo $user['contact']; ?></td>
                        <td class="border px-4 py-2"><?php echo $user['gender']; ?></td>
                        <td class="border px-4 py-2"><?php echo $user['city']; ?></td>
                        <td class="border px-4 py-2"><?php echo $user['province']; ?></td>
                        <td class="border px-4 py-2"><?php echo $user['barangay']; ?></td>
                        <td class="border px-4 py-2"><?php echo $user['additional_address']; ?></td>
                        <!-- <td class="border px-4 py-2"> <?php /* echo $user['status']; */?></td> Display status -->
                        <td class="border text-center px-4 py-2"> <!-- Manage column -->
                           <!-- <a href="edit_user.php?id=<?php // echo $user['id']; ?>" class="text-blue-500">
                                <i class="fas fa-edit"></i> --><!-- Edit icon -->
                           <!-- </a>-->
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="text-red-500 ml-2" onclick="return confirm('Are you sure you want to delete this user?');">
                                <i class="fas fa-trash-alt"></i> <!-- Delete icon -->
                            </a>
                        </td>


                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
         <!-- Pagination -->
            <div class="flex justify-between mt-4">
                <a href="?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Prev</a>
                <div>Page <?php echo $page; ?> of <?php echo $totalPages; ?></div>
                <a href="?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-green-500 text-white rounded <?php echo ($page >= $totalPages) ? 'opacity-50 cursor-not-allowed' : ''; ?>">Next</a>
            </div>
    </div>
</main>

</body>
</html>
