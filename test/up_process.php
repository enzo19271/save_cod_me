<?php
include 'config.php';

// Ambil data dari form
$id = $_POST['id'];
$nama_barang = $_POST['nama_barang'];
$harga_beli = $_POST['harga_beli'];
$harga_jual = $_POST['harga_jual'];
$gambar = $_FILES['gambar']['name'];
$target_dir = "images/";

// Jika gambar diupload, simpan gambar ke folder "images/"
if ($gambar) {
    $target_file = $target_dir . basename($gambar);
    move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file);
}

// Jika id kosong, maka ini adalah proses upload produk baru
if (empty($id)) {
    // Query untuk memasukkan data baru
    $stmt = $conn->prepare("INSERT INTO products (nama_barang, harga_beli, harga_jual, gambar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdds", $nama_barang, $harga_beli, $harga_jual, $gambar);
} else {
    // Jika id tidak kosong, ini adalah proses update produk
    if ($gambar) {
        // Jika ada gambar baru yang diupload, update gambar juga
        $stmt = $conn->prepare("UPDATE products SET nama_barang = ?, harga_beli = ?, harga_jual = ?, gambar = ? WHERE id = ?");
        $stmt->bind_param("sddsi", $nama_barang, $harga_beli, $harga_jual, $gambar, $id);
    } else {
        // Jika tidak ada gambar baru, update data tanpa gambar
        $stmt = $conn->prepare("UPDATE products SET nama_barang = ?, harga_beli = ?, harga_jual = ? WHERE id = ?");
        $stmt->bind_param("sddi", $nama_barang, $harga_beli, $harga_jual, $id);
    }
}

// Eksekusi query
if ($stmt->execute()) {
    echo "Data berhasil disimpan!";
    header('Location: data_transaksi.php'); // Kembali ke halaman utama setelah berhasil
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
