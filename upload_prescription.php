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
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['prescription'])) {
    $prescription_file = $_FILES['prescription'];

    // Check if file is uploaded without errors
    if ($prescription_file['error'] === 0) {
        $file_tmp = $prescription_file['tmp_name'];

        $prescription_data = file_get_contents($file_tmp);

        // Insert the prescription file directly as a LONGBLOB into the orders table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, prescription_data) VALUES (?, ?)");
        $stmt->bind_param("ib", $user_id, $prescription_data);

        if ($stmt->execute()) {
            $message = "Prescription uploaded successfully!";
        } else {
            $error = "Error uploading prescription.";
        }
    } else {
        $error = "Error uploading file.";
    }
}

header("Location: checkout.php?message=$message&error=$error");
exit;
?>
