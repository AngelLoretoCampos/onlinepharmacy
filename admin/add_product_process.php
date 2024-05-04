<?php
session_start(); // Start the session

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinepharmacy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST["product_name"];
    $product_description = $_POST["product_description"];
    $product_image = file_get_contents($_FILES["product_image"]["tmp_name"]);
    $prescription_required = $_POST["prescription_required"];
    $brand = $_POST["brand"];
    $category = $_POST["category"];
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, product_image, prescription_required, brand, category, quantity, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssdd", $product_name, $product_description, $product_image, $prescription_required, $brand, $category, $quantity, $price);

    if ($stmt->execute()) {
        // Set success message in session variable
        $_SESSION['success_message'] = "Product added successfully.";

        // Redirect to product list page
        header("Location: product_list.php");
        exit; // Ensure script stops executing after redirect
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
