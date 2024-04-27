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

// Add to Cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity']; // Assuming you have a quantity input in your form

    // Check if the product is already in the cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
    } else {
        // Insert new product into cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
    }

    header("Location: cart.php");
    exit;
}

// Fetch cart items
$sql = "SELECT p.product_name, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome -->
</head>
<body class="bg-gray-100">

<?php include 'inc/header.php'; ?>

<main class="container mx-auto mt-40 p-4">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="text-right"><a href="my_orders.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">My Orders <i class="fa-solid fa-arrow-right"></i></a></div>

        <div class="mb-4 flex justify-between">
            <h1 class="text-2xl font-semibold">Shopping Cart</h1>
        </div>

        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="border">Action</th>
                    <th class="border px-4 py-2">Product Name</th>
                    <th class="border px-4 py-2">Price</th>
                    <th class="border px-4 py-2">Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="border text-center">
                            <form action="delete_from_cart.php" method="post"> 
                                <button type="submit" name="delete_item" class="text-red-500 hover:text-red-700"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['price']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['quantity']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="mt-4 text-right">
            <a href="checkout.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Checkout</a>
        </div>
    </div>
</main>

<?php include 'inc/footer.php'; ?>

</body>
</html>
