<?php
// Start session
session_start();

// Include database connection file
include('db_connection.php'); // Make sure to replace this with your actual database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user info from database
$query = "SELECT first_name, last_name, sex, phone_number, email, role_fk, shipping_address FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the user data
    $user = $result->fetch_assoc();
} else {
    echo "No user found.";
    exit();
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container text-center my-4">
        <h1>My Account</h1>
    </div>

<?php 
    include('layouts/navbar.php')
?>

    <div class="container mt-5">
        <form>
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" class="form-control" id="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" class="form-control" id="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="sex">Sex</label>
                <input type="text" class="form-control" id="sex" value="<?php echo htmlspecialchars($user['sex']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="phoneNumber">Phone Number</label>
                <input type="text" class="form-control" id="phoneNumber" value="<?php echo htmlspecialchars($user['phone_number']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <input type="text" class="form-control" id="role" value="<?php echo htmlspecialchars($user['role_fk']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="shippingAddress">Shipping Address</label>
                <textarea class="form-control" id="shippingAddress" rows="3" readonly><?php echo htmlspecialchars($user['shipping_address']); ?></textarea>
            </div>

            <button type="button" class="btn btn-primary" onclick="window.location.href='#'">Edit Information</button>
        </form>
    </div>
    <!-- Bootstrap JS and dependencies (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
