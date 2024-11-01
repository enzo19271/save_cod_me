<?php
// Koneksi ke database
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="style/edit.css"/>
    <!-- Tambahkan CSS Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>

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
        <h2>Edit Produk</h2>
        <div class="form-group">
            <label for="nama_barang">Pilih Produk</label>
            <!-- Tambahkan style="width:100%;" untuk Select2 -->
            <select id="nama_barang" name="nama_barang" style="width: 100%;">
                <option value="">-- Pilih Produk --</option>
                <?php
                // Ambil daftar produk dari database
                $stmt = $conn->prepare("SELECT id, nama_barang FROM products ORDER BY nama_barang ASC");
                $stmt->execute();
                $stmt->bind_result($id, $nama_barang);

                while ($stmt->fetch()) {
                    echo "<option value='$id'>$nama_barang</option>";
                }

                $stmt->close();
                ?>
            </select>
        </div>

        <div id="produk-detail" style="display:none;">
            <!-- Gambar Produk -->
            <div class="form-group">
                <label for="gambar">Gambar Produk</label>
                <div class="image-preview" id="imagePreview">
                    <img src="" alt="Preview Gambar" id="produkGambar" style="display: none;">
                </div>
            </div>

            <!-- Harga Beli -->
            <div class="form-group">
                <label for="harga_beli">Harga Beli</label>
                <input type="number" id="harga_beli" name="harga_beli" value="" required>
            </div>

            <!-- Harga Jual -->
            <div class="form-group">
                <label for="harga_jual">Harga Jual</label>
                <input type="number" id="harga_jual" name="harga_jual" value="" required>
            </div>

            <!-- Wrapper untuk tombol simpan dan hapus -->
            <div class="button-group">
                <button type="submit" id="hapusProduk" style="background-color: red; color: white;">Hapus Produk</button>
                <button type="submit" id="simpanPerubahan">Simpan Perubahan</button>
            </div>
        </div>

    </div>
</div>

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Tambahkan JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Navbar Function -->
<script>
    const mobileMenu = document.getElementById("mobile-menu");
    const menu = document.querySelector(".menu");

    mobileMenu.addEventListener("click", () => {
        menu.classList.toggle("active");
    });
</script>

<script>
$(document).ready(function(){
    // Inisialisasi Select2 pada dropdown produk
    $('#nama_barang').select2({
        placeholder: 'Cari produk',
        allowClear: true
    });

    // Ketika produk dipilih dari dropdown
    $('#nama_barang').change(function(){
        var produkId = $(this).val();

        if(produkId) {
            // Gunakan AJAX untuk mendapatkan data produk berdasarkan ID
            $.ajax({
                url: 'get_produk.php', // File untuk mengambil data produk
                type: 'POST',
                data: {id: produkId},
                dataType: 'json',
                success: function(data) {
                    if(data) {
                        // Tampilkan form dengan data produk yang dipilih
                        $('#produk-detail').show();
                        $('#produkGambar').attr('src', 'images/' + data.gambar).show();
                        $('#harga_beli').val(data.harga_beli);
                        $('#harga_jual').val(data.harga_jual);
                    }
                }
            });
        } else {
            // Sembunyikan form jika tidak ada produk yang dipilih
            $('#produk-detail').hide();
        }
    });

    // Fungsi untuk simpan perubahan produk
    $('#simpanPerubahan').click(function(e) {
        e.preventDefault();
        var id = $('#nama_barang').val();
        var harga_beli = $('#harga_beli').val();
        var harga_jual = $('#harga_jual').val();

        $.ajax({
            url: 'edit_produk_process.php', // File untuk proses edit produk
            type: 'POST',
            data: {
                id: id,
                harga_beli: harga_beli,
                harga_jual: harga_jual
            },
            success: function(response) {
                alert("Produk berhasil diubah");
                location.reload(); // Refresh halaman setelah update
            }
        });
    });

    // Fungsi untuk hapus produk
    $('#hapusProduk').click(function(e) {
        e.preventDefault();
        var id = $('#nama_barang').val();

        if(confirm("Apakah Anda yakin ingin menghapus produk ini?")) {
            $.ajax({
                url: 'hapus_produk_process.php', // File untuk proses hapus produk
                type: 'POST',
                data: {id: id},
                success: function(response) {
                    alert("Produk berhasil dihapus");
                    location.reload(); // Refresh halaman setelah delete
                }
            });
        }
    });
});
</script>

</body>
</html>
