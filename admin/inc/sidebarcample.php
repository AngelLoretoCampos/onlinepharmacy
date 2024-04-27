<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aligned Sidebar with Menu Icons</title>
<!-- Include Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-8VL+nEk5KIWiMcXGxgVbUGoUwjDyAEd9XywzDMRnpbtlI1Mwq7o+GoRSqA02wJeP" crossorigin="anonymous">
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }
    .container {
        display: flex;
    }
    .sidebar {
        width: 200px;
        background-color: #333;
        color: white;
        padding: 20px;
    }
    .sidebar h2 {
        margin-top: 0;
    }
    .menu {
        list-style: none;
        padding: 0;
    }
    .menu li {
        margin-bottom: 10px;
    }
    .menu li a {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    .menu li a .fa {
        margin-right: 10px;
    }
    .main-content {
        flex: 1;
        padding: 20px;
    }
    /* For responsiveness */
    @media (max-width: 768px) {
        .container {
            flex-direction: column;
        }
        .sidebar, .main-content {
            width: 100%;
        }
    }
</style>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <h2>Sidebar</h2>
        <ul class="menu">
            <li><a href="#"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="#"><i class="fas fa-info-circle"></i>About</a></li>
            <li><a href="#"><i class="fas fa-cogs"></i>Services</a></li>
            <li><a href="#"><i class="fas fa-envelope"></i>Contact</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Main Content</h1>
        <p>This is the main content area. It will adjust based on the sidebar's width.</p>
    </div>
</div>

</body>
</html>
