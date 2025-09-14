<?php
session_start();
include 'connection.php';

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Fetch user info from DB using session email
$email = $_SESSION['email'];
$query = "SELECT firstName, lastName, university_id FROM signup WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $name = $user['firstName'] . ' ' . $user['lastName'];
    $vrsityId = $user['university_id'];
} else {
    echo "<script>alert('User not found.'); window.location.href='index.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pickupLocation = $_POST['pickupLocation'];
    $phone = $_POST['phone'];
    $ticketno = $_POST['ticketno'];

    $stmt = $conn->prepare("INSERT INTO staffreciept (name, email, vrsityId, pickupLocation, phone, ticketno) VALUES (?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $name, $email, $vrsityId, $pickupLocation, $phone, $ticketno);
    
    if ($stmt->execute()) {
        header("Location: https://urms-online.ulab.edu.bd/PaymentInfo.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Pass Page</title>
  <link rel="stylesheet" href="ticket.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

  <header>
    <div class="logo">
      <img src="ulab-logo.png" alt="Bus Logo">
    </div>
    <nav>
      <a href="myaccount.php"><i class="fas fa-user-circle"></i> My Account</a>
    </nav>
  </header>

  <main>
    <h1>Buy Your Bus Pass</h1>
    <div class="ticket-options">
      <div class="ticket">
        <h2>Single Trip</h2>
        <p>Price: 60 BDT</p>
        <button onclick="openPopup('1')">Buy Now</button>
      </div>
      <div class="ticket">
        <h2>40 Trips Pack</h2>
        <p>Price: 2000 BDT</p>
        <button onclick="openPopup('40')">Buy Now</button>
      </div>
      <div class="ticket">
        <h2>100 Trips Pack</h2>
        <p>Price: 4800 BDT</p>
        <button onclick="openPopup('100')">Buy Now</button>
      </div>
    </div>

    <div class="terms">
      <input type="checkbox" id="terms" name="terms">
      <label for="terms">I agree to the <a href="https://www.immediatelive.com/ticket-terms-and-conditions/">terms and conditions</a></label>
    </div>
  </main>

  <div id="popup" class="popup">
    <div class="popup-content">
      <h2>Enter Your Information</h2>
      <p id="selectedTicket"></p>
      <form id="userForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="pickup">Drop Off/Pick Up Point:</label>
        <div class="dropdown">
          <button id="dropBtn" class="dropbtn" onclick="toggleDropdown()" type="button">Select a location</button>
          <div id="dropMenu" class="dropdown-content">
            <a href="#" onclick="setLocation('Mohammadpur Bus Stand')">Mohammadpur Bus Stand</a>
            <a href="#" onclick="setLocation('Asad Gate')">Asad Gate</a>
            <a href="#" onclick="setLocation('Shankar Bus Stop')">Shankar Bus Stop</a>
            <a href="#" onclick="setLocation('Dhanmondi-27')">Dhanmondi-27</a>
            <a href="#" onclick="setLocation('Dhanmondi-15')">Dhanmondi-15</a>
            <a href="#" onclick="setLocation('Shymoli')">Shymoli</a>
            <a href="#" onclick="setLocation('Sobhanbag Mosque')">Sobhanbag Mosque</a>
            <a href="#" onclick="setLocation('Labaid')">Labaid</a>
            <a href="#" onclick="setLocation('Technical')">Technical</a>
            <a href="#" onclick="setLocation('Sony Square')">Sony Square</a>
            <a href="#" onclick="setLocation('Mirpur-10')">Mirpur-10</a>
            <a href="#" onclick="setLocation('Azimpur Bus Stand')">Azimpur Bus Stand</a>
            <a href="#" onclick="setLocation('Palashi')">Palashi</a>
          </div>
        </div>
        <input type="hidden" id="pickupLocation" name="pickupLocation" required>

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required>
        <input type="hidden" id="ticketno" name="ticketno">
        <button type="submit" onclick="validateAndProceed()">Proceed</button>
        <button type="button" onclick="closePopup()">Cancel</button>
      </form>
    </div>
  </div>
  <script>
    function openPopup(ticketType) {
      if (!document.getElementById('terms').checked) {
        alert('You must agree to the terms and conditions before proceeding.');
        return;
      }
      document.getElementById('popup').style.display = 'block';
      document.getElementById('selectedTicket').innerText = "Selected Ticket: " + ticketType;
      document.getElementById('ticketno').value = ticketType;
    }

    function closePopup() {
      document.getElementById('popup').style.display = 'none';
    }

    function validateAndProceed() {
      const form = document.getElementById('userForm');
      if (form.checkValidity()) {
        form.submit(); 
      } else {
        alert('Please fill out all fields.');
      }
    }

    function toggleDropdown() {
      document.getElementById("dropMenu").classList.toggle("show");
    }

    function setLocation(location) {
      document.getElementById("dropBtn").innerText = location;
      document.getElementById("pickupLocation").value = location;
      toggleDropdown();
    }

    window.onclick = function(event) {
      if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
          var openDropdown = dropdowns[i];
          if (openDropdown.classList.contains('show')) {
            openDropdown.classList.remove('show');
          }
        }
      }
    }
  </script>
</body>
</html>
