<?php 
include 'connection.php';
session_start();

if (isset($_POST['signIn'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM signup WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password']) && $row['is_verified'] == 1) {
            $_SESSION['email'] = $row['email'];
            header("Location: ticket.php");
            exit();
        } else {
            if ($row['is_verified'] != 1) {
                echo '<script>
                    alert("Your account is not verified. Please check your email to verify your account.");
                    window.location.href="index.php";
                </script>';
            } else {
                echo '<script>
                    alert("Incorrect email or password!");
                    window.location.href="index.php";
                </script>';
            }
        }
    } else {
        echo '<script>
            alert("Incorrect email or password!");
            window.location.href="index.php";
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
    <title>Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <div class="header-container">
            <img src="ulab-logo.png" alt="ULAB Logo" class="logo">
            <nav>
                <ul>
                <li><a href="adminlogin.php" >ADMIN</a></li>
                    <li><a href="staffLogin.php">Staff</a></li>
                    <li><a href="shuttleSchedule.html">Schedule</a></li>
                    <li><a href="routemaps.html">Route map</a></li>
                    <li><a href="https://admissions.ulab.edu.bd/?_gl=1*18d60qx*_ga*MTM0Mjc2MjE4NC4xNzIyMjMyMjk4*_ga_G3NJ3B5238*MTcyMzYzMzY1MS44LjAuMTcyMzYzMzY1MS42MC4wLjA.">Admissions</a></li>
                </ul>
            </nav>
            <div class="header-buttons">
                <a href="https://oam.ulab.edu.bd/?_gl=1*vsn7tt*_ga*MTM0Mjc2MjE4NC4xNzIyMjMyMjk4*_ga_G3NJ3B5238*MTcyMzU2MTI3Ni42LjAuMTcyMzU2MTI3Ni42MC4wLjA." class="apply-now">Apply Now</a>
                <a href="index.php" class="sign-in">Sign In</a>
            </div>
        </div>
    </header>
   
    <main>
    <div class="booking-container" id="signIn">
    <div>
                <img src="logo.png" alt="bus_logo" class="logo-icon">
            </div>
            <h2>Student Login.</h2>

        <form method="post" action="">
          <div class="input-group">
          <label for="email">Email:</label>
              <input type="email" name="email" id="email" placeholder="Enter your Email" required>
          </div>
          <div class="input-group">
          <label for="password">Password:</label>
              <input type="password" name="password" id="password" placeholder="Enter your password" required>
          </div>
          <div class="forgot-password">
                <a href="forgot-password.php">Forgot Password?</a>
            </div>
         <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>
        <div class="links">
          <p>Don't have account yet?</p>
          <a href="signup.php" id="signUpButton" class="btn">Sign Up</a>
        </div>
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

    <div class="moon"></div>
    <div class="sun"></div>

    <div class="cloud-container">
        <div class="clouds cloud1"></div>
        <div class="clouds cloud2"></div>
        <div class="clouds cloud3"></div>
    </div>
    <script>
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            const moon = document.querySelector('.moon');
            const sun = document.querySelector('.sun');
            const scrollPosition = window.scrollY;
            const maxScroll = document.body.scrollHeight - window.innerHeight;
            const scrollPercentage = scrollPosition / maxScroll;

            header.classList.toggle('shrink', scrollPosition > 50);

            const gradientStartColor = [0, 115, 177];
            const gradientEndColor = [40, 209, 152]; 
            const newColor = gradientStartColor.map((start, index) => {
                const end = gradientEndColor[index];
                return Math.round(start + (end - start) * scrollPercentage);
            });

            document.body.style.background = `linear-gradient(to bottom, rgb(${newColor.join(',')}) 0%, white 100%)`;

            const moonX = scrollPercentage * 50;
            const moonY = scrollPercentage * -110;
            moon.style.transform = `translate(${moonX}px,${moonY}px)`;

            const sunX = scrollPercentage * 90; 
            const sunY = scrollPercentage * -130; 
            sun.style.transform = `translate(${sunX}px, ${sunY}px)`;
        });
    </script>
</body>
</html>