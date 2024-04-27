<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if brand ID is provided in the URL
    if(isset($_GET['id'])) {
        $brandId = $_GET['id'];

        // Fetch the brand information
        $stmtGetBrand = $pdo->prepare("SELECT * FROM brands WHERE brand_id = ?");
        $stmtGetBrand->execute([$brandId]);
        $brand = $stmtGetBrand->fetch(PDO::FETCH_ASSOC);

        if(!$brand) {
            // Brand not found, redirect to brand list page with error message
            header("Location: brand_list.php?error=Brand not found");
            exit();
        }
    } else {
        // Brand ID not provided, redirect to brand list page with error message
        header("Location: brand_list.php?error=Brand ID not provided");
        exit();
    }

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $brandName = $_POST['brand_name'];
        $brandDescription = $_POST['brand_description'];

        // Update the brand information
        $stmtUpdateBrand = $pdo->prepare("UPDATE brands SET brand_name = ?, brand_description = ? WHERE brand_id = ?");
        $stmtUpdateBrand->execute([$brandName, $brandDescription, $brandId]);

        // Redirect back to the brand list page
        header("Location: brand_list.php");
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
    <title>Edit Brand</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 ml-64">
<?php include 'inc/sidebar.php'?>
    <div class="flex justify-center">
        <!-- Main Content -->
        <div class="container mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-semibold mb-4">Edit Brand</h1>
            <!-- Brand Edit Form -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-4">
                    <label for="brand_name" class="block text-sm font-medium text-gray-600">Brand Name:</label>
                    <input type="text" name="brand_name" id="brand_name" class="mt-1 p-2 w-full border rounded-md" value="<?php echo $brand['brand_name']; ?>" required>
                </div>
                <div class="mb-4">
                    <label for="brand_description" class="block text-sm font-medium text-gray-600">Brand Description:</label>
                    <textarea name="brand_description" id="brand_description" rows="3" class="mt-1 p-2 w-full border rounded-md" required><?php echo $brand['brand_description']; ?></textarea>
                </div>
                <div class="mb-4 text-right">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save Changes</button>
                    <a href="brand_list.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded ml-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
