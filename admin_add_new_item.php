<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

// Include database connection file
include('db_connection.php'); // Make sure to replace this with your actual database connection file

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $quantity = $_POST['quantity'];
    $summary = $_POST['summary'];

    // Insert new item into the database
    $stmt = $conn->prepare("INSERT INTO items (title, type, quantity, summary) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $title, $type, $quantity, $summary);

    if ($stmt->execute()) {
        // Redirect to admin_items.php on success
        header("Location: admin_items.php");
        exit(); // Make sure to exit after redirection
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>


<!-- Title Section -->
<div class="container text-center my-4">
    <h1>Add New Item</h1>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">Library System</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a href="index.php" class="nav-link">Home</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Search</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link active">Items</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Users</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Transactions</a>
            </li>
        </ul>
        <ul class="navbar-nav"> <!-- Aligns right -->
            <li class="nav-item">
                <a href="#" class="nav-link text-red">My Account</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">

    <form method="post" action="">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select class="form-control" id="type" name="type" required>
                <option value="book">Book</option>
                <option value="movie">Movie</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
        <div class="form-group">
            <label for="summary">Summary</label>
            <textarea class="form-control" id="summary" name="summary" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Item</button>
    </form>
</div>

</body>
</html>
