<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "Sem4ng4tsukses!";
$database = "devices";

$conn = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$id = $_GET['id'];
$sn = $_GET['sn'];
$unit = $_GET['pt'];
$pt = $_GET['unit'];
$param = TRUE;

if (isset($_GET['id'])&&isset($_GET['sn'])&&isset($_GET['pt']&&isset($_GET['unit']))) {
  $param=TRUE;
} else {
  $param = FALSE;
  echo "ID not provided.";
}

// Ambil data dari form jika ada
if ($param && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Query untuk mengambil data dari tanggal awal hingga tanggal akhir
    $query = "SELECT * FROM stb2_log WHERE waktu BETWEEN '$start_date' AND '$end_date' AND kodeorg = '$pt' AND kodetangki = '$unit' ORDER BY waktu DESC";
} else if ($param) {
    // Jika tidak ada data dari form, ambil 20 data terakhir
    $query = "SELECT * FROM stb2_log WHERE kodeorg = '$pt' AND kodetangki = '$unit' ORDER BY waktu DESC LIMIT 50";
}

$result = $conn->query($query);

// Inisialisasi array untuk menyimpan data yang akan digunakan dalam grafik dan tabel
$chartData = [];
$tableData = [];

// Format data untuk digunakan dalam grafik dan tabel
foreach ($result as $row) {
    $tinggi_total = $row['sounding'] + 108;
    $tinggi_cm = intval($tinggi_total);
    $tinggi_mm = intval(($tinggi_total - $tinggi_cm) * 10);
    $tinggi_mm_col = $tinggi_mm . "mm";

    // Query untuk mengambil volume_cm dari pabrik_5tinggitangki
    $query_cm = "SELECT volume FROM pabrik_5tinggitangki WHERE tinggi = $tinggi_cm AND millcode = '$pt' AND kodetangki = '$unit'" ;
    $result_cm = $conn->query($query_cm);

    if ($result_cm === false) {
        echo "Error in query_cm: " . $conn->error;
        continue; // Skip to the next iteration
    }

    $volume_cm = $result_cm->fetch_assoc()['volume'];

    // Query untuk mengambil volume_mm dari pabrik_5milimeter
    // Sesuaikan dengan struktur tabel pabrik_5milimeter
    $query_mm = "SELECT `$tinggi_mm_col` FROM pabrik_5milimeter WHERE $tinggi_total <= pabrik_5milimeter.max AND $tinggi_total >= pabrik_5milimeter.min AND millcode = '$pt' AND kodetangki = '$unit'";
    $result_mm = $conn->query($query_mm);
    
    if ($result_mm === false) {
        echo "Error in query_mm: " . $conn->error;
        continue; // Skip to the next iteration
    }

    $volume_mm = $result_mm->fetch_assoc()[$tinggi_mm_col];

    // Kalkulasi volume_akhir
    $temperature = $row['suhu'];
    $volume_akhir = ($volume_cm + $volume_mm) * (1 + (3.48e-5 * ($temperature - 32)));

    // Tambahkan data ke array untuk grafik
    if($row['duration']<750){
    $chartData[] = [
        'waktu' => $row['waktu'],
        'sounding' => $row['sounding'],
        'suhu' => $row['suhu'],
        'duration' => $row['duration'],
        'bat' => $row['bat'],
        'air' => $row['air'], // Tambahkan kolom air
        'lumpur'=> $row['lumpur'],
        'volume' => $volume_akhir
    ];
  }
    // Tambahkan data ke array untuk tabel
    $tableData[] = [
        'waktu' => $row['waktu'],
        'sounding' => $row['sounding'],
        'suhu' => $row['suhu'],
        'duration' => $row['duration'],
        'bat' => $row['bat'],
        'volume' => number_format($volume_akhir, 2), // Menambahkan format desimal dua angka
        'tonase' => number_format((($volume_akhir*((-0.0007 * $row['suhu']) + 0.9222))/1000),2)
    ];
}

