<?php
include 'database/dbconnection.php';
include 'inc/header.php'; 

// Fetch all categories from the database
$stmt = $conn->query("SELECT categoryName FROM category");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
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
            <a href="dashboard.php?category=<?php echo urlencode($category); ?>" class="category-link">
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-2"><?php echo $category; ?></h2>
                    <!-- You can add more information about each category here if needed -->
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'inc/footer.php'; ?>
</body>
</html>
