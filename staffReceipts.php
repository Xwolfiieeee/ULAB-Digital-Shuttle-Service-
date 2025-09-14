<?php 
include 'connection.php';

$sql = "SELECT name, email, vrsityId, ticketno, pickupLocation FROM staffreciept";
$result = $conn->query($sql);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['markDone'])) {
    $email = $_POST['email'];
    $ticketno = $_POST['ticketno'] - 1; 

    if ($ticketno > 0) {
        
        $updateSql = "UPDATE staffreciept SET ticketno = ? WHERE email = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("is", $ticketno, $email);
        $stmt->execute();
    } else {
        
        $deleteSql = "DELETE FROM staffreciept WHERE email = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
    }
    header("Location: staffReceipts.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Receipts Management - ULAB</title>
    <link rel="stylesheet" href="staffReceipts.css">
</head>
<body>
    <img src="ulab-logo.png" alt="ULAB Logo" class="university-logo">

    <div class="receipts-container">
        <h2>Student Receipts</h2>

        
        <input type="text" id="searchInput" placeholder="Search by email, ID, or name..." onkeyup="searchReceipts()">
        
        
        <table id="receiptsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>University ID</th>
                    <th>Trips Remaining</th>
                    <th>Drop/Pickup Point</th> 
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr onclick=\"showReceipt('{$row['name']}', '{$row['email']}', '{$row['vrsityId']}', {$row['ticketno']}, '{$row['pickupLocation']}')\">";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['vrsityId']}</td>";
                        echo "<td>{$row['ticketno']}</td>";
                        echo "<td>{$row['pickupLocation']}</td>";  
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No receipts available</td></tr>";  
                }
                ?>
            </tbody>
        </table>

        
        <div id="receipt" class="receipt-container" style="display: none;">
            <img src="ulab-logo.png" alt="ULAB Logo" width="100">
            <h3>Receipt</h3>
            <p><strong>Student Name:</strong> <span id="studentName"></span></p>
            <p><strong>University ID:</strong> <span id="studentId"></span></p>
            <p><strong>Email:</strong> <span id="studentEmail"></span></p>
            <p><strong>Trips Remaining:</strong> <span id="tripsRemaining"></span></p>
            <p><strong>Drop/Pickup Point:</strong> <span id="dropPickupPoint"></span></p>

            <form method="POST">
                <input type="hidden" id="receiptEmail" name="email">
                <input type="hidden" id="receiptTripCount" name="ticketno">
                <button type="submit" name="markDone">Confirm Ticket</button>
            </form>
        </div>
    </div>

    <script>
        function showReceipt(name, email, id, remainingTrips, dropPickupPoint) {
            const receiptContainer = document.getElementById('receipt');
            receiptContainer.style.display = 'block';

            document.getElementById('studentName').textContent = name;
            document.getElementById('studentId').textContent = id;
            document.getElementById('studentEmail').textContent = email;
            document.getElementById('tripsRemaining').textContent = remainingTrips;
            document.getElementById('dropPickupPoint').textContent = dropPickupPoint;  

            document.getElementById('receiptEmail').value = email;
            document.getElementById('receiptTripCount').value = remainingTrips;
        }

        function searchReceipts() {
            const input = document.getElementById('searchInput').value.toUpperCase();
            const table = document.getElementById('receiptsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[0];
                const emailCell = rows[i].getElementsByTagName('td')[1];
                const idCell = rows[i].getElementsByTagName('td')[2];
                if (nameCell || emailCell || idCell) {
                    const nameText = nameCell.textContent || nameCell.innerText;
                    const emailText = emailCell.textContent || emailCell.innerText;
                    const idText = idCell.textContent || idCell.innerText;
                    if (nameText.toUpperCase().indexOf(input) > -1 || emailText.toUpperCase().indexOf(input) > -1 || idText.toUpperCase().indexOf(input) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }
    </script>
</body>
</html>