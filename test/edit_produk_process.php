<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];

    // Update data produk
    $stmt = $conn->prepare("UPDATE products SET harga_beli=?, harga_jual=? WHERE id=?");
    $stmt->bind_param("ddi", $harga_beli, $harga_jual, $id);
    $stmt->execute();
    $stmt->close();
}
?>
