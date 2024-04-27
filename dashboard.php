<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
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
<?php
include 'inc/header.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinepharmacy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from database
if(isset($_GET['query'])) {
    $search_query = $_GET['query'];
    $sql = "SELECT id, product_name, price, product_image FROM products WHERE product_name LIKE '%$search_query%'";
} else {
    $sql = "SELECT id, product_name, price, product_image FROM products";
}

$result = $conn->query($sql);
?>

<main class="p-4 mt-40">

    <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
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
            echo '<p class="text-center">No products found.</p>';
        }
        ?>
    </section>
</main>

<?php include 'inc/footer.php'; ?>

</body>
</html>

