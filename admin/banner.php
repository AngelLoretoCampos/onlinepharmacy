<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Slide Pictures</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<?php include 'inc/header.php'; ?>

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Slide Pictures</h1>

    <!-- Image Upload Form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-gray-700">Choose Image:</label>
            <input type="file" name="image" id="image" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>
        <button type="submit" name="upload" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Upload Image</button>
    </form>

    <!-- Display Saved Images -->
    <h2 class="text-xl font-semibold mt-8 mb-4">Saved Images</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload'])) {
            // Handle image upload
            if ($_FILES["image"]["error"] == UPLOAD_ERR_OK) {
                $imageData = file_get_contents($_FILES["image"]["tmp_name"]);
                if (!empty($imageData)) {
                    // Save image data to database
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "onlinepharmacy_db";

                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "INSERT INTO slidepic (image_data) VALUES (?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("b", $imageData);
                    if ($stmt->execute()) {
                        echo '<div class="bg-white rounded-lg shadow-md p-2">
                                <img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Slide Image" class="w-full h-32 object-cover">
                              </div>';
                    } else {
                        echo '<p class="text-red-600">Failed to upload image.</p>';
                    }

                    $stmt->close();
                    $conn->close();
                } else {
                    echo '<p class="text-red-600">Error: Image data is empty.</p>';
                }
            } else {
                echo '<p class="text-red-600">Error uploading image.</p>';
            }
        } else {
            // Fetch and display saved images
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "onlinepharmacy_db";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM slidepic";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="bg-white rounded-lg shadow-md p-2">
                            <img src="data:image/jpeg;base64,' . base64_encode($row['image_data']) . '" alt="Slide Image" class="w-full h-32 object-cover">
                          </div>';
                }
            } else {
                echo '<p class="text-center">No images found.</p>';
            }

            $conn->close();
        }
        ?>
    </div>
</div>

</body>
</html>
