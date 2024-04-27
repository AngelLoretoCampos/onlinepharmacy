<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if delete request is sent
    if (isset($_POST['delete_id'])) {
        // Begin a transaction to ensure atomicity
        $pdo->beginTransaction();
        
        try {
            // Check if the product is associated with any orders
            $delete_id = $_POST['delete_id'];
            $stmtCheckOrders = $pdo->prepare("SELECT COUNT(*) as count FROM order_items WHERE product_id = :id");
            $stmtCheckOrders->bindValue(':id', $delete_id);
            $stmtCheckOrders->execute();
            $orderCount = $stmtCheckOrders->fetch(PDO::FETCH_ASSOC)['count'];
            
            // If there are orders associated with the product, display a message and abort the deletion
            if ($orderCount > 0) {
                echo '<script>
                        if (confirm("This product can\'t be deleted because it is currently on sale, in the cart, or ordered by users. Click OK to dismiss.")) {
                            window.location.href = "product_list.php";
                        }
                    </script>';
            } else {
                // Delete associated order_items records
                $stmtOrderItems = $pdo->prepare("DELETE FROM order_items WHERE product_id = :id");
                $stmtOrderItems->bindValue(':id', $delete_id);
                $stmtOrderItems->execute();
                
                // Now delete the product itself
                $stmtDeleteProduct = $pdo->prepare("DELETE FROM products WHERE id = :id");
                $stmtDeleteProduct->bindValue(':id', $delete_id);
                $stmtDeleteProduct->execute();
                
                // Commit the transaction if all queries succeeded
                $pdo->commit();
                
                // Display a success message and refresh the page after 1 second
                echo '<script>
                        if (confirm("Product deleted successfully. Click OK to dismiss.")) {
                            window.location.href = "product_list.php";
                        }
                    </script>';
            }
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $pdo->rollBack();
            
            // Display an alert box with the error message
            echo '<script>
                    if (confirm("Error: ' . $e->getMessage() . '. Click OK to dismiss.")) {
                        window.location.href = "product_list.php";
                    }
                </script>';
        }
    }
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>
