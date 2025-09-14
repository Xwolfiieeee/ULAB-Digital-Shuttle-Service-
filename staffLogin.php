<?php 
include 'connection.php';

if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $sql = "SELECT * FROM staff WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        session_start();
        $row = $result->fetch_assoc();

        
        if ($password === $row['password']) {
            $_SESSION['email'] = $row['email'];
            header("Location: staffReceipts.php");
            exit();
        } else {
          
            echo '<script>
                alert("Incorrect email or password!");
                window.location.href="staffLogin.php";
            </script>';
        }
    } else {
        
        echo '<script>
            alert("Incorrect email or password!");
            window.location.href="staffLogin.php";
        </script>';
    }
    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - University</title>
    <link rel="stylesheet" href="stafflogin.css"> 
</head>
<body>
   
    <img src="ulab-logo.PNG" alt="University Logo" class="university-logo">

    <div class="login-container">
        <div class="login-form">
            <h1>Staff Login</h1>

            <form action="" method="post">
             
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

               
                <input type="submit" class="btn" value="Sign In" name="signIn">

                
                <div class="forgot-password">
                    <a href="staffforgotpass.php">Forgot your password?</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
