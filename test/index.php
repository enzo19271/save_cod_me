<?php
include_once("config.php");

// Mendapatkan bulan saat ini
$bulan_ini = date('Y-m');

// Query untuk mendapatkan total penjualan bulan ini (harga_jual * jumlah_barang)
$query_saldo_saat_ini = "SELECT SUM(harga_jual * jumlah_barang) AS total_penjualan 
                         FROM transaksi 
                         WHERE DATE_FORMAT(tanggal_transaksi, '%Y-%m') = '$bulan_ini'";
$result_saldo = mysqli_query($conn, $query_saldo_saat_ini);
$data_saldo = mysqli_fetch_assoc($result_saldo);
$saldo_saat_ini = $data_saldo['total_penjualan'] ?? 0;

// Query untuk mendapatkan total pendapatan bulan ini
$query_pendapatan_bulan_ini = "SELECT SUM(harga_jual * jumlah_barang) AS total_pendapatan 
                               FROM transaksi 
                               WHERE DATE_FORMAT(tanggal_transaksi, '%Y-%m') = '$bulan_ini'";
$result_pendapatan = mysqli_query($conn, $query_pendapatan_bulan_ini);
$data_pendapatan = mysqli_fetch_assoc($result_pendapatan);
$pendapatan_bulan_ini = $data_pendapatan['total_pendapatan'] ?? 0;

// Proses jika form tambah modal/saldo diajukan
if (isset($_POST['tambah_saldo'])) {
    $jumlah_saldo = $_POST['jumlah_saldo'];
    $keterangan = $_POST['keterangan'];
    $tanggal_saat_ini = date('Y-m-d H:i:s'); // Tanggal otomatis

    // Query untuk menyimpan data saldo/modal baru ke tabel modal_kas
    $query_tambah_saldo = "INSERT INTO modal_kas (jumlah_modal, keterangan, tanggal) 
                           VALUES ('$jumlah_saldo', '$keterangan', '$tanggal_saat_ini')";
    mysqli_query($conn, $query_tambah_saldo);
    
    // Redirect agar tidak terjadi resubmission
    header("Location: index.php");
    exit();
}

// Proses untuk menghapus saldo
if (isset($_GET['hapus_saldo'])) {
    $id_saldo = $_GET['hapus_saldo'];
    $query_hapus_saldo = "DELETE FROM modal_kas WHERE id = '$id_saldo'";
    mysqli_query($conn, $query_hapus_saldo);
    
    // Redirect setelah menghapus
    header("Location: index.php");
    exit();
}

// Query untuk mendapatkan total saldo/modal dari modal_kas
$query_total_modal = "SELECT SUM(jumlah_modal) AS total_modal FROM modal_kas";
$result_modal = mysqli_query($conn, $query_total_modal);
$data_modal = mysqli_fetch_assoc($result_modal);
$total_modal = $data_modal['total_modal'] ?? 0;

// Menghitung Kas Saat Ini (modal_kas + total penjualan bulan ini)
$kas_saat_ini = $total_modal + $saldo_saat_ini;

// Query untuk menampilkan data saldo yang sudah ditambahkan
$query_data_saldo = "SELECT * FROM modal_kas ORDER BY tanggal DESC";
$result_data_saldo = mysqli_query($conn, $query_data_saldo);

