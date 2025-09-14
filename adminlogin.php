<?php
include 'connection.php';
session_start();


if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM adminmain WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        
        if ($password === $row['password']) {
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_logged_in'] = true; 
            header("Location: admin.php");
            exit();
        } else {
            echo '<script>
                alert("Incorrect email or password!");
                window.location.href="adminlogin.php";
            </script>';
        }
    } else {
        echo '<script>
            alert("Incorrect email or password!");
            window.location.href="adminlogin.php";
        </script>';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="adminlogin.css">
</head>
<body>
    <header>
        <div class="header-container">
            <img src="ulab-logo.png" alt="ULAB Logo" class="logo">
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="staffLogin.php">Staff</a></li>
                    <li><a href="shuttleSchedule.html">Schedule</a></li>
                    <li><a href="routemaps.html">Route Map</a></li>
                </ul>
            </nav>
        </div>
    </header>
   
    <main>
        <div class="login-container">
            <h2>Admin Login</h2>
            <form method="post" action="">
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>
                <input type="submit" class="btn" value="Login" name="login">
            </form>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <img src="campus.jpg" alt="Bus Icon" class="bus-icon">
        </div>
        <div class="copyright">
            <p>&copy; 2006-2024 UNIVERSITY OF LIBERAL ARTS BANGLADESH. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
