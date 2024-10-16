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

// Fetch column names
$columnsResult = $conn->query("SHOW COLUMNS FROM items");
$columns = [];
while ($column = $columnsResult->fetch_assoc()) {
    // Exclude 'id' and 'created_at' fields
    if ($column['Field'] !== 'id' && $column['Field'] !== 'created_at') {
        $columns[] = $column['Field'];
    }
}
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

<?php include('layouts/navbar.php'); ?>

<div class="container mt-4">
    <!-- Add new item button -->
    <a href="admin_add_new_item.php" class="btn btn-primary mb-4">Add New Item</a>
    <table class="table">
        <thead>
            <tr>
                <?php foreach ($columns as $column): ?>
                    <th><?php echo htmlspecialchars(ucfirst($column)); ?></th>
                <?php endforeach; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $result->fetch_assoc()): ?>
                <tr>
                    <?php foreach ($columns as $column): ?>
                        <td><?php echo htmlspecialchars($item[$column]); ?></td>
                    <?php endforeach; ?>
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>
