<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $sex = $_POST['sex'];
    $phone_number = $_POST['phone_number'];
    $shipping_address = $_POST['shipping_address'];

    // Include database connection file
    include('db_connection.php'); // Make sure to replace this with your actual database connection file

    // Check if email already exists
    $checkEmailStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $result = $checkEmailStmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='alert alert-danger'>Email already exists. Please use a different email.</div>";
    } else {
        // Check if passwords match
        if ($password !== $confirm_password) {
            echo "<div class='alert alert-danger'>Passwords do not match. Please try again.</div>";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, sex, phone_number, shipping_address, role_fk) VALUES (?, ?, ?, ?, ?, ?, ?, DEFAULT)");
            $stmt->bind_param("sssssss", $first_name, $last_name, $email, $hashed_password, $sex, $phone_number, $shipping_address);
            
            if ($stmt->execute()) {
                // Redirect to login page on success
                header("Location: login.php");
                exit(); // Ensure that no further code is executed after the redirect
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
        }
    }

    $stmt->close();
    $checkEmailStmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>User Registration</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required placeholder="First name">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required placeholder="Last name">
        </div>
        <div class="form-group">
            <label for="sex">Sex</label>
            <select class="form-control" id="sex" name="sex" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter your phone number">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
        </div>
        <div class="form-group">
            <label for="shipping_address">Shipping Address</label>
            <textarea class="form-control" id="shipping_address" name="shipping_address" placeholder="Enter your shipping address"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

</body>
</html>
