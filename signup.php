<?php
session_start(); 

include('connection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function sendMail($email, $v_code) {
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
    require 'PHPMailer/Exception.php';

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
        $mail->Subject = 'Email verification from ULAB digital Shuttle Service';
        $mail->Body    = "Thanks for registering!<br>
                          Click the link below to verify your email address:<br>
                          <a href='http://localhost/signup/verify.php?email=$email&v_code=$v_code'>Verify</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false; 
    }     
}

if (isset($_POST['signUp'])) {
    
    
    $firstName = mysqli_real_escape_string($conn, $_POST['fName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    if (!preg_match("/@ulab\.edu\.bd$/", $email)) {
    echo '<script>
            alert("Please sign up with a ULAB email address (ending with @ulab.edu.bd).");
            window.location.href = "index.php";
          </script>';
    exit();
}

    $university_id = mysqli_real_escape_string($conn, $_POST['university_id']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);

    
    
    $sql = "SELECT * FROM signup WHERE university_id='$university_id' OR email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {  
        
        if ($password == $cpassword) {
           
            
            $hash = password_hash($password, PASSWORD_BCRYPT);

            
            $v_code = bin2hex(random_bytes(16));

            
            $sql = "INSERT INTO signup (firstName, lastName, email, university_id, password, verification_code, is_verified) 
                    VALUES ('$firstName','$lastName', '$email', '$university_id', '$hash', '$v_code', '0')";

            if (mysqli_query($conn, $sql)) {
                if (sendMail($email, $v_code)) {
                    echo '<script>
                            alert("Signup successful. Please check your email inbox or Spam to verify your account.");
                            window.location.href = "index.php";
                          </script>';
                } else {
                    
                    $delete_sql = "DELETE FROM signup WHERE email = '$email'";
                    mysqli_query($conn, $delete_sql);
                    echo '<script>
                            alert("Email sending failed. Please try again later.");
                            window.location.href = "index.php";
                          </script>';
                }
            } else {
                echo '<script>
                        alert("Error inserting user data into the database.");
                        window.location.href = "index.php";
                      </script>';
            }
        } else {
            echo '<script>
                    alert("Passwords do not match");
                    window.location.href = "index.php";
                  </script>';
        }
    } else {
        echo '<script>
                alert("University ID or Email already exists!");
                window.location.href = "index.php";
              </script>';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student's Signup</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="solar.css">
    <link rel="stylesheet" href="signup.css">
</head>
<body>
<div class="solar-syst">
        <div class="sun"></div>
        <div class="mercury"></div>
        <div class="venus"></div>
        <div class="earth"></div>
        <div class="mars"></div>
        <div class="jupiter"></div>
        <div class="saturn"></div>
        <div class="uranus"></div>
        <div class="neptune"></div>
        <div class="pluto"></div>
        <div class="asteroids-belt"></div>
</div>
<div>
        <h1 class="sign-up-heading"><- Create an Account</h1>
    </div>

    <div class="container" id="signup">
  
      <form method="post" action="">
        <div class="input-group">
        <label for="fname">First Name</label>
           <input type="text" name="fName" id="fName" placeholder="First Name" required>
        </div>
        <div class="input-group">
        <label for="lName">Last Name</label>
            <input type="text" name="lName" id="lName" placeholder="Last Name" required>
            
        </div>
        <div class="input-group">
        <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Email" required>
            
        </div>
        <div class="input-group">
                <label for="university_id">University ID</label>
                <input type="text" id="university_id" name="university_id" placeholder="Enter University ID" required>
            </div>
        <div class="input-group">
        <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <div class="password-strength">
                    <div class="strength-bar" id="strength-bar"></div>
                </div>
                <div class="message" id="message">
                    <ul>
                        <li>Use at least 8 characters.</li>
                        <li>use both uppercase and lowercase.</li>
                        <li>Add numbers and special characters.</li>
                    </ul>
                </div>
            </div>
       
            <div class="input-group">
        <label for="cpassword">Confirm Password</label>
            <input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password" required>
            </div>

       <input type="submit" class="btn" value="Sign Up" name="signUp">
      </form>
      </div>
    <script >
        document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strength-bar');
    const messageBox = document.getElementById('message');

    passwordInput.addEventListener('focus', function() {
        messageBox.classList.add('visible');
    });

    passwordInput.addEventListener('blur', function() {
        messageBox.classList.remove('visible');
    });

    passwordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        let strength = 0;
        let color = 'red';

        
        if (password.length >= 8) {
            strength += 25;
        }
        
        if (/[A-Z]/.test(password)) {
            strength += 25;
        }
        
        if (/[a-z]/.test(password)) {
            strength += 25;
        }
        
        if (/\d/.test(password) && /[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            strength += 25;
        }

    
        strengthBar.style.width = strength + '%';
        if (strength <= 25) {
            color = 'red';
        } else if (strength <= 50) {
            color = 'orange';
        } else if (strength <= 75) {
            color = 'yellow';
        } else {
            color = 'green';
        }
        strengthBar.style.backgroundColor = color;
    });
});

</script>
</body>
</html>






