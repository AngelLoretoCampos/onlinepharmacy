<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

// Check if category ID is provided
if (isset($_GET['id'])) {
    $categoryId = $_GET['id'];

    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute the SQL statement to delete the category
        $stmt = $pdo->prepare("DELETE FROM category WHERE id = :id");
        $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to the category list page
        header("Location: category_list.php");
        exit();
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
} else {
    // If category ID is not provided, redirect back to the category list page
    header("Location: category_list.php");
    exit();
}
?>
