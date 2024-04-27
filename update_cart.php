<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinepharmacy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Return an error response if the user is not logged in
    http_response_code(401);
    exit("Unauthorized");
}

$user_id = $_SESSION['user_id'];

// Update Cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Update the quantity in the cart table
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Return a success response
        http_response_code(200);
        exit("Quantity updated successfully");
    } else {
        // Return an error response if the update failed
        http_response_code(500);
        exit("Failed to update quantity");
    }
} else {
    // Return an error response if the request is invalid
    http_response_code(400);
    exit("Bad request");
}
?>