// Tutup koneksi database
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report ODSOU</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div style="width: 90%; margin: 20px auto 0 auto; text-align: center;">
    <h2>Tampilkan Data</h2>
    <form action="" method="post"> <!-- Tambahkan method="post" pada form -->
      <label for="start_date">Tanggal Awal:</label>
      <input type="date" id="start_date" name="start_date"> <!-- Tambahkan atribut name -->

      <label for="end_date">Tanggal Akhir:</label>
      <input type="date" id="end_date" name="end_date"> <!-- Tambahkan atribut name -->

      <button type="submit" name="submit">Tampilkan Data</button> <!-- Tambahkan atribut name -->
    </form>
  </div>

  <div style="width: 90%; margin: 20px auto 0 auto; text-align: center;">
    <h2>Grafik Sounding</h2>
    <canvas id="myChart" width="800" height="400"></canvas>
  </div>

  <div style="width: 90%; margin: 20px auto 0 auto; text-align: center;">
    <h2>Tabel Hasil Sounding</h2>
    <table style="margin: 0 auto;" border="1" cellpadding="5">
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Sounding (cm)</th>
          <th>Suhu (°C)</th>
          <th>Durasi (menit)</th>
          <th>Baterai (%)</th>
          <th>Volume (liter)</th>
          <th>Tonase (ton)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tableData as $entry) : ?>
          <tr>
            <td><?php echo $entry['waktu']; ?></td>
            <td><?php echo $entry['sounding']; ?></td>
            <td><?php echo $entry['suhu']; ?></td>
            <td><?php echo $entry['duration']; ?></td>
            <td><?php echo $entry['bat']; ?></td>
            <td><?php echo $entry['volume']; ?></td>
            <td><?php echo $entry['tonase']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <script>
    // Ambil data dari PHP dan format sesuai kebutuhan grafik
    var data = <?php echo json_encode(array_reverse($chartData)); ?>;

    // Inisialisasi dan konfigurasi grafik
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.map(entry => entry.waktu),
        datasets: [{
          label: 'Sounding',
          data: data.map(entry => entry.sounding),
          backgroundColor: 'rgba(204, 102, 0, 0.2)', // Warna orange tua
          borderColor: 'rgba(204, 102, 0, 1)', // Warna orange tua
          borderWidth: 1,
          yAxisID: 'y-axis-1'
        },
        {
          label: 'Suhu',
          data: data.map(entry => entry.suhu),
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          borderColor: 'rgba(255, 99, 132, 1)',
          borderWidth: 1,
          yAxisID: 'y-axis-1'
        },
        {
          label: 'Air',
          data: data.map(entry => entry.air),
          backgroundColor: 'rgba(135, 206, 235, 0.2)',
          borderColor: 'rgba(135, 206, 235, 1)',
          borderWidth: 1,
          yAxisID: 'y-axis-1'
        },
        {
          label: 'Lumpur',
          data: data.map(entry => entry.lumpur),
          backgroundColor: 'rgba(139, 69, 19, 0.2)',
          borderColor: 'rgba(139, 69, 19, 1)',
          borderWidth: 1,
          yAxisID: 'y-axis-1'
        },
        {
          label: 'Durasi',
          data: data.map(entry => entry.duration),
          backgroundColor: 'rgba(255, 206, 86, 0.2)',
          borderColor: 'rgba(255, 206, 86, 1)',
          borderWidth: 1,
          yAxisID: 'y-axis-2'
        },
        {
          label: 'Baterai',
          data: data.map(entry => entry.bat),
          backgroundColor: 'rgba(0, 128, 0, 0.2)', // Hijau untuk baterai
          borderColor: 'rgba(0, 128, 0, 1)',
          borderWidth: 1,
          yAxisID: 'y-axis-1'
        }]
      },
      options: {
        responsive: true,
        onClick: function(event, elements) {
          // Tanggapi peristiwa klik di sini dan tampilkan detail informasi
          if (elements.length > 0) {
            var index = elements[0]._index;
            var detailInfo = data[index];

            // Tampilkan informasi detail di sini (gunakan HTML atau modul pop-up)
            console.log(detailInfo);
          }
        },
        scales: {
          yAxes: [{
            id: 'y-axis-1',
            type: 'linear',
            position: 'left',
            scaleLabel: {
              display: true,
              labelString: 'Sounding (cm), Suhu (°C), Air, dan Lumpur'
            }
          },
          {
            id: 'y-axis-2',
            type: 'linear',
            position: 'right',
            scaleLabel: {
              display: true,
              labelString: 'Durasi (menit) & Baterai (%)'
            }
          }]
        }
      }
    });
</script>
</body>
</html>
