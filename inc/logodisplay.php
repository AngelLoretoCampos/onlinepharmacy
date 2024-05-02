
<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinepharmacy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch short name, long name, and logo from the database
$sql = "SELECT short, longname, about, image FROM systemsetting";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $shortName = $row['short'];
    $longName = $row['longname'];
    $about = $row['about'];
    $image = $row['image']; // Assuming the logo is stored as a longblob in the database

    // Convert the binary data to base64 encoding
    $logoBase64 = base64_encode($image);
} else {
    $shortName = "Lyfe Pharmacy"; // Default short name if not found
    $longName = "Online Lyfe Pharmacy"; // Default long name if not found
    $about = ""; // Default about if not found
    $logoBase64 = ""; // Empty base64 encoded logo if not found
}

// Close the connection
$conn->close();
?>
