<?php
session_start();
include('connection.php');


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $allowedTables = ['signup', 'staff', 'staffreciept'];

    if (isset($_POST['delete']) && in_array($_POST['table'], $allowedTables)) {
        $table = $_POST['table'];
        $id = intval($_POST['id']);

        $sql = "DELETE FROM $table WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "Record deleted successfully.";
        } else {
            echo "Error deleting record: " . $stmt->error;
        }
        $stmt->close();

    } elseif (isset($_POST['update']) && in_array($_POST['table'], $allowedTables)) {
        $table = $_POST['table'];
        $id = intval($_POST['id']);
        $updates = $_POST['updates'];

        if (is_array($updates) && !empty($updates)) {
            $set_clause = [];
            $params = [];
            $types = "";

            foreach ($updates as $column => $value) {
                $set_clause[] = "$column = ?";
                $params[] = $value;
                $types .= "s";
            }

            $params[] = $id;
            $types .= "i";

            $set_clause = implode(", ", $set_clause);
            $sql = "UPDATE $table SET $set_clause WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                echo "Record updated successfully.";
            } else {
                echo "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "No updates provided.";
        }
    }
}

$tables = ['signup', 'staff', 'staffreciept'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #333;
            color: white;
            padding: 15px;
        }
        .header .logout-btn {
            float: right;
        }
        .container {
            margin-top: 20px;
        }
        table {
            margin-bottom: 40px;
        }
        .btn {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Admin Dashboard</h2>
    <a href="logout.php" class="btn btn-warning logout-btn">Logout</a>
</div>

<div class="container">
    <?php foreach ($tables as $table): ?>
        <h3><?php echo ucfirst($table); ?></h3>
        <?php
        $result = $conn->query("SELECT * FROM $table");
        if ($result && $result->num_rows > 0):
        ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <?php while ($field = $result->fetch_field()): ?>
                            <th><?php echo $field->name; ?></th>
                        <?php endwhile; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <?php foreach ($row as $value): ?>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            <?php endforeach; ?>
                            <td>
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal"
                                        onclick="openUpdateModal('<?php echo $table; ?>', '<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES); ?>')">
                                    Update
                                </button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="table" value="<?php echo $table; ?>">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records found in <?php echo ucfirst($table); ?> table.</p>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="updateTable" name="table">
                    <input type="hidden" id="updateId" name="id">
                    <div id="updateFields"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openUpdateModal(table, rowData) {
        const row = JSON.parse(rowData);
        document.getElementById('updateTable').value = table;
        document.getElementById('updateId').value = row.id;

        const updateFields = document.getElementById('updateFields');
        updateFields.innerHTML = '';

        for (const [key, value] of Object.entries(row)) {
            if (key !== 'id') {
                updateFields.innerHTML += `
                    <div class="mb-3">
                        <label for="${key}" class="form-label">${key}</label>
                        <input type="text" class="form-control" id="${key}" name="updates[${key}]" value="${value}">
                    </div>
                `;
            }
        }
    }
</script>

</body>
</html>
