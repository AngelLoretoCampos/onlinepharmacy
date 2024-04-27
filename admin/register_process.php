<?php
include '../database/dbconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $employee_id = $_POST['employee_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (full_name, employee_id, password) VALUES (:full_name, :employee_id, :password)");
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        echo "Error: Cannot register admin";
    }
}
?>
