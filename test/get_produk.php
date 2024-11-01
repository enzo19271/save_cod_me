<?php
include 'config.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Ambil data produk berdasarkan ID
    $stmt = $conn->prepare("SELECT nama_barang, harga_beli, harga_jual, gambar FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nama_barang, $harga_beli, $harga_jual, $gambar);
    $stmt->fetch();

    // Kirim data dalam format JSON
    $data = array(
        'nama_barang' => $nama_barang,
        'harga_beli' => $harga_beli,
        'harga_jual' => $harga_jual,
        'gambar' => $gambar
    );

    echo json_encode($data);
}
?>
