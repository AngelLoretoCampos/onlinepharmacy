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

// Fetch system settings
$sql = "SELECT * FROM systemsetting";
$result = $conn->query($sql);

$settings = [];
if ($result->num_rows > 0) {
    // Fetching all settings into an associative array
    while ($row = $result->fetch_assoc()) {
        $settings[] = $row;
    }
}

// Close the connection
$conn->close();
include 'inc/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 ml-64">

<main>
    <div class="container mt-20">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-semibold mb-4">System Settings</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($settings as $setting) : ?>
                    <div class="w-full rounded-lg p-4">
                        <form action="update_setting.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $setting['id']; ?>">
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700" for="about_<?php echo $setting['id']; ?>">Short Name</label>
                                <input type="text" name="short" value="<?php echo $setting['short']; ?>">
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700" for="about_<?php echo $setting['id']; ?>">Long Name</label>
                                <input type="text" name="logname" value="<?php echo $setting['longname']; ?>">
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700" for="about_<?php echo $setting['id']; ?>">About:</label>
                                <textarea name="about" id="about_<?php echo $setting['id']; ?>" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"><?php echo $setting['about']; ?></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700" for="image_<?php echo $setting['id']; ?>">Upload Image:</label>
                                <input type="file" name="image" id="image_<?php echo $setting['id']; ?>" accept="image/*" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>
</body>
</html>
