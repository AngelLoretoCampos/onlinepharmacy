<?php
include 'database/dbconnection.php';

$message = '';
$barangays = [];

// Fetch barangays from the database
$stmt = $conn->prepare("SELECT * FROM barangays");
$stmt->execute();
$barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $barangay = $_POST['barangay'];
    $additional_address = $_POST['additional_address'];

    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, password, contact, gender, email, province, city, barangay, additional_address) VALUES (:firstname, :lastname, :password, :contact, :gender, :email, :province, :city, :barangay, :additional_address)");
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':contact', $contact);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':province', $province);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':barangay', $barangay);
    $stmt->bindParam(':additional_address', $additional_address);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        $message = "Error: Cannot register user";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Include FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-2xl relative">
        <!-- Close icon -->
        <a href="dashboard.php" class="absolute top-2 right-3 text-gray-600 hover:text-gray-900">
            <i class="fas fa-times"></i>
        </a>

        <h1 class="text-2xl font-semibold mb-4 text-center">Register</h1>
        
        <?php if ($message): ?>
            <p class="text-red-500 mb-4 text-center"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="post" action="" class="grid grid-cols-2 gap-4">
            <!-- Column 1 -->
            <div>
                <label for="firstname" class="block text-sm font-medium text-gray-600">Firstname:</label>
                <input type="text" name="firstname" id="firstname" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <label for="lastname" class="block text-sm font-medium text-gray-600">Lastname:</label>
                <input type="text" name="lastname" id="lastname" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600">Password:</label>
                <input type="password" name="password" id="password" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <label for="contact" class="block text-sm font-medium text-gray-600">Contact:</label>
                <input type="number" name="contact" id="contact" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <label for="gender" class="block text-sm font-medium text-gray-600">Gender:</label>
                <select name="gender" id="gender" required class="mt-1 p-2 w-full border rounded-md">
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-600">Email:</label>
                <input type="email" name="email" id="email" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <!-- Column 2 -->
            <div>
                <label for="province" class="block text-sm font-medium text-gray-600">Province:</label>
                <input type="text" name="province" id="province" value="Albay" readonly class="mt-1 p-2 w-full border rounded-md bg-gray-200">
            </div>

            <div>
                <label for="city" class="block text-sm font-medium text-gray-600">City:</label>
                <input type="text" name="city" id="city" value="Camalig" readonly class="mt-1 p-2 w-full border rounded-md bg-gray-200">
            </div>

            <div>
                <label for="barangay" class="block text-sm font-medium text-gray-600">Barangay:</label>
                <select name="barangay" id="barangay" required class="mt-1 p-2 w-full border rounded-md">
                    <option value="" disabled selected>Select Barangay</option>
                    <?php foreach ($barangays as $barangay): ?>
                        <option value="<?php echo $barangay['name']; ?>"><?php echo $barangay['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="additional_address" class="block text-sm font-medium text-gray-600">Additional Address Information:</label>
                <input type="text" name="additional_address" id="additional_address" placeholder="ex. Purok #3" class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div class="col-span-full text-center">
                <button type="submit" class="w-3/4 bg-green-500 text-white p-2 rounded-md hover:bg-green-600">Register</button>
            </div>

        </form>
        
        <div class="mt-4 text-center">Already have an account?
            <a href="login.php" class="text-blue-500 hover:text-blue-700"> Login</a>
        </div>
    </div>
</div>

</body>
</html>
