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

if (isset($_POST['order_id']) && isset($_POST['order_status'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $order_status, $order_id);

    if ($stmt->execute()) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 mb-4" role="alert">
                Order status updated successfully!
              </div>';
        
        // Refresh the page after 2 seconds
        echo '<script>
                setTimeout(function(){
                    window.location.href = "view_order.php?order_id=' . $order_id . '";
                }, 0);
              </script>';
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 mb-4" role="alert">
                Error updating order status: ' . $stmt->error . '
              </div>';
    }

    $stmt->close();
    $conn->close();
} else {
    echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-2 mb-4" role="alert">
            Invalid request!
          </div>';
}
?>
