<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$password = $_POST['password'];

	$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result && $result->num_rows > 0) {
		$user = $result->fetch_assoc();

		if (hash('sha256', $password) === $user['password']) {
			$_SESSION['user'] = $user;
			$_SESSION['message'] = 'Login berhasil!';
			header("Location: index.php");
			exit;
		} else $error = "Password salah.";
	} else $error = "User tidak ditemukan.";
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Login - Saku</title>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">

	<style>
		body {
			background: url('babi.png');
			background-size: cover;
			background-position: center;
			background-attachment: fixed;
			height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
		}

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

		input::placeholder {
			color: #ddd;
		}

		.form-control {
			background: rgba(255, 255, 255, 0.8);
		}
	</style>
</head>

<body>
	<div class="bg-effect">
		<h3 class="text-center mb-3 fw-bold">Login Saku</h3>
		<p class="text-center text-light mb-4">Selamat datang kembali 🤝</p>

		<?php if (!empty($error)): ?>
			<div class="alert alert-danger py-2 text-center">
				<?= $error; ?>
			</div>
		<?php endif; ?>

		<form method="POST" autocomplete="off">

			<div class="mb-3">
				<label class="form-label text-light">Username</label>
				<input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
			</div>

			<div class="mb-3">
				<label class="form-label text-light">Password</label>
				<input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
			</div>

			<button type="submit" class="btn btn-success w-100 mb-2 fw-bold">Masuk</button>

			<div class="text-center">
				<small>Belum punya akun? <a href="register.php" class="text-warning ">Daftar</a></small>
			</div>
		</form>
	</div>

	<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>