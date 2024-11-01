<?php
// Koneksi ke database
include 'config.php';

// Ambil data dari form
$nama_barang = $_POST['nama_barang'];
$jumlah_barang = $_POST['jumlah_barang'];
$harga_beli = $_POST['harga_beli'];
$harga_jual = $_POST['harga_jual'];

// Validasi data
if (empty($nama_barang) || empty($jumlah_barang) || empty($harga_beli) || empty($harga_jual)) {
    echo "Harap isi semua data.";
    exit;
}

// Persiapkan query INSERT dengan prepared statement
$stmt = $conn->prepare("INSERT INTO transaksi (nama_barang, jumlah_barang, harga_beli, harga_jual) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sidi", $nama_barang, $jumlah_barang, $harga_beli, $harga_jual);

// Eksekusi query
if ($stmt->execute()) {
    echo '<script>
        alert("Transaksi berhasil disimpan!");
        window.location.href = "transaksi.php"; // Ganti "index.php" dengan nama halaman utama Anda
    </script>';
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
