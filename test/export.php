<?php
$mysqli = new mysqli("localhost", "root", "", "test_bt");

if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  exit;
}

// Check if month is selected via POST (from data.php)
if (isset($_POST['bulan'])) {
  $bulan = $_POST['bulan'];
  $sql = "SELECT * FROM transaksi WHERE MONTH(tanggal_transaksi) = '$bulan'";
  $bulan_formatted = date('F', mktime(0, 0, 0, $bulan, 10)); // Mengubah bulan angka menjadi nama bulan
  $judul = "Data Penjualan Bulan " . $bulan_formatted;
} else {
  // Default query to fetch all data if no month is selected
  $sql = "SELECT * FROM transaksi";
  $judul = "Data Penjualan Semua Bulan";
}

$result = mysqli_query($mysqli, $sql);

// Set header for Excel export
header('Content-Type: application/vnd.ms-excel');
$filename = "Data Penjualan " . (isset($bulan_formatted) ? $bulan_formatted : 'Semua_Bulan') . "_" . date('Y') . ".xls"; // Nama file dinamis
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Fetch all results as an array
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Menggabungkan data barang dengan nama yang sama pada hari dan bulan yang sama
$combined_data = [];
foreach ($data as $row) {
  // Ambil hanya tanggal (hari dan bulan) dari tanggal_transaksi
  $tanggal_key = date('d-m', strtotime($row['tanggal_transaksi']));
  $key = $tanggal_key . '-' . $row['nama_barang']; // Kunci unik: tanggal (tanpa tahun) + nama barang
  
  if (!isset($combined_data[$key])) {
    $combined_data[$key] = $row;
  } else {
    // Jika nama_barang sudah ada pada tanggal yang sama, akumulasikan jumlah_barang, jumlah_jual, dan keuntungan
    $combined_data[$key]['jumlah_barang'] += $row['jumlah_barang'];
    // Menghitung rata-rata harga beli dan jual
    $combined_data[$key]['harga_beli'] = ($combined_data[$key]['harga_beli'] + $row['harga_beli']) / 2;
    $combined_data[$key]['harga_jual'] = ($combined_data[$key]['harga_jual'] + $row['harga_jual']) / 2;
  }
}

// Mengonversi hasil penggabungan ke dalam array indeks
$combined_data = array_values($combined_data);

// Create table for data display
echo '<table>';
echo '<thead>';

// Tambahkan judul dengan ukuran font 16
echo '<tr><th colspan="9" style="text-align:center; font-size:16px;">' . $judul . '</th></tr>'; // Judul dengan colspan agar berada di tengah tabel
echo '<tr></tr>'; // Baris kosong untuk jarak antara judul dan header tabel

// Header tabel
echo '<tr>';
echo '<th>NO</th>';
echo '<th>Nama Barang</th>';
echo '<th>Harga Beli (Rp.)</th>';
echo '<th>Harga Jual (Rp.)</th>';
echo '<th>Jumlah Barang</th>';
echo '<th>Keuntungan (Rp.)</th>';
echo '<th>Jumlah Keuntungan (Rp.)</th>';
echo '<th>Jumlah Jual (Rp.)</th>';
echo '<th>Tanggal Transaksi</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$no = 1; // Inisialisasi nomor urut
$total_jumlah_jual = 0; // Inisialisasi total jumlah jual
foreach ($combined_data as $row) {
  echo '<tr>';
  echo '<td>' . $no++ . '</td>'; // Kolom nomor
  echo '<td>' . $row['nama_barang'] . '</td>';

  // Format prices with number_format and "Rp." prefix
  echo '<td>Rp. ' . number_format($row['harga_beli'], 2, ',', '.') . '</td>';
  echo '<td>Rp. ' . number_format($row['harga_jual'], 2, ',', '.') . '</td>';
  echo '<td>' . $row['jumlah_barang'] . '</td>';

  // Calculate and format additional values
  $keuntungan = $row['harga_jual'] - $row['harga_beli'];
  $jumlah_jual = $row['harga_jual'] * $row['jumlah_barang'];
  $jumlah_keuntungan = $jumlah_jual - ($row['harga_beli'] * $row['jumlah_barang']);
  $tanggalnya = strtotime($row['tanggal_transaksi']);
  $tanggal_formatted = date('d F Y', $tanggalnya);

  echo '<td>Rp. ' . number_format($keuntungan, 2, ',', '.') . '</td>';
  echo '<td>Rp. ' . number_format($jumlah_keuntungan, 2, ',', '.') . '</td>';
  echo '<td>Rp. ' . number_format($jumlah_jual, 2, ',', '.') . '</td>'; // Format jumlah jual
  echo '<td>' . $tanggal_formatted . '</td>';

  // Tambahkan jumlah jual ke total
  $total_jumlah_jual += $jumlah_jual;

  echo '</tr>';
}

// Tambahkan baris untuk "Total Jumlah Jual"
echo '<tr>';
echo '<td colspan="7" style="text-align:right; font-weight:bold;">Total Jumlah Jual (Rp.)</td>';
echo '<td colspan="2" style="font-weight:bold;">Rp. ' . number_format($total_jumlah_jual, 2, ',', '.') . '</td>';
echo '</tr>';

echo '</tbody>';
echo '</table>';

$mysqli->close(); // Close the database connection
?>
