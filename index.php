<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("Location: login.php");
	exit;
}
include 'config.php';

$user_id = $_SESSION['user']['id'];

// cari
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$searchQuery = $search ? "AND description LIKE '%$search%'" : '';

$transactions = $conn->query("SELECT * FROM transactions WHERE user_id = $user_id $searchQuery ORDER BY created_at DESC");

$pemasukan = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE user_id=$user_id AND type='Pemasukan'")->fetch_assoc()['total'] ?? 0;
$pengeluaran = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE user_id=$user_id AND type='Pengeluaran'")->fetch_assoc()['total'] ?? 0;

// pesan
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<title>Saku</title>

	<style>
		/* TAB PILL DENGAN OUTLINE */
		.nav-pills .nav-link {
			border-radius: 30px;
			padding: 10px 22px;
			font-weight: 600;
			transition: .25s ease;
		}

		/* TAB TIDAK AKTIF → OUTLINE */
		.nav-pills .nav-link:not(.active) {
			background: #fff;
			border: 2px solid #0D6EFD;
			color: #0D6EFD;
		}

		/* TAB AKTIF → SOLID */
		.nav-pills .nav-link.active {
			background: #0D6EFD;
			color: #fff !important;
			border: 2px solid #0D6EFD;
		}

		/* HOVER BIAR LEBIH HIDUP */
		.nav-pills .nav-link:hover {
			transform: scale(1.05);
		}

		.chart-container {
			width: 90%;
			margin: auto;
		}
	</style>

</head>

<body class="bg-light">

	<div class="container mt-5 p-4 bg-white shadow rounded">

		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="fw-bold mb-0">Halo, <?= $_SESSION['user']['name']; ?> 👋</h3>
			<a href="javascript:void(0);" onclick="confirmLogout()" class=" fw-bold btn btn-danger p-2">Logout</a>
		</div>

		<!-- ====== NAV TABS (Bootsrap Modern) ====== -->
		<ul class="nav nav-pills mb-4  gap-2" id="pills-tab">
			<li class="nav-item "><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#transactions">Transaksi</button></li>
			<li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#dompetku">Dompetku</button></li>
			<li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#profile">Profil</button></li>
		</ul>


		<div class="tab-content">

			<!-- ================= TAB 1 :: TRANSAKSI ================= -->
			<div class="tab-pane fade show active" id="transactions">

				<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
					<form method="GET" class="d-flex align-items-center gap-2">
						<input type="text" name="search" class="form-control" placeholder="Cari deskripsi..." value="<?= $search; ?>" style="width:260px">
						<button class="btn btn-outline-primary">Cari</button>
						<a href="?" class="btn btn-light border">Refersh</a>
					</form>

					<a href="create.php" class="btn btn-success px-4">+ Buat Transaksi</a>
				</div>

				<?php if ($transactions->num_rows > 0): ?>
					<div class="table-responsive">
						<table class="table table-striped table-hover text-center align-middle border rounded ">
							<thead class="table-primary">
								<tr>
									<th>Tanggal</th>
									<th>Tipe</th>
									<th>Jumlah</th>
									<th>Deskripsi</th>
									<th width="150">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php while ($row = $transactions->fetch_assoc()): ?>
									<tr>
										<td> <?= date('d-m-Y', strtotime($row['created_at'])); ?></td>
										<td><span class="badge bg-<?= $row['type'] == 'Pemasukan' ? 'success' : 'danger' ?>"><?= $row['type']; ?></span></td>
										<td>Rp <?= number_format($row['amount'], 2); ?></td>
										<td><?= $row['description']; ?></td>
										<td>
											<a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-primary">Ubah</a>
											<a href="delete.php?id=<?= $row['id']; ?>" onclick="return confirm('Hapus Data?')" class="btn btn-sm btn-danger">Hapus</a>
										</td>
									</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>

				<?php else: ?>
					<p class="text-center text-muted">Belum ada transaksi 🔎 <a href="create.php">Tambah sekarang</a></p>
				<?php endif; ?>

				<div class="chart-container mt-4">
					<canvas id="transactionChart"></canvas>
				</div>
			</div>

			<!-- ================= TAB DOMPETKU ================= -->
			<div class="tab-pane fade" id="dompetku">
				<div class="row g-4">
					<!-- Saldo Utama -->
					<div class="col-12">
						<div class="card bg-primary text-white border-0 shadow rounded-4 position-relative overflow-hidden">
							<div class="card-body p-5">
								<h5 class="text-white-50 text-uppercase mb-2" style="letter-spacing: 1px;">Total Saldo Kamu</h5>
								<h1 class="display-4 fw-bold mb-0">Rp <?= number_format($pemasukan - $pengeluaran, 2, ',', '.'); ?></h1>
								<!-- Decoration -->
								<div class="position-absolute" style="top: -20px; right: -20px; opacity: 0.15;">
									<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" fill="currentColor" class="bi bi-wallet2" viewBox="0 0 16 16">
										<path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499L12.136.326zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484L5.562 3zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-13z" />
									</svg>
								</div>
							</div>
						</div>
					</div>

					<!-- Detail Income -->
					<div class="col-md-6">
						<div class="card border-0 shadow-sm rounded-4">
							<div class="card-body p-4 d-flex align-items-center">
								<div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 text-success">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-arrow-down-circle-fill" viewBox="0 0 16 16">
										<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 7.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z" />
									</svg>
								</div>
								<div>
									<span class="text-muted d-block mb-1">Total Pemasukan</span>
									<h4 class="fw-bold text-success mb-0">Rp <?= number_format($pemasukan, 2, ',', '.'); ?></h4>
								</div>
							</div>
						</div>
					</div>

					<!-- Detail Expense -->
					<div class="col-md-6">
						<div class="card border-0 shadow-sm rounded-4">
							<div class="card-body p-4 d-flex align-items-center">
								<div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3 text-danger">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-arrow-up-circle-fill" viewBox="0 0 16 16">
										<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5a.5.5 0 0 0-1 0v5.793l-2.146-2.147a.5.5 0 0 0-.708.708l3 3z" />
									</svg>
								</div>
								<div>
									<span class="text-muted d-block mb-1">Total Pengeluaran</span>
									<h4 class="fw-bold text-danger mb-0">Rp <?= number_format($pengeluaran, 2, ',', '.'); ?></h4>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- ================= TAB 2 :: PROFIL ================= -->
			<div class="tab-pane fade" id="profile">
				<h4 class="fw-bold mb-3">Data Profil</h4>
				<div class="list-group">
					<span class="list-group-item"><b>Username:</b> <?= $_SESSION['user']['username']; ?></span>
					<span class="list-group-item"><b>Nama:</b> <?= $_SESSION['user']['name']; ?></span>
					<span class="list-group-item"><b>Email:</b> <?= $_SESSION['user']['email']; ?></span>
					<span class="list-group-item"><b>Joined:</b> <?= date('d M Y', strtotime($_SESSION['user']['created_at'])); ?></span>
				</div>
			</div>

		</div>
	</div>


	<script src="bootstrap/js/bootstrap.bundle.min.js"></script>

	<?php if ($message): ?>
		<script>
			alert("<?= $message; ?>")
		</script>
	<?php endif; ?>

	<script>
		function confirmLogout() {
			if (confirm("Yakin keluar?")) location.href = "logout.php";
		}
	</script>

</body>

</html>