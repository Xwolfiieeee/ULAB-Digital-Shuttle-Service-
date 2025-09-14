<?php
include 'connection.php';

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);

    
    $sql = "SELECT * FROM signup WHERE token = ? AND expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        if (isset($_POST['resetPassword'])) {
            $password = $_POST['password'];
            $cpassword = $_POST['cpassword'];

            if ($password === $cpassword) {
                $hash = password_hash($password, PASSWORD_BCRYPT);

                
                $sql = "UPDATE signup SET password = ?, token = NULL, expiry = NULL WHERE token = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $hash, $token);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo '<script>
                        alert("Password has been reset successfully. You can now log in.");
                        window.location.href = "index.php";
                    </script>';
                } else {
                    echo '<script>
                        alert("Error resetting the password. Please try again.");
                        window.location.href = "reset-password.php?token=' . $token . '";
                    </script>';
                }
            } else {
                echo '<script>
                    alert("Passwords do not match.");
                    window.location.href = "reset-password.php?token=' . $token . '";
                </script>';
            }
            $stmt->close();
        }
    } else {
        echo '<script>
            alert("Invalid or expired token.");
            window.location.href = "forgot-password.php";
        </script>';
    }
} else {
    echo '<script>
        alert("No token provided.");
        window.location.href = "forgot-password.php";
    </script>';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <link rel="stylesheet" href="resetpass.css">
</head>
<body>
    <header>
        <img src="ulab-logo.png" alt="ULAB Logo" class="logo">
    </header>
    <main>
        <div class="container">
            <h1>Reset Password</h1>
            <form method="post" action="">
                <label for="password">New Password:</label>
                <input type="password" name="password" id="password" required>
                <label for="cpassword">Confirm Password:</label>
                <input type="password" name="cpassword" id="cpassword" required>
                <input type="submit" name="resetPassword" value="Reset Password">
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 ULAB Digital Shuttle Service. All rights reserved.</p>
    </footer>
</body>
</html>
