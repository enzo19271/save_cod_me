<?php
// Koneksi ke database
$servername = "localhost"; // ganti dengan host database Anda
$username = "root"; // ganti dengan username database Anda
$password = ""; // ganti dengan password database Anda
$dbname = "test_bt"; // ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mendapatkan produk dalam urutan nama_barang secara alfabetis
$sql = "SELECT id, nama_barang, harga_beli, harga_jual, gambar FROM products ORDER BY nama_barang ASC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BUMDesa Belilik</title>
    <link rel="stylesheet" href="style/transaksi.css" />
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
    <!-- Batas -->

    <div class="container">
        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="search" placeholder="Cari produk...">
        </div>

        <!-- Product Grid -->
        <div class="product-grid" id="productGrid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Format harga ke dalam format Rupiah
                    $harga_beli = "Rp " . number_format($row["harga_beli"], 2, ',', '.');
                    $harga_jual = "Rp " . number_format($row["harga_jual"], 2, ',', '.');

                    echo '
                    <div class="product-item">
                        <div class="card">
                            <img class="img-sc" src="images/'.$row["gambar"].'" alt="'.$row["nama_barang"].'">
                            <div class="card-body">
                                <h5 class="card-title">'.$row["nama_barang"].'</h5>
                                <p>Harga: '.$harga_jual.'</p>
                                <button class="btn" onclick="openModal(\''.$row["nama_barang"].'\', '.$row["harga_beli"].', '.$row["harga_jual"].', '.$row["id"].')">Check Out</button>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo "0 produk ditemukan";
            }
            $conn->close();
            ?>
        </div>
    </div>

    <!-- Modal for transaction -->
    <div id="modal" class="modal">
      <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Transaksi</h2>
        <form action="process.php" method="POST">
            <input type="hidden" id="id" name="id">
            <div class="form-group">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" id="nama_barang" name="nama_barang" readonly>
            </div>
            <div class="form-group">
                <label for="harga_beli">Harga Beli:</label>
                <input type="number" id="harga_beli" name="harga_beli" readonly>
            </div>
            <div class="form-group">
                <label for="harga_jual">Harga Jual:</label>
                <input type="number" id="harga_jual" name="harga_jual" readonly>
            </div>
            <div class="form-group">
                <label for="jumlah_barang">Jumlah Barang:</label>
                <input type="number" id="jumlah_barang" name="jumlah_barang" required>
            </div>
            <button type="submit" class="btn">Simpan Transaksi</button>
        </form>
      </div>
    </div>

    <!-- CSS for Modal -->
    <style>
      /* Modal styles */
      .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
      }

      .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 400px;
      }

      .close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
      }

      .close-btn:hover,
      .close-btn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
      }

      .form-group {
        margin-bottom: 15px;
      }

      .form-group label {
        font-weight: bold;
      }

      .form-group input {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
      }

      .btn {
        background-color: #0a285f;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
      }

      .btn:hover {
        background-color: #ffb400;
      }
    </style>
    <!-- Script for Modal functionality -->
    <script>
      function openModal(nama_barang, harga_beli, harga_jual, id) {
          document.getElementById('nama_barang').value = nama_barang;
          document.getElementById('harga_beli').value = harga_beli;
          document.getElementById('harga_jual').value = harga_jual;
          document.getElementById('id').value = id;
          document.getElementById('modal').style.display = 'block';
      }

      function closeModal() {
          document.getElementById('modal').style.display = 'none';
      }
    </script>

    <script>
      const mobileMenu = document.getElementById("mobile-menu");
      const menu = document.querySelector(".menu");

      mobileMenu.addEventListener("click", () => {
        menu.classList.toggle("active");
      });
    </script>

    <!-- Search Function -->
    <script>
        document.getElementById('search').addEventListener('keyup', function() {
            var searchText = this.value.toLowerCase();
            var products = document.querySelectorAll('.product-item');

            products.forEach(function(product) {
                var title = product.querySelector('.card-title').textContent.toLowerCase();
                if (title.includes(searchText)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    </script>
  </body>
</html>
