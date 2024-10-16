<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

// Include database connection file
include('db_connection.php'); // Make sure to replace this with your actual database connection file

// Get item ID from URL
$itemId = $_GET['id'] ?? null;

if ($itemId) {
    // Fetch item details
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if (!$item) {
        echo "<div class='alert alert-danger'>Item not found.</div>";
        exit();
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST['title'];
        $type = $_POST['type'];
        $quantity = $_POST['quantity'];
        $summary = $_POST['summary'];
        $is_visible = isset($_POST['is_visible']) ? 1 : 0; // Checkbox for visibility

        // Update item in the database
        $updateStmt = $conn->prepare("UPDATE items SET title = ?, type = ?, quantity = ?, summary = ?, is_visible = ? WHERE id = ?");
        $updateStmt->bind_param("ssissi", $title, $type, $quantity, $summary, $is_visible, $itemId);

        if ($updateStmt->execute()) {
            // Redirect to admin_items.php on success
            header("Location: admin_items.php");
            exit(); // Make sure to exit after redirection
        } else {
            echo "<div class='alert alert-danger'>Error: " . $updateStmt->error . "</div>";
        }

        $updateStmt->close();
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>No item ID provided.</div>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>


<!-- Title Section -->
<div class="container text-center my-4">
    <h1>Edit Item</h1>
</div>

<?php 
    include('layouts/navbar.php')
?>

<div class="container mt-5">
    
    <form method="post" action="">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select class="form-control" id="type" name="type" required>
                <option value="book" <?php echo ($item['type'] == 'book') ? 'selected' : ''; ?>>Book</option>
                <option value="movie" <?php echo ($item['type'] == 'movie') ? 'selected' : ''; ?>>Movie</option>
                <option value="other" <?php echo ($item['type'] == 'other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" required>
        </div>
        <div class="form-group">
            <label for="summary">Summary</label>
            <textarea class="form-control" id="summary" name="summary" rows="4"><?php echo htmlspecialchars($item['summary']); ?></textarea>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_visible" name="is_visible" <?php echo ($item['is_visible']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_visible">Visible to public</label>
        </div>
        <button type="submit" class="btn btn-primary">Update Item</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
