<?php
include '../database/dbconnection.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['full_name'] = $admin['full_name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Invalid Email or Password";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen" style="background-image: url('../system images/pharmacy-theme-with-green-leaves-pills-and-capsules-on-dark-background-capsules-herb-supplements-on-green-leaves-background-ai-generated-free-photo.jpg'); background-size: cover; background-position: center;">

<div class="flex justify-center items-center h-full">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-semibold mb-4 text-center">Employee Login</h1>

        <form method="post" action="" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-600">Email:</label>
                <input type="email" name="email" id="email" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600">Password:</label>
                <input type="password" name="password" id="password" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <button type="submit" class="w-full bg-green-500 text-white p-2 rounded-md hover:bg-green-600">Login</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
