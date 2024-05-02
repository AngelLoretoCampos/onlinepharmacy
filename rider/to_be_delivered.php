<?php

// Start session
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['rider_id'])) {
    header("Location: login.php");
    exit;
}

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinepharmacy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get order details based on the provided ID
if (isset($_GET['id'])) {
    $orderId = $_GET['id'];
    
    $sql = "SELECT o.id, o.user_id, CONCAT(u.firstname, ' ', u.lastname) AS name, CONCAT(u.province, ', ', u.city, ', ', u.barangay, ', ', u.additional_address) AS address,
            u.contact, o.payment_method, o.ref_code, o.total_amount, o.order_date, o.order_status, o.delivery_image 
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    // Check if order status is delivered to hide the upload form
    if ($order['order_status'] === 'Delivered') {
        $uploadHidden = true;
    } else {
        $uploadHidden = false;
    }
} else {
    die("Invalid request.");
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_order'])) {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Image uploaded via file input
        $image = file_get_contents($_FILES['image']['tmp_name']);
    } elseif (!empty($_POST['image'])) {
        // Image captured via webcam
        $image = base64_decode(str_replace('data:image/png;base64,', '', $_POST['image']));
    } else {
        die("No image provided.");
    }
    
    $updateSql = "UPDATE orders SET order_status = 'Delivered', delivery_image = ? WHERE id = ?";
            
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $image, $orderId);
    $stmt->send_long_data(0, $image);
    $stmt->execute();
    
    // Refresh the page after updating
    header("Location: {$_SERVER['PHP_SELF']}?id={$orderId}");
}

// Fetch logo image from the database
$sqlLogo = "SELECT image FROM systemsetting";
$resultLogo = $conn->query($sqlLogo);
$rowLogo = $resultLogo->fetch_assoc();
$imageData = $rowLogo['image']; // Assuming the image is stored as a longblob in the database

// Convert image data to base64 encoding
$logoBase64 = base64_encode($imageData);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Style for modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.9);
        }
        
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 800px;
        }
        
        .modal-content img {
            width: 100%;
            height: auto;
        }
        
        .close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 35px;
            font-weight: bold;
            transition: 0.3s;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="flex justify-between items-center px-4 py-2">
        <!-- Logo -->
        <img src="data:image/jpeg;base64,<?php echo $logoBase64; ?>" alt="Lyfe Pharmacy Logo" class="h-20">

        <!-- History and Logout icons -->
        <div class="ml-auto flex space-x-4">
         <a href="history.php" class="inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                <i class="fas fa-history"></i>
            </a>
            <a href="logout.php" class="inline-block bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg mt-5 shadow-md mx-auto p-4 max-w-screen-md">

        <h2 class="text-xl font-semibold mb-4 text-green-600">Order Details</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <tbody>
                    <tr>
                        <td class="border px-4 py-2 font-semibold">Name</td>
                        <td class="border px-4 py-2"><?php echo $order['name']; ?></td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2 font-semibold">Address</td>
                        <td class="border px-4 py-2"><?php echo $order['address']; ?></td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2 font-semibold">Contact</td>
                        <td class="border px-4 py-2"><?php echo $order['contact']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <ul>
                <li><strong>COD Amount:</strong> ₱<?php echo $order['total_amount']; ?></li>
                <li><strong>Order Status:</strong> <?php echo $order['order_status']; ?></li>
            </ul>
        </div>

        <!-- Display delivery image if it exists -->
        <?php if ($order['delivery_image']): ?>
            <div class="mt-4">
                <img src="data:image/png;base64,<?php echo base64_encode($order['delivery_image']); ?>" alt="Delivery Image" class="max-w-full cursor-pointer" onclick="openModal()">
            </div>
        <?php endif; ?>

        <!-- Modal for the image -->
        <div id="myModal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="img01">
        </div>

       <!-- Video element to capture image -->
<?php if (!$order['delivery_image'] || $order['order_status'] !== 'Delivered'): ?>
    <div class="mt-4 relative">
        <video id="video" width="100%" height="auto" autoplay></video>
        <button id="capture-btn" class=" bg-white text-green-500 px-5 py-4 rounded-full absolute bottom-0 left-1/2 transform -translate-x-1/2" style="z-index: 10;">
            <i class="fa-solid fa-camera"></i>
        </button>
    </div>
<?php endif; ?>


    <!-- Form to submit captured image -->
    <div class="mt-4">
        <form id="capture-form" method="post" enctype="multipart/form-data" <?php if ($uploadHidden) echo 'hidden'; ?>>
            <input type="hidden" name="image" id="image-input">
            <button type="submit" name="confirm_order" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Order Delivered
            </button>
        </form>
    </div>

    <!-- JavaScript to handle modal and image capture -->
    <script>
        // Open the modal
        function openModal() {
            document.getElementById('myModal').style.display = "block";
            var img = document.querySelector('.max-w-full');
            document.getElementById("img01").src = img.src;
        }

        // Close the modal
        function closeModal() {
            document.getElementById('myModal').style.display = "none";
        }

        // Capture image from video element
        const video = document.getElementById('video');
        const captureButton = document.getElementById('capture-btn');
        const imageInput = document.getElementById('image-input');
        const captureForm = document.getElementById('capture-form');

        // Access webcam and stream video
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(error => {
                console.error('Error accessing webcam: ', error);
            });

        // Capture image from video stream
        captureButton.addEventListener('click', () => {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageUrl = canvas.toDataURL('image/png');
            imageInput.value = imageUrl;

            // Show captured image
            const img = document.createElement('img');
            img.src = imageUrl;
            img.classList.add('max-w-full', 'cursor-pointer');
            img.onclick = openModal;
            const captureDiv = document.querySelector('.mt-4');
            captureDiv.innerHTML = ''; // Clear previous content
            captureDiv.appendChild(img);
        });
        
    </script>

</body>
</html>
