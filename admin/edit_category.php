<?php
include '../database/dbconnection.php';

// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$full_name = $_SESSION['full_name'];

$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

$category = [];

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM category WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            header("Location: category_list.php");
            exit();
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $category_name = $_POST['category_name'];
    $cat_desc = $_POST['cat_desc'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("UPDATE category SET categoryName = :category_name, cat_desc = :cat_desc WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':category_name', $category_name, PDO::PARAM_STR);
        $stmt->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: category_list.php?success=1");
        exit();
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>

<?php include 'inc/sidebar.php'; ?>

<body class="bg-gray-100">
    <div class="ml-64 flex justify-center items-center h-screen">
        <div class="bg-white w-full max-w-xl rounded-md p-6">
            <h2 class="text-2xl font-semibold mb-4">Edit Category</h2>

            <form method="post" class="space-y-4">
                <!-- Hidden input field to store category ID -->
                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">

                <div class="flex flex-col mb-4">
                    <label for="category_name" class="text-sm font-medium text-gray-600 mb-1">Category Name:</label>
                    <input type="text" id="category_name" name="category_name" required class="border rounded-md p-2" value="<?php echo $category['categoryName']; ?>">
                </div>
                <div class="flex flex-col mb-4">
                    <label for="cat_desc" class="text-sm font-medium text-gray-600 mb-1">Category Description:</label>
                    <input type="text" id="cat_desc" name="cat_desc" required class="border rounded-md p-2" value="<?php echo $category['cat_desc']; ?>">
                </div>
                <div class="text-left">
                    <input type="submit" value="Save Changes" class="bg-blue-500 text-white px-4 py-2 rounded">
                    <a href="category_list.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded ml-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
