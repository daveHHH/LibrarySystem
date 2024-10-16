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
    // Prepare the insert statement
    $columns = [];
    $values = [];
    $types = '';

    // Fetch columns and their types
    $columnsResult = $conn->query("SHOW COLUMNS FROM items");
    while ($column = $columnsResult->fetch_assoc()) {
        $fieldName = $column['Field'];
        $fieldType = $column['Type'];

        // Prepare data for insertion based on form input
        if ($fieldName !== 'id' && $fieldName !== 'created_at') { // Exclude id and created_at

            $value = $_POST[$fieldName] ?? null; // Get the value from POST

            if (strpos($fieldType,'int') !== false) {
                $types .= 'i'; // Integer type
                $values[] = (int)$value;
                var_dump($value);
            } elseif (strpos($fieldType, 'varchar') !== false || strpos($fieldType, 'text') !== false) {
                $types .= 's'; // String type
                $values[] = (string)$value;
            } elseif (strpos($fieldType, 'date') !== false) {
                $types .= 's'; // Date type (as string)
                $values[] = (string)$value;
            } elseif (strpos($fieldType, 'enum') !== false) {
                $types .= 's'; // Enum type (as string)
                $values[] = (string)$value;
            }
            $columns[] = $fieldName; // Add to columns array
        }
    }
 
    // Prepare the insert statement
    if (!empty($columns)) {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $sql = "INSERT INTO items (" . implode(',', $columns) . ") VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);

        // Check if prepare was successful
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param($types, ...$values);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to admin_items.php on success
            header("Location: admin_items.php");
            exit(); // Make sure to exit after redirection
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>No valid columns to insert.</div>";
    }

    $stmt->close();
}

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

<?php include('layouts/navbar.php'); ?>

<div class="container mt-5">
    <form method="post" action="">
        <?php
        // Fetch columns and their types for form generation
        $columnsResult = $conn->query("SHOW COLUMNS FROM items");
        while ($column = $columnsResult->fetch_assoc()) {
            $fieldName = $column['Field'];
            $fieldType = $column['Type'];

            // Generate form fields based on column types
            if ($fieldName === 'id' || $fieldName === 'created_at') {
                continue; // Skip id and created_at fields
            }

            echo '<div class="form-group">';
            echo '<label for="' . htmlspecialchars($fieldName) . '">' . htmlspecialchars(ucfirst($fieldName)) . '</label>';

            if (strpos($fieldType, 'int') !== false) {
                echo '<input type="number" class="form-control" id="' . htmlspecialchars($fieldName) . '" name="' . htmlspecialchars($fieldName) . '" required>';
            } elseif (strpos($fieldType, 'date') !== false) {
                echo '<input type="date" class="form-control" id="' . htmlspecialchars($fieldName) . '" name="' . htmlspecialchars($fieldName) . '" required>';
            } elseif (strpos($fieldType, 'varchar') !== false || strpos($fieldType, 'text') !== false) {
                echo '<input type="text" class="form-control" id="' . htmlspecialchars($fieldName) . '" name="' . htmlspecialchars($fieldName) . '" required>';
            } elseif (strpos($fieldType, 'enum') !== false) {
                // Extract enum values
                preg_match("/^enum\((.+)\)$/", $fieldType, $matches);
                
                if (isset($matches[1])) {
                    // Remove single quotes and split by comma
                    $enumValues = array_map('trim', explode(',', str_replace("'", "", $matches[1])));
                    
                    var_dump($enumValues); // Debugging output
            
                    echo '<select class="form-control" id="' . htmlspecialchars($fieldName) . '" name="' . htmlspecialchars($fieldName) . '" required>';
                    foreach ($enumValues as $value) {
                        echo '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($value) . '</option>';
                    }
                    echo '</select>';
                }
            }

            echo '</div>'; // Close form group
        }
        ?>
        
        <button type="submit" class="btn btn-primary">Add Item</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
