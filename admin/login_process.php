<?php
include '../database/dbconnection.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['email'];
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
        $message = "Invalid Employee ID or Password";
    }
}
?>

