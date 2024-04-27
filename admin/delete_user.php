<?php
// Include database connection
include '../database/dbconnection.php';

// Check if user ID is provided
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    try {
        // Prepare and execute the SQL statement to delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        // Redirect back to the page displaying all users
        header("Location: client_list.php");
        exit();
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
} else {
    // If user ID is not provided, redirect back to the page displaying all users
    header("Location: client_list.php");
    exit();
}
?>
