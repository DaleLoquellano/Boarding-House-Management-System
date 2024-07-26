<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$database = "im2database";

// Create connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $conn->real_escape_string($_POST['username']);
    $inputPassword = $conn->real_escape_string($_POST['password']);

    // Debugging: Display the submitted values


    // Query to fetch the user and hashed password
    $sql = "SELECT username, password, StaffFname, StaffMi, StaffLname, StaffID, StaffRole FROM staff WHERE username = '$inputUsername'";
    $result = $conn->query($sql);

    if ($result === false) {
    
        echo "<pre>Username or password is incorrect</pre>";
    } elseif ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Debugging: Display fetched user data
        echo "<pre>Debug: Fetched user data: " . print_r($user, true) . "</pre>";

        // Verify the provided password
        if (password_verify($inputPassword, $user['password'])) {
            $_SESSION['staff_username'] = $user['username']; // Store the username as the staff ID
            $_SESSION['staff_name'] = $user['StaffFname'] . ' ' . $user['StaffMi'] . ' ' . $user['StaffLname'];
            $_SESSION['staff_password'] = $user['password'];
            $_SESSION['staff_id'] = $user['StaffID'];
            $_SESSION['staff_role'] = $user['StaffRole'];

            // Debugging: Check session variables


            // Redirect to the dashboard
            header("Location: ../thelast/settings.php");
            exit();
        } else {
            $errorMessage = "Invalid credentials, please try again or contact admin";

        }
    } else {
        $errorMessage = "Invalid credentials, please try again or contact admin";

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Munoz Boarding House Login</title>
    <link rel="stylesheet" href="styles/login-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Mallanna&display=swap" rel="stylesheet">
    <style>
        /* Add some basic styles for error messages */
        .error-message {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo-container">
                <img src="icons/logo.png" alt="Munoz Boarding House Logo" class="logo">
            </div>
            <h2>Welcome back!</h2>
            <?php if (!empty($errorMessage)): ?>
                <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Log In</button>
                <a href="#" class="forgot-password">Forgot password?</a>
            </form>
        </div>
    </div>
</body>
</html>
