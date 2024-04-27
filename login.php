<?php
include 'database/dbconnection.php';

session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Include FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-96 relative">
        <!-- Close icon -->
        <a href="dashboard.php" class="absolute top-2 right-3 text-gray-600 hover:text-gray-900">
            <i class="fas fa-times"></i>
        </a>

        <h1 class="text-2xl font-semibold mb-4">Login</h1>
        
        <?php if ($message): ?>
            <p class="text-red-500 mb-4"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="post" action="" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-600">Email:</label>
                <input type="text" name="email" id="email" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600">Password:</label>
                <input type="password" name="password" id="password" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <button type="submit" class="w-full bg-green-500 text-white p-2 rounded-md hover:bg-green-600">Login</button>
            </div>
        </form>
        
        <div class="mt-4 text-left">
            No Account?
            <a href="register.php" class="text-blue-500 hover:text-blue-700">Create Account</a>
        </div>
    </div>
</div>

</body>
</html>
