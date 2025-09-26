<?php
include "../../connection.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
$id = $_GET['id'];
$sn = $_GET['sn'];
$pt = $_GET['pt'];
$unit = $_GET['unit'];
$param = TRUE;

if (isset($_GET['id']) && isset($_GET['sn']) && isset($_GET['pt']) && isset($_GET['unit'])) {
    $param = TRUE;
} else {
    $param = FALSE;
    echo "ID not provided.";
}

if ($param && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $query = "SELECT * FROM stb2_log WHERE waktu BETWEEN '$start_date' AND '$end_date' AND kodeorg = '$pt' AND kodetangki = '$unit' ORDER BY waktu DESC";
    //echo $query;
  } else if ($param) {
    $query = "SELECT * FROM stb2_log WHERE kodeorg = '$pt' AND kodetangki = '$unit' ORDER BY waktu DESC LIMIT 50";
    //echo $query;
  }

$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}
$query_odsou ="SELECT * FROM pabrik_5odsou WHERE millcode = '$pt' AND kodetangki = '$unit' LIMIT 1";
$result_odsou = $conn->query($query_odsou);  // Corrected variable name

if (!$result_odsou) {
    die("Query failed: " . $conn->error);
}
$row_odsou='';
// Check if any rows were returned
if ($result_odsou->num_rows > 0) {
    // Fetch the first row from the result set
    $row_odsou= $result_odsou->fetch_assoc();  // Fetch as associative array

    // Access individual fields from the fetched row
    //echo $row_odsou['offset'];  // Assuming 'offset' is a column in your table
    //echo $row_odsou['mejaukur'];  // Assuming 'mejaukur' is a column in your table
} else {
    echo "No rows found";
}

$chartData = [];
$tableData = [];

if ($result->num_rows > 0) {
    foreach ($result as $row) {
        $tinggi_total = $row['sounding'] + ($row_odsou['mejaukur']/10) + $row_odsou['offset'];
        $tinggi_cm = intval($tinggi_total);
        $tinggi_mm = intval(($tinggi_total - $tinggi_cm) * 10);
        $tinggi_mm_col = $tinggi_mm . "mm";

        $query_cm = "SELECT volume FROM pabrik_5tinggitangki WHERE tinggi = $tinggi_cm AND millcode = '$pt' AND kodetangki = '$unit'";
        //echo $query;
        $result_cm = $conn->query($query_cm);

        if ($result_cm === false) {
            echo "Error in query_cm: " . $conn->error;
            continue;
        }

        $volume_cm = $result_cm->fetch_assoc()['volume'];

        $query_mm = "SELECT `$tinggi_mm_col` FROM pabrik_5milimeter WHERE $tinggi_total <= pabrik_5milimeter.max AND $tinggi_total >= pabrik_5milimeter.min AND millcode = '$pt' AND kodetangki = '$unit'";
        $result_mm = $conn->query($query_mm);

        if ($result_mm === false) {
            echo "Error in query_mm: " . $conn->error;
            continue;
        }

        $volume_mm = $result_mm->fetch_assoc()[$tinggi_mm_col];

        $temperature = $row['suhu'];
        $volume_akhir = ($volume_cm + $volume_mm) * (1 + (3.48e-5 * ($temperature - 32)));

        if ($row['average'] < 200) {
            $chartData[] = [
                'waktu' => $row['waktu'],
                'sounding' => ($row['sounding'] < 23.3) ? 0 : $row['sounding'] + $row_odsou['offset'],
                'suhu' => $row['suhu'],
                'duration' => $row['duration'],
                'bat' => $row['bat'],
                'air' => $row['air'],
                'lumpur' => $row['lumpur'],
                'volume' => ($row['sounding'] < 23.3) ? 0 : $volume_akhir
            ];
        }
        if ($row['average'] < 200) {
            $tableData[] = [
                'waktu' => $row['waktu'],
                'sounding' => ($row['sounding'] < 23.3) ? 0 : $row['sounding'] + $row_odsou['offset'],
                'suhu' => $row['suhu'],
                'duration' => $row['duration'],
                'bat' => $row['bat'],
                'volume' => ($row['sounding'] < 23.3) ? 0 : number_format($volume_akhir, 2),
                'tonase' => ($row['sounding'] < 23.3) ? 0 :number_format((($volume_akhir * ((-0.0007 * $row['suhu']) + 0.9222)) / 1000), 2)
            ];
        }
    }
} else {
    echo "No records found.";
}

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
        <form action="" method="post">
            <label for="start_date">Tanggal Awal:</label>
            <input type="date" id="start_date" name="start_date">
            <label for="end_date">Tanggal Akhir:</label>
            <input type="date" id="end_date" name="end_date">
            <button type="submit" name="submit">Tampilkan Data</button>
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
        var data = <?php echo json_encode(array_reverse($chartData)); ?>;
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(entry => entry.waktu),
                datasets: [{
                    label: 'Sounding',
                    data: data.map(entry => entry.sounding),
                    backgroundColor: 'rgba(204, 102, 0, 0.2)',
                    borderColor: 'rgba(204, 102, 0, 1)',
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
                    backgroundColor: 'rgba(0, 128, 0, 0.2)',
                    borderColor: 'rgba(0, 128, 0, 1)',
                    borderWidth: 1,
                    yAxisID: 'y-axis-1'
                }]
            },
            options: {
                responsive: true,
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
