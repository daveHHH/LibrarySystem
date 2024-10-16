<?php
// Include the database connection
include 'db_connection.php'; // Make sure this path is correct

// Load the XML file
$xml = simplexml_load_file('xml/table_structure.xml');

if ($xml === false) {
    die("Error loading XML file.");
}

// Drop the existing items table if it exists
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("DROP TABLE IF EXISTS items");

// Start building the SQL for creating the new table
$createTableSQL = "CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP"; // Add created_at field

// Loop through each field in the XML and define the SQL types
foreach ($xml->field as $field) {
    $name = $conn->real_escape_string((string)$field->name);
    $type = (string)$field->type;

    // Determine SQL column type based on XML type
    switch ($type) {
        case 'string':
            $createTableSQL .= ", $name VARCHAR(255)";
            break;
        case 'integer':
            $createTableSQL .= ", $name INT";
            break;
        case 'date':
            $createTableSQL .= ", $name DATE";
            break;
        case 'enum':
            $enumValues = (string)$field->enumValues;
            $enumArray = explode(',', $enumValues);
            $enumList = "'" . implode("','", array_map('trim', $enumArray)) . "'";
            $createTableSQL .= ", $name ENUM($enumList)";
            break;
        default:
            die("Unsupported field type: $type");
    }
}

// Close the SQL statement
$createTableSQL .= ")";

// Execute the query to create the new table
if ($conn->query($createTableSQL) === TRUE) {
    // Redirect with success message
    header("Location: admin_edit_table_structure.php?result=success");
    exit();
} else {
    // Redirect with error message
    header("Location: admin_edit_table_structure.php?result=error&message=" . urlencode($conn->error));
    exit();
}

// Close the database connection
$conn->close();
?>
