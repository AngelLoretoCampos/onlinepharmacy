<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if product ID is provided and delete the product
    if (isset($_GET['id'])) {
        $delete_id = $_GET['id'];

        // Delete associated order_items records
        $stmtOrderItems = $pdo->prepare("DELETE FROM order_items WHERE product_id = :id");
        $stmtOrderItems->bindValue(':id', $delete_id);
        $stmtOrderItems->execute();

        // Now delete the product itself
        $stmtDeleteProduct = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmtDeleteProduct->bindValue(':id', $delete_id);
        $stmtDeleteProduct->execute();

        // Redirect back to the product list page
        header("Location: product_list.php");
        exit; // Ensure that no further output is sent after redirection
    } else {
        // Redirect to product list page if product ID is not provided
        header("Location: product_list.php");
        exit;
    }
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>
