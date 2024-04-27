<?php
// Database connection
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $brand_name = $_POST['brand_name'];
        $brand_description = $_POST['brand_description'];

        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO brands (brand_name, brand_description) VALUES (:brand_name, :brand_description)");

        // Bind parameters
        $stmt->bindParam(':brand_name', $brand_name);
        $stmt->bindParam(':brand_description', $brand_description);

        // Execute SQL statement
        $stmt->execute();

        // Redirect to brand list page with success message
        header("Location: brand_list.php?status=success");
        exit();
    }

} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>
