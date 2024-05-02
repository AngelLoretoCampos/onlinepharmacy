<?php
include 'database/dbconnection.php';

// Check if a search query is provided
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search_query = $_GET['query'];

    // Perform search query in your database
    $stmt = $conn->prepare("SELECT id, product_name, price, product_image FROM products WHERE product_name LIKE ?");
    $stmt->execute(["%$search_query%"]);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If no search query provided, redirect to dashboard.php or display a message
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .product-card {
            width: 250px;
            height: 300px;
        }

        .product-card img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .product-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body class="bg-gray-100">

<?php include 'inc/header.php'; ?>

<main class="p-4 mt-40">

    <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <?php
        if (!empty($search_results)) {
            foreach ($search_results as $row) {
                $product_id = $row['id'];
                $product_name = $row['product_name'];
                $product_price = $row['price'];
                $product_image = base64_encode($row['product_image']); // Convert blob data to base64
                echo '
                <a href="view_product.php?id=' . $product_id . '" class="hover:underline">
                    <div class="bg-white p-4 rounded-lg shadow-md product-card">
                        <img src="data:image/jpeg;base64,' . $product_image . '" alt="' . $product_name . '" class="w-full h-32 mb-4">
                        <h2 class="text-xl font-semibold mb-2 product-name" title="' . $product_name . '">' . $product_name . '</h2>
                        <p class="text-gray-600">₱' . $product_price . '</p>
                    </div>
                </a>';
            }
        } else {
            echo '<p class="text-center">No products found .</p>';
        }
        ?>
    </section>
</main>

<?php include 'inc/footer.php'; ?>

</body>
</html>
