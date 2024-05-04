<?php
// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Define database connection details
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

// Create a PDO database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category'], $_POST['cat_desc'])) {
    try {
        // Retrieve form data
        $category = $_POST['category'];
        $cat_desc = $_POST['cat_desc'];
        
        // Insert category into the database
        $sql = "INSERT INTO category (categoryName, cat_desc) VALUES (:category, :cat_desc)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        $stmt->execute();

        // Redirect to category list page after successful insertion
        header("Location: category_list.php");
        exit();
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    } catch (Exception $e) {
        // Handle other exceptions
        echo "Error: " . $e->getMessage();
    }
}
?>
