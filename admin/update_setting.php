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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $id = $_POST['id'];
    $about = $_POST['about'];

    // Check if an image file was uploaded
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Read the contents of the uploaded image file
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        
        // Update system setting with the image data in the database
        $stmt = $conn->prepare("UPDATE systemsetting SET about = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssi", $about, $imageData, $id);
    } else {
        // Update system setting without the image data in the database
        $stmt = $conn->prepare("UPDATE systemsetting SET about = ? WHERE id = ?");
        $stmt->bind_param("si", $about, $id);
    }
    
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "System setting updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update system setting!";
    }

    $stmt->close();
}

// Close the connection
$conn->close();

// Redirect back to the system settings page
header("Location: settings.php");
exit();
?>
