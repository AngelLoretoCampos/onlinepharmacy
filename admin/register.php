<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen">

<div class="flex justify-center items-center h-full">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-semibold mb-4 text-center">Admin Registration</h1>

        <form method="post" action="register_process.php" class="space-y-4">
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-600">Full Name:</label>
                <input type="text" name="full_name" id="full_name" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-600">Employee ID:</label>
                <input type="text" name="employee_id" id="employee_id" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600">Password:</label>
                <input type="password" name="password" id="password" required class="mt-1 p-2 w-full border rounded-md">
            </div>

            <div>
                <button type="submit" class="w-full bg-green-500 text-white p-2 rounded-md hover:bg-green-600">Register</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
