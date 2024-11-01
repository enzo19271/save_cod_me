<?php
$mysqli = new mysqli("localhost", "root", "", "test_bt");


// Ambil ID data yang akan dihapus dari parameter GET
$id = $_GET['id'];

// Query untuk menghapus data berdasarkan ID
$sql = "DELETE FROM transaksi WHERE id='$id'";

// Eksekusi query
if (mysqli_query($mysqli, $sql)) {
    echo "Data berhasil dihapus.";
    header("Location: data_transaksi.php"); // Alihkan ke halaman data.php setelah hapus
} else {
    echo "Error: " . mysqli_error($mysqli);
}

mysqli_close($mysqli);
?>