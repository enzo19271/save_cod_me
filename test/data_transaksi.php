<?php
$mysqli = new mysqli("localhost", "root", "", "test_bt");

// Use $mysqli to query the database
$result = mysqli_query($mysqli, "SELECT * FROM transaksi ORDER BY nama_barang");

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BUMDesa Belilik</title>
    <link rel="stylesheet" href="style/data.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Tambahkan ini -->
  </head>
  <body?>
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
    <!-- Batas -->

    <div class="container">
        <!-- Edit Data Produk Section -->
        <div class="card">
            <h2>EDIT & UPLOAD DATA PRODUK</h2>
            <p>Upload data produk berdasarkan harga baru! pastikan data terinput dengan sempurna!</p>
            <form class="formbln" action="upload.php" method="post" enctype="multipart/form-data">
                <button type="submit" class="btn">Upload Data</button>
            </form>
            <form class="formbln" action="edit_produk.php" method="post" enctype="multipart/form-data">
                <button type="submit" class="btn">Edit & Hapus Data</button>
            </form>
        </div>

        <!-- Export Data Penjualan Section -->
        <div class="card">
            <h2>DOWNLOAD DATA PENJUALAN</h2>
            <p>Download data penjualan perbulan atau semua bulan dalam satu file berbentuk excel.</p>
            <form class="formbln" action="export.php" method="post">
              <label for="bulan">Pilih Bulan:</label>
              <select class="select" name="bulan" id="bulan">
                <?php
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                  echo "<option value='$bulan'>" . date('F', mktime(0, 0, 0, $bulan, 1)) . "</option>";
                }
                ?>
              </select>
              <button type="submit" class="btn">Export Perbulan</button>
            </form>
        </div>
    </div>

    <div class="container2">
      <div class="form-container2">
        <h2>DATA TRANSAKSI</h2>
        <div class="content5">
          <?php if (isset($_GET['succes'])) { ?>
            <div class="alert-success">
              <span class="alertClose">X</span>
              <span class="alertText"><?php echo $_GET['succes']; ?></span>
            </div>
            <script>
              setTimeout(function() {
                var alert = document.querySelector('.alert-success');
                alert.style.display = 'none';
              }, 3000);
            </script>
          <?php } ?>
          <form action="export.php" method="post">
            <div class="table-container5">
              <table>
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Jumlah Barang</th>
                    <th>Keuntungan</th>
                    <th>Jumlah Keuntungan</th>
                    <th>Jumlah Jual</th>
                    <th>Tanggal Transaksi</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $result = mysqli_query($mysqli, "SELECT * FROM transaksi ORDER BY id DESC");
                  while ($r = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>" . $r['id'] . "</td>";
                    echo "<td>" . $r['nama_barang'] . "</td>";

                    $harga_beli = number_format($r['harga_beli'], 2, ',', '.');
                    $harga_jual = number_format($r['harga_jual'], 2, ',', '.');
                    $keuntungan = number_format($r['harga_jual'] - $r['harga_beli'], 2, ',', '.');
                    $jumlah_keuntungan = number_format(($r['harga_jual'] - $r['harga_beli']) * $r['jumlah_barang'], 2, ',', '.');
                    $jumlah_jual = number_format($r['harga_jual'] * $r['jumlah_barang'], 2, ',', '.');

                    echo "<td>Rp. {$harga_beli}</td>";
                    echo "<td>Rp. {$harga_jual}</td>";
                    echo "<td>" . $r['jumlah_barang'] . "</td>";
                    echo "<td>Rp. {$keuntungan}</td>";
                    echo "<td>Rp. {$jumlah_keuntungan}</td>";
                    echo "<td>Rp. {$jumlah_jual}</td>";
                    echo "<td>" . $r['tanggal_transaksi'] . "</td>";
                    // Ganti teks "Delete" dengan ikon trash
                    echo "<td><a href='delete.php?id=" . $r['id'] . "'><i class='fas fa-trash' style='color: red;'></i></a></td>";
                    echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
              <button type="submit" class="btn">Export Semua</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Navbar Function -->
    <script>
      const mobileMenu = document.getElementById("mobile-menu");
      const menu = document.querySelector(".menu");

      mobileMenu.addEventListener("click", () => {
        menu.classList.toggle("active");
      });
    </script>
</body>
</html>
