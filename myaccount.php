<?php
session_start();
include('connection.php');


if (!isset($_SESSION['email'])) {
    header("Location: index.php"); 
    exit;
}


$user_email = $_SESSION['email'];
$sql = "SELECT firstName, lastName, email, university_id FROM signup WHERE email = '$user_email'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $account_info = mysqli_fetch_assoc($result);
} else {
    $account_info = ['firstName' => '', 'lastName' => '', 'email' => '', 'university_id' => ''];
}


$ticket_sql = "SELECT SUM(ticketno) AS total_tickets, GROUP_CONCAT(DISTINCT pickupLocation SEPARATOR ', ') AS pickup_locations
               FROM staffreciept 
               WHERE email = '$user_email'";
$ticket_result = mysqli_query($conn, $ticket_sql);

if ($ticket_result && mysqli_num_rows($ticket_result) > 0) {
    $ticket_info = mysqli_fetch_assoc($ticket_result);
    $tickets_left = $ticket_info['total_tickets'] ? $ticket_info['total_tickets'] : 'No tickets available';
    $pickup_location = $ticket_info['pickup_locations'] ? $ticket_info['pickup_locations'] : 'No pickup location available';
} else {
    $tickets_left = 'No tickets available';
    $pickup_location = 'No pickup location available';
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Account</title>
    <link rel="stylesheet" href="account.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>My Account</h1>
    <form>
        
        <div class="mb-3">
            <label for="firstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($account_info['firstName']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="lastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($account_info['lastName']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($account_info['email']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="university_id" class="form-label">University ID</label>
            <input type="text" class="form-control" id="university_id" name="university_id" value="<?php echo htmlspecialchars($account_info['university_id']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="tickets_left" class="form-label">Tickets Left</label>
            <input type="text" class="form-control" id="tickets_left" name="tickets_left" value="<?php echo htmlspecialchars($tickets_left); ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="pickup_location" class="form-label">Pickup Location</label>
            <input type="text" class="form-control" id="pickup_location" name="pickup_location" value="<?php echo htmlspecialchars($pickup_location); ?>" readonly>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
