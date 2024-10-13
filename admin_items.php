<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

// Include database connection file
include('db_connection.php'); // Make sure to replace this with your actual database connection file

// Handle item deletion
if (isset($_POST['delete'])) {
    $itemId = $_POST['item_id'];
    $conn->query("DELETE FROM items WHERE id = $itemId");
}

// Fetch all items
$result = $conn->query("SELECT * FROM items");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Items</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>


<!-- Title Section -->
<div class="container text-center my-4">
    <h1>Manage Items</h1>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Library System</a>
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

<div class="container mt-4">
    <!-- Add new item button -->
    <a href="admin_add_new_item.php" class="btn btn-primary">Add New Item</a>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Visible</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo htmlspecialchars($item['type']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo $item['is_visible'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <!-- Edit button -->
                        <a href="admin_edit_item.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">Edit</a>

                        <!-- Delete form -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>