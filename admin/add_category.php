<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Category Input Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>

<?php 
include 'inc/sidebar.php';
?>

<body class="bg-gray-100">
    <div class="ml-64 flex justify-center items-center h-screen">
        <div class="bg-white w-full max-w-xl rounded-md p-6">
            <h2 class="text-2xl font-semibold mb-4">Enter a Category</h2>

            <form id="categoryForm" action="process_category.php" method="POST" class="space-y-4">
                <div class="flex flex-col mb-4">
                    <label for="category" class="text-sm font-medium text-gray-600 mb-1">Category:</label>
                    <input type="text" id="category" name="category" required class="border rounded-md p-2">
                </div>
                <div class="flex flex-col mb-4">
                    <label for="cat_desc" class="text-sm font-medium text-gray-600 mb-1">Category Description:</label>
                    <input type="text" id="cat_desc" name="cat_desc" required class="border rounded-md p-2">
                </div>
                <div class="text-left">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add event listener for form submission
        document.getElementById('categoryForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission
            // You can perform additional validation here if needed
            // Submit the form via AJAX or let it submit normally
            this.submit(); // Submit the form
        });
    </script>
</body>

</html>
