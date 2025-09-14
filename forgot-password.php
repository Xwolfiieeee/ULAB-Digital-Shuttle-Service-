<?php
include 'connection.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (isset($_POST['resetRequest'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    
    $sql = "SELECT * FROM signup WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 day')); 

      
        $sql = "UPDATE signup SET token = ?, expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        
        $resetLink = "http://localhost/signup/reset-password.php?token=$token";

        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'phantomberen@gmail.com';   
            $mail->Password   = 'rtci fpzu mqmh vcpv';     
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('phantomberen@gmail.com', 'ULAB Digital Shuttle Service');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click the link below to reset your password:<br>
            <a href='$resetLink'>Reset Password</a>";

            $mail->send();
            echo '<script>
                alert("Password reset link has been sent to your email.");
                window.location.href = "index.php";
            </script>';
        } catch (Exception $e) {
            error_log('Mail Error: ' . $mail->ErrorInfo); 
            echo '<script>
                alert("Failed to send password reset email. Please try again later.");
                window.location.href = "forgot-password.php";
            </script>';
        }
    } else {
        echo '<script>
            alert("Email not found in our records.");
            window.location.href = "forgot-password.php";
        </script>';
    }
    $stmt->close();
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgotpass.css">
</head>
<body>
    <header>
        <img src="ulab-logo.png" alt="ULAB Logo" class="logo">
    </header>
    <main>
        <div class="container">
            <h1>Forgot Password</h1>
            <form method="post" action="">
                <label for="email">Enter your email address:</label>
                <input type="email" name="email" id="email" required>
                <input type="submit" name="resetRequest" value="Request Password Reset">
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 ULAB Digital Shuttle Service. All rights reserved.</p>
    </footer>
</body>
</html>
