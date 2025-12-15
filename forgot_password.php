<?php
include 'config.php';

$message = ''; // Variable to store feedback message
$success = false; // Variable to determine if the message is a success

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        // Generate new password
        $new_password = substr(md5(rand()), 0, 8);
        $hashed_password = hash('sha256', $new_password);

        // Update password in the database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();

        $success = true;
        $message = "Your new password is: $new_password"; // Set success message
    } else {
        $message = "Email not found."; // Set error message
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Forgot Password</title>
    <style>
        .alert {
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Reset Password</button>
        </form>

        <!-- Display success or error message -->
        <?php if ($message): ?>
            <div class="alert <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <p><a href="login.php">Back to Login</a></p>
    </div>

    <script>
        <?php if ($message): ?>
            alert("<?php echo addslashes($message); ?>");
        <?php endif; ?>
    </script>

    <!-- Bootstrap Bundle with Popper (local) -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
