<?php
session_start();

// Check if session message is set
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    // Unset session message to prevent it from appearing again
    unset($_SESSION['message']);
}

// Check if session error is set
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    // Unset session error to prevent it from appearing again
    unset($_SESSION['error']);
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        // JavaScript function to hide alert messages after 1 second
        setTimeout(function() {
            var alertMessage = document.querySelector('.alert');
            if (alertMessage) {
                alertMessage.style.display = 'none';
            }
        }, 1000); // 1000 milliseconds = 1 second
    </script>
</head>

<body class="bg-gray-100">

    <main class="ml-64 mt-10">
        <div class="container bg-white rounded-lg shadow-lg p-6 mx-auto">

            <h1 class="text-3xl font-semibold mb-8">System Settings</h1>

            <!-- Display session message if set -->
            <?php if (isset($message)) : ?>
                <div class="alert bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline"><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <!-- Display session error if set -->
            <?php if (isset($error)) : ?>
                <div class="alert bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php foreach ($settings as $setting) : ?>
            <form action="update_setting.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $setting['id']; ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="short_<?php echo $setting['id']; ?>">Short Name</label>
                        <div class="mb-4 border  rounded-md p-2 border-green-500">
                            <input type="text" name="short" id="short_<?php echo $setting['id']; ?>" value="<?php echo $setting['short']; ?>" class="focus:outline-none w-full ">
                        </div>

                        <label class="block text-sm font-medium text-gray-700" for="longname_<?php echo $setting['id']; ?>">Long Name</label>
                        <div class="mb-4 border rounded-md p-2 border-green-500">
                            <input type="text" name="longname" id="longname_<?php echo $setting['id']; ?>" value="<?php echo $setting['longname']; ?>" class="focus:outline-none w-full">
                        </div>

                        <label class="block text-sm font-medium text-gray-700" for="about_<?php echo $setting['id']; ?>">About</label>
                        <div class="mb-4 border rounded-md p-2 border-green-500">
                            <textarea name="about" id="about_<?php echo $setting['id']; ?>" rows="3" class="focus:outline-none  w-full resize-none"><?php echo $setting['about']; ?></textarea>
                        </div>

                    </div>

                    <!-- Second column -->
                    <div>
                            <label class="block text-sm font-medium text-gray-700" for="image_<?php echo $setting['id']; ?>">Upload New Logo</label>
                            <div class="mb-4 border rounded-xl border-green-500 relative">
                                <!-- Styled button to mimic file input -->
                                <label for="image_<?php echo $setting['id']; ?>" class="absolute left-0 bg-green-500 text-white px-4 py-3 rounded-l-lg cursor-pointer">Choose File</label>
                                <!-- Actual file input (hidden) -->
                                <input type="file" name="image" id="image_<?php echo $setting['id']; ?>" accept="image/*" class="opacity-0 left-0 px-4 m-2.5 cursor-pointer" onchange="showFileName(this)">
                                <!-- Display selected file name -->
                                <span id="file-name_<?php echo $setting['id']; ?>" class="absolute left-0 bg-white px-4 py-3 rounded-l-lg cursor-pointer hidden"></span>
                            </div>

                            <script>
                                // JavaScript function to show selected file name
                                function showFileName(input) {
                                    var fileName = input.files[0].name;
                                    var fileNameElement = document.getElementById('file-name_<?php echo $setting['id']; ?>');
                                    fileNameElement.textContent = fileName;
                                    fileNameElement.classList.remove('hidden');
                                }
                            </script>


                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Current Logo</label>
                            <?php if (!empty($setting['image'])) : ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($setting['image']); ?>" alt="Current Image" class="mt-2 w-24 h-24 border-2 items-center border-green-500 rounded-full">
                            <?php else : ?>
                            <p class="mt-2 text-gray-500">No image uploaded</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
                <div class="mr-10 text-right">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 ">Update</button>
                </div>
            </form>
            <?php endforeach; ?>
        </div>
    </main>

</body>

</html>
