<?php
// Koneksi ke database
include 'config.php';

// Inisialisasi variabel untuk edit
$id = $nama_barang = $harga_beli = $harga_jual = $gambar = "";
$isEdit = false;

// Jika ada parameter 'id', berarti ini adalah form untuk edit
if (isset($_GET['id'])) {
    $isEdit = true;
    $id = $_GET['id'];

    // Ambil data produk berdasarkan ID
    $stmt = $conn->prepare("SELECT id, nama_barang, harga_beli, harga_jual, gambar FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($id, $nama_barang, $harga_beli, $harga_jual, $gambar);
    $stmt->fetch();
    $stmt->close();
}

// Jika variabel tidak di-set dari database, tetap gunakan nilai default
if (empty($nama_barang)) {
    $nama_barang = '';
}

if (empty($harga_beli)) {
    $harga_beli = 0;
}

if (empty($harga_jual)) {
    $harga_jual = 0;
}

if (empty($gambar)) {
    $gambar = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload/Edit Produk</title>
    <link rel="stylesheet" href="style/upload.css"/>
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


<div class="container">
    <div class="form-container">
        <h2>Upload Data Produk</h2>

        <!-- Gambar Produk Ditempatkan Paling Atas -->
        <?php if (!empty($gambar)): ?>
            <div class="image-preview">
                <img src="images/<?php echo htmlspecialchars($gambar); ?>" alt="Gambar Produk" id="currentImage">
            </div>
        <?php endif; ?>

        <form action="up_process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="form-group">
                <label for="nama_barang">Nama Produk</label>
                <input type="text" id="nama_barang" name="nama_barang" value="<?php echo htmlspecialchars($nama_barang); ?>" required>
            </div>

            <div class="form-group">
                <label for="harga_beli">Harga Beli</label>
                <input type="number" id="harga_beli" name="harga_beli" value="<?php echo htmlspecialchars($harga_beli); ?>" required>
            </div>

            <div class="form-group">
                <label for="harga_jual">Harga Jual</label>
                <input type="number" id="harga_jual" name="harga_jual" value="<?php echo htmlspecialchars($harga_jual); ?>" required>
            </div>

            <div class="form-group">
                <label for="gambar">Gambar Produk</label>
                <input type="file" id="gambar" name="gambar" accept="image/*">
                <div class="image-preview" id="imagePreview">
                    <img src="" alt="Preview Gambar" style="display: none;">
                </div>
            </div>

            <button type="submit"><?php echo $isEdit ? "Update Produk" : "Upload Produk"; ?></button>
        </form>

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

<script>
    // JavaScript untuk preview gambar yang baru diupload
    const gambarInput = document.getElementById('gambar');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = imagePreview.querySelector('img');

    gambarInput.addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            reader.addEventListener('load', function() {
                previewImage.setAttribute('src', this.result);
                previewImage.style.display = 'block';
            });

            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>
