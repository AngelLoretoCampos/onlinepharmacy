<?php
include 'database/dbconnection.php';
include 'inc/header.php'; 

// Fetch all categories from the database with their descriptions
$stmt = $conn->query("SELECT categoryName, cat_desc FROM category");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 mt-40">

<main class="container mx-auto mt-40">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($categories as $category): ?>
            <a href="dashboard.php?category=<?php echo urlencode($category['categoryName']); ?>" class="category-link">
                <div class="bg-white p-4 rounded-lg shadow-md h-full">
                    <h2 class="text-xl font-semibold mb-2"><?php echo $category['categoryName']; ?></h2>
                    <p class="text-gray-600"><?php echo $category['cat_desc']; ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'inc/footer.php'; ?>
</body>
</html>