// Proses Tarik Saldo
if (isset($_POST['tarik_saldo'])) {
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_akhir = $_POST['tanggal_akhir'];
    $alasan = $_POST['alasan'];
    $tanggal_saat_ini = date('Y-m-d H:i:s'); // Tanggal saat ini

    // Query untuk menghitung keuntungan dalam rentang tanggal yang dipilih
    $query_tarik_saldo = "SELECT SUM((harga_jual - harga_beli) * jumlah_barang) AS total_keuntungan 
                          FROM transaksi 
                          WHERE tanggal_transaksi BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
    $result_tarik_saldo = mysqli_query($conn, $query_tarik_saldo);
    $data_tarik_saldo = mysqli_fetch_assoc($result_tarik_saldo);
    $total_keuntungan = $data_tarik_saldo['total_keuntungan'] ?? 0;

    if ($total_keuntungan > 0) {
        // Kurangi kas saat ini
        $kas_saat_ini -= $total_keuntungan;

        // Simpan penarikan ke modal_kas dengan keterangan
        $query_simpan_penarikan = "INSERT INTO modal_kas (jumlah_modal, keterangan, tanggal) 
                                   VALUES ('-$total_keuntungan', '$alasan', '$tanggal_saat_ini')";
        mysqli_query($conn, $query_simpan_penarikan);
        
        // Redirect setelah penarikan
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Tidak ada keuntungan pada rentang waktu ini');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BUMDesa Belilik</title>
    <link rel="stylesheet" href="style/home.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Tambahkan ini -->
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <h1>BUMDESA Belilik</h1>
        </div>
        <div class="menu-toggle" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <ul class="menu">
            <li><a href="index.php">Homepage</a></li>
            <li><a href="transaksi.php">Transaksi</a></li>
            <li><a href="data_transaksi.php">Data Transaksi</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Sambutan -->
        <div class="card welcome-card">
            <h2>Selamat Datang di BUMDESA Belilik!</h2>
            <p>Selamat bekerja! Semoga hari ini produktif dan penuh keberkahan.</p>
        </div>

        <!-- Card Info -->
        <div class="card-container">
            <div class="card info-card">
                <h3>Kas Saat Ini</h3>
                <p>Rp <?= number_format($kas_saat_ini, 0, ',', '.') ?></p> <!-- Menampilkan kas saat ini -->
            </div>

            <div class="card info-card">
                <h3>Pendapatan Bulan Ini</h3>
                <p>Rp <?= number_format($pendapatan_bulan_ini, 0, ',', '.') ?></p>
            </div>
        </div>

        <!-- Card Tambah Saldo -->
        <div class="card tambah-saldo-card">
            <h2>Tambah Kas</h2>
            <p>Pastikan data terinput dengan benar!</p>
            <form method="POST" action="">
                <div class="form-row-horizontal">
                    <div class="input-group">
                        <span>Rp.</span>
                        <input type="number" name="jumlah_saldo" id="jumlah_saldo" placeholder="xxx.xxx.xxx" required>
                    </div>
                    <div class="form-group">
                        <select name="keterangan" id="keterangan">
                            <option value="Modal Awal">Modal Awal</option>
                            <option value="Penambahan Modal">Penambahan Modal</option>
                            <option value="Investasi">Investasi</option>
                        </select>
                    </div>
                    <button type="submit" name="tambah_saldo" class="btn-simpan">Simpan</button>
                </div>
            </form>
        </div>

        <!-- Tabel Data Saldo -->
        <div class="card data-saldo-card">
            <h3>Data Saldo/Kas</h3>
            <table border="1">
                <tr>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result_data_saldo)): ?>
                <tr>
                    <td><?= $row['keterangan'] ?></td>
                    <td>Rp <?= number_format($row['jumlah_modal'], 0, ',', '.') ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></td>
                    <td><a href="index.php?hapus_saldo=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')"><i class='fas fa-trash' style='color: red;'></i</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <!-- Card Tarik Saldo -->
    <div class="card tambah-saldo-card">
        <h2>Tarik Kas</h2>
        <p>Pilih rentang tanggal dan alasan penarikan.</p>
        <form method="POST" action="">
            <div class="form-row-horizontal">
                <div class="input-group">
                    <label for="tanggal_mulai">Tanggal Mulai:</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" required>
                </div>
                <div class="input-group">
                    <label for="tanggal_akhir">Tanggal Akhir:</label>
                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" required>
                </div>
                <div class="input-group">
                    <label for="alasan">Alasan Penarikan:</label>
                    <select name="alasan" id="alasan" required>
                        <option value="Keperluan Operasional">Keperluan Operasional</option>
                        <option value="Pengembalian Investasi">Pengembalian Investasi</option>
                        <option value="Pembayaran Utang">Pembayaran Utang</option>
                    </select>
                </div>
                <button type="submit" name="tarik_saldo" class="btn-simpan">Tarik Saldo</button>
            </div>
        </form>
    </div>


    <script>
        const mobileMenu = document.getElementById("mobile-menu");
        const menu = document.querySelector(".menu");

        mobileMenu.addEventListener("click", () => {
            menu.classList.toggle("active");
        });
    </script>
</body>
</html>
