<?php
$host = 'localhost';
$dbname = 'onlinepharmacy_db';
$username = 'root';
$password = '';

if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT DISTINCT variation FROM inventory WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($variations);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
