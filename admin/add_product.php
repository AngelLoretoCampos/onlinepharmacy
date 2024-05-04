<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmtBrand = $pdo->query("SELECT * FROM brands");
    $brands = $stmtBrand->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch categories
    $stmtCategory = $pdo->query("SELECT * FROM category");
    $categories = $stmtCategory->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>

<?php include 'inc/sidebar.php'; ?>

<body class="bg-gray-100 ml-64">

    <div class="flex justify-center">
        <!-- Main Content -->
        <div class="container mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
            <div class="w-full max-w-md">
                <h1 class="text-2xl font-semibold mb-4">Add Product</h1>

                <form action="add_product_process.php" method="post" enctype="multipart/form-data" class="w-full">
                    <!-- Prescription Required -->
                    <div class="mb-4">
                        <label for="prescription_required" class="block text-sm font-medium text-gray-600">Prescription Required:</label>
                        <select name="prescription_required" id="prescription_required" class="mt-1 p-2 w-full border border-green-500 rounded-md" required>
                            <option value="" disabled selected>Select</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <!-- Brand -->
                    <div class="mb-4">
                        <label for="brand" class="block text-sm font-medium text-gray-600">Manufacturer:</label>
                        <select name="brand" id="brand" class="mt-1 p-2 w-full border border-green-500 rounded-md" required>
                            <option value="" disabled selected>Select Manufacturer</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['brand_name']; ?>"><?php echo $brand['brand_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label for="category" class="block text-sm font-medium text-gray-600">Category:</label>
                        <select name="category" id="category" class="mt-1 p-2 w-full border border-green-500 rounded-md" required>
                            <option value="" disabled selected>Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['categoryName']; ?>"><?php echo $category['categoryName']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Product Name -->
                    <div class="mb-4">
                        <label for="product_name" class="block text-sm font-medium text-gray-600">Product Name:</label>
                        <input type="text" name="product_name" id="product_name" class="mt-1 p-2 w-full border border-green-500 rounded-md" required>
                    </div>

                    <!-- Product Description -->
                    <div class="mb-4">
                        <label for="product_description" class="block text-sm font-medium text-gray-600">Product Description:</label>
                        <textarea name="product_description" id="product_description" rows="3" class="mt-1 p-2 w-full border  border-green-500 rounded-md" required></textarea>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-600">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" class="mt-1 p-2 w-full border border-green-500 rounded-md">
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        <label for="price" class="block text-sm font-medium text-gray-600">Price:</label>
                        <input type="number" name="price" id="price" class="mt-1 p-2 w-full border border-green-500 rounded-md">
                    </div>

                    <!-- Product Image -->
                  <div class="mb-4 border rounded-xl border-green-500 relative">
                <!-- Styled button to mimic file input -->
                <label for="product_image" class="absolute left-0 bg-green-500 text-white px-4 py-3 rounded-l-lg cursor-pointer">Choose File</label>
                <!-- Actual file input (hidden) -->
                <input type="file" name="product_image" id="product_image" accept="image/*" class="opacity-0 left-0 px-4 m-2.5 cursor-pointer" onchange="showFileName(this)">
                <!-- Display selected file name -->
                <span id="file-name" class="absolute left-0 bg-white px-4 py-3 rounded-l-lg cursor-pointer hidden"></span>
            </div>


                    <!-- Submit Button -->
                    <div class="mb-4">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>

    function showFileName(input) {
    const fileNameSpan = document.getElementById('file-name');
    if (input.files.length > 0) {
        fileNameSpan.textContent = input.files[0].name;
        fileNameSpan.classList.remove('hidden');
    } else {
        fileNameSpan.textContent = '';
        fileNameSpan.classList.add('hidden');
    }
}

        function previewImage(event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onloadend = function () {
                preview.src = reader.result;
                preview.classList.remove('hidden');
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
            }
        }
    </script>

</body>

</html>
