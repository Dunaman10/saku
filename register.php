<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = hash('sha256', $_POST['password']);
    $email = $_POST['email'];
    $name = $_POST['name'];

    $conn->query("INSERT INTO users (username, password, email, name) 
                  VALUES ('$username', '$password', '$email', '$name')");
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <style>
        .bg-effect {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(4px);
            padding: 35px;
            border-radius: 15px;
            color: white;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 0 20px #0006;
        }

        .form-label {
        margin-bottom: 2px !important; /* default Bootstrap sekitar 8px */

        }

         .form-control {
        margin-top: 0 !important;
    }
    </style>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container mt-5">
    <h2 class="text-center mb-4 fw-bold">Register</h2>

    <form method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label mb-1">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan Username Anda" required class="mt-0">
        </div>

        <div class="mb-3">
            <label class="form-label mb-1">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password Anda" required class="mt-0">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan Email Anda" required>
        </div>

        <div class="mb-4">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Lengkap Anda" required>
        </div>

        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary w-100 fw-semibold">Register</button>
        </div>

    </form>

    <p class="mt-3 text-center">
        <a href="login.php" class="text-decoration-none">← Kembali ke Login</a>
    </p>
</div>

</body>

</html>

<!-- Bootstrap Bundle with Popper (local) -->
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
