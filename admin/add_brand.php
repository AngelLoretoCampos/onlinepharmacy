<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

$message = '';  // Variable to store the message

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $brand_name = $_POST['brand_name'];
        $brand_description = $_POST['brand_description'];

        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO brands (brand_name, brand_description) VALUES (:brand_name, :brand_description)");

        // Bind parameters
        $stmt->bindParam(':brand_name', $brand_name);
        $stmt->bindParam(':brand_description', $brand_description);

        // Execute SQL statement
        $stmt->execute();

        // Set success message
        $message = 'Brand added successfully!';

        // Redirect to brand_list.php after 2 seconds
        header("refresh:2;url=brand_list.php");
    }

} catch (PDOException $e) {
    // Handle database errors
    $message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Brand</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>

<?php include 'inc/sidebar.php' ?>

<body class="bg-gray-100 ml-64">

    <div class="flex justify-center">
        <!-- Main Content -->
        <div class="container mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
            <div class="w-full max-w-md">
                <h1 class="text-2xl font-semibold mb-4">Add Brand</h1>

                <!-- Display Message -->
                <?php if (!empty($message)): ?>
                    <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form action="add_brand.php" method="post" class="w-full">
                    <!-- Brand Name -->
                    <div class="mb-4">
                        <label for="brand_name" class="block text-sm font-medium text-gray-600">Brand Name:</label>
                        <input type="text" name="brand_name" id="brand_name" class="mt-1 p-2 w-full border rounded-md" required>
                    </div>

                    <!-- Brand Description -->
                    <div class="mb-4">
                        <label for="brand_description" class="block text-sm font-medium text-gray-600">Brand Description:</label>
                        <textarea name="brand_description" id="brand_description" rows="3" class="mt-1 p-2 w-full border rounded-md" required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-4">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Add Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script for popup -->
    <?php if (!empty($message)): ?>
        <script>
            alert("<?php echo $message; ?>");
        </script>
    <?php endif; ?>

</body>

</html>
