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

        // Check if the brand exists
        $stmtCheckBrand = $pdo->prepare("SELECT COUNT(*) as count FROM brands WHERE brand_id = ?");
        $stmtCheckBrand->execute([$brandId]);
        $brandCount = $stmtCheckBrand->fetch(PDO::FETCH_ASSOC)['count'];

        if($brandCount > 0) {
            // Delete the brand
            $stmtDeleteBrand = $pdo->prepare("DELETE FROM brands WHERE brand_id = ?");
            $stmtDeleteBrand->execute([$brandId]);

            // Redirect back to the brand list page
            header("Location: brand_list.php");
            exit();
        } else {
            // Brand not found, redirect to brand list page with error message
            header("Location: brand_list.php?error=Brand not found");
            exit();
        }
    } else {
        // Brand ID not provided, redirect to brand list page with error message
        header("Location: brand_list.php?error=Brand ID not provided");
        exit();
    }
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>
