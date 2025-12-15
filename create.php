<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("Location: login.php");
	exit;
}
include 'config.php';

$user_id = $_SESSION['user']['id'];
$message = ""; // Inisialisasi variabel pesan

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$type = $_POST['type'];
	$amount = floatval($_POST['amount']);
	$description = $_POST['description'];


	// Insert transaction into the database
	$query = "INSERT INTO transactions (user_id, type, amount, description, created_at) VALUES ( ?, ?, ?, ?, NOW())";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("isds", $user_id, $type, $amount, $description);

	if ($stmt->execute()) {
		$message = "Transaksi Berhasil di Tambahkan!";

		$query_balance = "SELECT SUM(amount) AS total_balance FROM transactions WHERE user_id = ?";
		$stmt_balance = $conn->prepare($query_balance);
		$stmt_balance->bind_param("i", $user_id);
		$stmt_balance->execute();
		$result = $stmt_balance->get_result();
		$row = $result->fetch_assoc();
	} else {
		$message = "Error: " . $stmt->error;
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tambah Transaksi</title>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="style.css">
	<script>
		function showMessage(message) {
			if (message) {
				alert(message);
			}
		}
	</script>
</head>

<body onload="showMessage('<?= $message ?>')">
	<div class="container">
		<h1>Tambah Transaksi</h1>
		<form action="create.php" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="type">Tipe:</label>
				<select name="type" id="type" required>
					<option>--Silahkan Pilih--</option>
					<option value="Pemasukan">Pemasukan</option>
					<option value="Pengeluaran">Pengeluaran</option>
				</select>
			</div>

			<div class="form-group">
				<label for="amount">Jumlah:</label>
				<input type="number" name="amount" id="amount" step="0.01" placeholder="Amount" required>
			</div>

			<div class="form-group">
				<label for="description">Deskripsi:</label>
				<input type="text" name="description" id="description" rows="4" placeholder="Description" required>
			</div>

			<div class="form-actions text-center">
				<button type="submit" class="">Tambah</button>
				<br>
			</div>
			<a href="index.php">Kembali</a>
		</form>

	</div>
	</body>

	<!-- Bootstrap Bundle with Popper (local) -->
	<script src="bootstrap/js/bootstrap.bundle.min.js"></script>

	</html>
