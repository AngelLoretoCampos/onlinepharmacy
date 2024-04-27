<?php
// Database connection parameters
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$dbUsername = 'root'; // replace with your database username
$dbPassword = ''; // replace with your database password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = $_POST['contact'];
    $password = $_POST['password'];

    try {
        // Create a new PDO instance
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbUsername, $dbPassword);

        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL query to fetch rider by contact
        $sql = "SELECT * FROM riders WHERE contact = :contact";

        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':contact', $contact);

        // Execute the SQL statement
        $stmt->execute();

        // Fetch the rider
        $rider = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($rider && password_verify($password, $rider['password'])) {
            // Start the session
            session_start();

            // Store rider ID in session variable
            $_SESSION['rider_id'] = $rider['id'];

            // Redirect to rider dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Invalid contact or password!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close the database connection
    $conn = null;
}
?>

<!-- ... (HTML part with updated form) ... -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen">

<div class="container mx-auto p-4">
    <!-- Logo -->
    <div class="mb-8 text-center">
        <img src="../system images/bgpp 1.png" alt="Logo" class="h-32 w-32  mx-auto mb-4">
    </div>

    <div class="container p-4">
        <form action="login.php" method="post" class="bg-white rounded-lg p-6 max-w-md mx-auto">
            <div class="text-2xl">Rider Login</div>
            <br>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="contact">Phone Number</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="contact" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="password" name="password" required>
            </div>
            <div class="mb-4">
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full focus:outline-none focus:shadow-outline" type="submit">Login</button>
            </div>
            <p class=" text-center"> No Account? <a href="register.php" class="text-blue-500">Register here</a></p>
        </form>
    </div>

</body>
</html>
