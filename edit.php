<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM transactions WHERE id = $id AND user_id = " . $_SESSION['user']['id']);
if ($result->num_rows === 0) {
    die("Transaction not found.");
}
$transaction = $result->fetch_assoc();

$saldo_akhir = 0; // Variabel untuk saldo akhir

// Calculate the final balance before handling the form
$query_balance = "SELECT SUM(CASE WHEN type = 'Pemasukan' THEN amount ELSE -amount END) AS balance FROM transactions WHERE user_id = ?";
$stmt_balance = $conn->prepare($query_balance);
$stmt_balance->bind_param("i", $_SESSION['user']['id']);
$stmt_balance->execute();
$result_balance = $stmt_balance->get_result();
$row = $result_balance->fetch_assoc();
$saldo_akhir = $row['balance'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $amount = floatval($_POST['amount']);
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    // Update transaction in the database
    $stmt = $conn->prepare("UPDATE transactions SET type = ?, amount = ?, description = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sdssi", $type, $amount, $description, $id, $_SESSION['user']['id']);
    $stmt->execute();

    // Recalculate the final balance after the update
    $query_balance = "SELECT SUM(CASE WHEN type = 'Pemasukan' THEN amount ELSE -amount END) AS balance FROM transactions WHERE user_id = ?";
    $stmt_balance = $conn->prepare($query_balance);
    $stmt_balance->bind_param("i", $_SESSION['user']['id']);
    $stmt_balance->execute();
    $result_balance = $stmt_balance->get_result();
    $row = $result_balance->fetch_assoc();
    $saldo_akhir = $row['balance'];

    header("Location: edit.php?id=$id&updated=true&balance=" . urlencode($saldo_akhir));
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Ubah Transaksi</title>
</head>

<body>
    <div class="container">
        <h1>Ubah Transaksi</h1>

        <!-- Success Message -->
        <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
            <script>
                alert("Transaction updated successfully!");
            </script>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="type">Tipe:</label>
            <select name="type" id="type" required>
                <option>--Silahkan Pilih--</option>
                <option value="Pemasukan" <?= $transaction['type'] === 'Pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                <option value="Pengeluaran" <?= $transaction['type'] === 'Pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
            </select>
            <label for="amount">Jumlah:</label>
            <input type="number" name="amount" id="amount" value="<?= $transaction['amount'] ?>" placeholder="Amount" required>
            <label for="description">Deskripsi:</label>
            <input type="text" name="description" id="description" value="<?= $transaction['description'] ?>" placeholder="Description" required>
            <button type="submit">Perbarui</button>
        </form>
        <p><a href="index.php">Kembali</a></p>
    </div>

    <!-- Modal for zooming images -->
    <div id="imageModal" class="modal" onclick="closeModal()">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        function showModal(src) {
            const modal = document.getElementById("imageModal");
            const modalImage = document.getElementById("modalImage");
            modal.style.display = "flex";
            modalImage.src = src;
        }

        function closeModal() {
            const modal = document.getElementById("imageModal");
            modal.style.display = "none";
        }
    </script>

    <!-- Bootstrap Bundle with Popper (local) -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
