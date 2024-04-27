<?php
include 'database/dbconnection.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $user_id);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Redirect to dashboard or homepage if user doesn't exist
    header("Location: dashboard.php");
    exit();
}


// Fetch barangays
$stmt = $conn->prepare("SELECT * FROM barangays");
$stmt->execute();
$barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $barangay = $_POST['barangay'];
    $additional_address = $_POST['additional_address'];
    $password = $_POST['password'];

    // Verify password
    if (password_verify($password, $user['password'])) {
        $stmt = $conn->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email, province = :province, city = :city, barangay = :barangay, additional_address = :additional_address WHERE id = :id");
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':province', $province);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':barangay', $barangay);
        $stmt->bindParam(':additional_address', $additional_address);
        $stmt->bindParam(':id', $user_id);

        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
        } else {
            $error = "Error updating profile.";
        }
    } else {
        $error = "Incorrect password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome -->

</head>
<body class="bg-gray-100 ">

<?php include 'inc/header.php'; ?>

<main class="container mx-auto mt-40 p-4">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <div class="text-right"><a href="my_orders.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">My Orders <i class="fa-solid fa-arrow-right"></i></a></div>

        <h1 class="text-2xl font-semibold mb-4">Profile Manager</h1>

        <?php if (isset($message)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="w-full max-w-lg">
            <div class="mb-4">
                <label for="firstname" class="block text-gray-700 text-sm font-bold mb-2">First Name:</label>
                <input type="text" name="firstname" id="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="lastname" class="block text-gray-700 text-sm font-bold mb-2">Last Name:</label>
                <input type="text" name="lastname" id="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="province" class="block text-gray-700 text-sm font-bold mb-2">Province:</label>
                <input type="text" name="province" id="province" value="<?php echo htmlspecialchars($user['province']); ?>" class="shadow bg-gray-400 appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
            </div>
            <div class="mb-4">
                <label for="city" class="block text-gray-700 text-sm font-bold mb-2">City:</label>
                <input type="text" name="city" id="city" readonly value="<?php echo htmlspecialchars($user['city']); ?>" class="shadow bg-gray-400  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="barangay" class="block text-gray-700 text-sm font-bold mb-2">Barangay:</label>
                <select name="barangay" id="barangay" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php foreach ($barangays as $row): ?>
                        <option value="<?php echo $row['name']; ?>" <?php echo ($user['barangay'] === $row['name']) ? 'selected' : ''; ?>><?php echo $row['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="additional_address" class="block text-gray-700 text-sm font-bold mb-2">Additional Address:</label>
                <textarea name="additional_address" id="additional_address" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($user['additional_address']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Current Password:</label>
                <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update</button>
            </div>
        </form>
    </div>
</main>
