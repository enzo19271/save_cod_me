<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil total kas saat ini
    $total_kas_saat_ini = $_POST['total_kas_saat_ini'];
    
    // Misalnya, kamu ingin mencatat bahwa kas ditarik
    $keterangan = "Penarikan Kas";
    $tanggal = date('Y-m-d');

    // Memasukkan data penarikan ke modal_kas (atau mungkin kamu punya tabel lain untuk penarikan)
    $query = "INSERT INTO modal_kas (jumlah_modal, keterangan, tanggal) VALUES ('-$total_kas_saat_ini', '$keterangan', '$tanggal')";
    mysqli_query($mysqli, $query);

    // Redirect kembali ke halaman utama setelah berhasil
    header('Location: data_transaksi.php?succes=Kas berhasil ditarik');
}
?>
