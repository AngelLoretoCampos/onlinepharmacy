<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body class="bg-gray-100">

<?php include 'inc/header.php'; ?>

<main class="p-4 mt-40">
    <div class="max-w-3xl mx-auto bg-white rounded-lg p-6">
        <h1 class="text-3xl font-semibold mb-4">About Us</h1>
        <hr>
        <br>
        <?php
        include 'database/dbconnection.php';

        // Fetch about content from the database
        $stmt = $conn->prepare("SELECT about FROM systemsetting");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $about_content = $row['about'];

        // Display the about content
        echo "<p>$about_content</p>";
        ?>
    </div>
</main>

<?php include 'inc/footer.php'; ?>

</body>
</html>
