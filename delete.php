<?php
session_start();
if (!isset($_SESSION['user'])) {
	header("Location: login.php");
	exit;
}

include 'config.php';

$id = intval($_GET['id']);
$user_id = $_SESSION['user']['id'];

$query = $conn->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
$query->bind_param("ii", $id, $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
	$transaction = $result->fetch_assoc();

	$deleteQuery = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
	$deleteQuery->bind_param("ii", $id, $user_id);
	if ($deleteQuery->execute()) {
		$_SESSION['message'] = "Delete berhasil dihapus.";
	} else {
		$_SESSION['message'] = "Gagal menghapus data.";
	}
} else {
	$_SESSION['message'] = "Data tidak ditemukan.";
}

header("Location: index.php");
exit;
