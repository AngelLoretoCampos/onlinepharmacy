<?php
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root'; // replace with your username
$password = ''; // replace with your password

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullName = $_POST['full_name'];
        $contact = $_POST['contact'];
        $password = $_POST['password'];

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // SQL query to insert new rider
        $sql = "INSERT INTO riders (full_name, contact, password) VALUES (:full_name, :contact, :password)";

        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':full_name', $fullName);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':password', $hashedPassword);

        // Execute the SQL statement
        if ($stmt->execute()) {
            echo "Rider registered successfully!";
            
            // Redirect to login page
            header("Location: login.php");
            exit; // Ensure script execution stops here
        } else {
            echo "Error: Cannot register rider";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn = null;
?>

<!-- ... (HTML part remains unchanged) ... -->



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen">

<div class="container mx-auto p-4">
    <!-- Logo -->
    <div class="mb-8 text-center">
        <img src="../system images/bgpp 1.png" alt="Logo" class="h-32 w-32  mx-auto mb-4">
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 max-w-md mx-auto">
        <h1 class="text-2xl font-semibold mb-4 text-center">Rider Register</h1>
        <form action="register.php" method="post">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="full_name">Full Name</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="full_name" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="contact">Contact</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="contact" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="password" name="password" required>
            </div>
            <div class="mb-4">
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full focus:outline-none focus:shadow-outline" type="submit">Register</button>
            </div>
            <p>Already have an Account? <a href="login.php" class="text-blue-800">Login</a></p>
        </form>
    </div>
</div>

</body>
</html>
