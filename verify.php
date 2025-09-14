<?php
require("connection.php");

if (isset($_GET['email']) && isset($_GET['v_code'])) {
   
    echo "Email: " . htmlspecialchars($_GET['email']) . "<br>";
    echo "Verification Code: " . htmlspecialchars($_GET['v_code']) . "<br>";

   
    $email = mysqli_real_escape_string($conn, $_GET['email']);
    $v_code = mysqli_real_escape_string($conn, $_GET['v_code']);

    
    $query = "SELECT * FROM signup WHERE email='$email' AND verification_code='$v_code'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            $result_fetch = mysqli_fetch_assoc($result);
            if ($result_fetch['is_verified'] == 0) {
                
                $update = "UPDATE signup SET is_verified='1' WHERE email='$email'";
                if (mysqli_query($conn, $update)) {
                    echo '<script>
                    alert("Email verification successful");
                    window.location.href = "index.php";
                  </script>';
                } else {
                    echo '<script>
                    alert("Cannot run update query: ' . mysqli_error($conn) . '");
                    window.location.href = "index.php";
                  </script>';
                }
            } else {
                echo '<script>
                alert("Email already verified");
                window.location.href = "index.php";
              </script>';
            }
        } else {
            echo '<script>
            alert("No matching record found");
            window.location.href = "index.php";
          </script>';
        }
    } else {
        
        echo '<script>
        alert("Cannot run select query: ' . mysqli_error($conn) . '");
        window.location.href = "index.php";
      </script>';
    }
} else {
    echo '<script>
    alert("Invalid request");
    window.location.href = "index.php";
  </script>';
}
?>
