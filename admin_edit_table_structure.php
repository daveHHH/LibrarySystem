<?php 
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>

<?php
// Load XML file
$xmlFile = 'xml/table_structure.xml';
$xml = simplexml_load_file($xmlFile);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        // Add new field
        $fieldName = $_POST['fieldName'];
        $fieldType = $_POST['fieldType'];
        $enumValues = $_POST['enumValues'] ?? '';

        // Create new field
        $newField = $xml->addChild('field');
        $newField->addChild('name', htmlspecialchars($fieldName));
        $newField->addChild('type', htmlspecialchars($fieldType));
        
        // If type is enum, add enum values
        if ($fieldType === 'enum' && !empty($enumValues)) {
            $newField->addChild('enumValues', htmlspecialchars($enumValues));
        }

        // Save XML file
        $xml->asXML($xmlFile);
    } elseif ($action === 'delete') {
        // Delete existing field using XPath
        $fieldName = $_POST['fieldName'];
        $fieldsToDelete = $xml->xpath("field[name='$fieldName']");
    
        foreach ($fieldsToDelete as $field) {
            $dom = dom_import_simplexml($field);
            $dom->parentNode->removeChild($dom);
        }
    
        // Save XML file
        $xml->asXML($xmlFile);
    } elseif ($action === 'edit') {
        // Edit existing field
        $oldFieldName = $_POST['oldFieldName'];
        $newFieldName = $_POST['newFieldName'];
        $newFieldType = $_POST['newFieldType'];
        $newEnumValues = $_POST['newEnumValues'] ?? '';

        foreach ($xml->field as $field) {
            if ($field->name == $oldFieldName) {
                $field->name = htmlspecialchars($newFieldName);
                $field->type = htmlspecialchars($newFieldType);
                if ($newFieldType === 'enum' && !empty($newEnumValues)) {
                    $field->enumValues = htmlspecialchars($newEnumValues);
                } else {
                    unset($field->enumValues);
                }
                break;
            }
        }
        
        // Save XML file
        $xml->asXML($xmlFile);
    }
}

// Reload XML after processing to reflect changes
$xml = simplexml_load_file($xmlFile);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Field Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function toggleEnumNewInput(select) {
            const enumInput = document.getElementById('enumValuesDiv');
            enumInput.style.display = select.value === 'enum' ? 'block' : 'none';
        }

        function toggleEnumInput(selectElement) {
            // Get the input field for new enum values
            const enumValuesInput = selectElement.parentElement.querySelector('input[name="newEnumValues"]');
            
            // Show or hide based on the selected value
            if (selectElement.value === 'enum') {
                enumValuesInput.style.display = 'block'; // Show the enum input
            } else {
                enumValuesInput.style.display = 'none'; // Hide the enum input
                enumValuesInput.value = ''; // Clear the input value when hiding
            }
        }
    </script>
</head>
<body>
<!-- Title Section -->
<div class="container text-center my-4">
    <h1>Table Structure</h1>
</div>
<?php 
    include('layouts/navbar.php')
?>
<?php
    if (isset($_GET['result'])) {
        if ($_GET['result'] === 'success') {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Table "items" created successfully.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                  </div>';
        } elseif ($_GET['result'] === 'error' && isset($_GET['message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error creating table: ' . htmlspecialchars($_GET['message']) . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                  </div>';
        }
    }
?>
<div class="container mt-5 mb-5">
    <h2 class="mb-4">Add New Filed</h2>
    <form method="post" class="mb-4">
        <div class="form-row align-items-end">
            <div class="form-group col-md-4">
                <label for="fieldName">Field Name</label>
                <input type="text" name="fieldName" id="fieldName" class="form-control" placeholder="Field Name" required>
            </div>
            <div class="form-group col-md-4">
                <label for="fieldType">Field Type</label>
                <select name="fieldType" id="fieldType" class="form-control" onchange="toggleEnumNewInput(this)" required>
                    <option value="">Select Type</option>
                    <option value="string">String</option>
                    <option value="integer">Integer</option>
                    <option value="enum">Enum</option>
                </select>
            </div>
            <div class="form-group col-md-4" id="enumValuesDiv" style="display:none;">
                <label for="enumValuesInput">Enum Values (comma separated)</label>
                <input type="text" name="enumValues" id="enumValuesInput" class="form-control" placeholder="Enum Values (comma separated)">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <button type="submit" name="action" value="add" class="btn btn-primary">Add Field</button>
            </div>
        </div>
    </form>


    <h2 class="mb-4">Existing Fields On Item Table</h2>
    <ul class="list-group">
        <?php foreach ($xml->field as $field): ?>
            <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-2">
                <div class="mb-2 mb-md-0">
                    <?php echo htmlspecialchars($field->name) . ' (' . htmlspecialchars($field->type); 
                    if (isset($field->enumValues)) echo ', Values: ' . htmlspecialchars($field->enumValues);
                    echo ')'; ?>
                </div>
                <div class="d-flex flex-column flex-md-row align-items-start">
                    <form method="post" action="" style="display:inline;" class="d-flex flex-column flex-md-row align-items-start">
                        <input type="hidden" name="oldFieldName" value="<?php echo htmlspecialchars($field->name); ?>">
                        <input type="text" name="newFieldName" placeholder="New Name" required class="form-control form-control-sm mb-2 mb-md-0 w-auto">
                        <select name="newFieldType" onchange="toggleEnumInput(this)" required class="form-control form-control-sm mb-2 mb-md-0 w-auto">
                            <option value="">Select Type</option>
                            <option value="string">String</option>
                            <option value="integer">Integer</option>
                            <option value="enum">Enum</option>
                        </select>
                        <input type="text" name="newEnumValues" id="newEnumValuesInput" placeholder="Enum Values (comma separated)" style="display:none;" class="form-control form-control-sm mb-2 mb-md-0 w-auto">
                        <button type="submit" name="action" value="edit" class="btn btn-warning btn-sm">Update Field</button>
                    </form>
                    <form method="post" action="" style="display:inline;">
                        <input type="hidden" name="fieldName" value="<?php echo htmlspecialchars($field->name); ?>">
                        <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm mb-2 mb-md-0 mr-md-2">Delete</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2 class="mt-4 mb-4"></h2>
    <form method="post" action="export_to_mysql.php">
        <button class="btn btn-success btn-sm align-right" type="submit" name="export">Export to MySQL</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
