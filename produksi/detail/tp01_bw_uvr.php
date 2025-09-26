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
// Default fetch last 7 days if no date range is selected
if (!isset($_POST['start_date']) && !isset($_POST['end_date'])) {
    $end_date = date('Y-m-d', strtotime('+1 days'));
    $start_date = date('Y-m-d', strtotime('-7 days'));
} else {
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
}

// Fetch data based on the date range
$sql = "SELECT sn, ch, waktu FROM tp01_log WHERE waktu BETWEEN ? AND ? AND sn=$sn ORDER BY waktu";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);

$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>TP01_BW_UVR Detail</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        h2 {
            text-align: center;
        }
        .form-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        .chart-wrapper {
            width: 70%;
        }
        .legend {
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <h2>Pilih Tanggal</h2>
    <div class="form-container">
        <form method="post" action="">
            Tanggal Mulai: <input type="date" name="start_date">
            Tanggal Akhir: <input type="date" name="end_date">
            <input type="submit" value="Filter">
        </form>
    </div>

    <h2>Curah Hujan Harian</h2>
    <div class="chart-container">
        <div class="chart-wrapper">
            <canvas id="dailyChart"></canvas>
        </div>
        <div class="legend">
            <h3>Legenda</h3>
            <p><span style="color: #808080">&#9632;</span> Berawan: 0 mm/hari</p>
            <p><span style="color: #008000">&#9632;</span> Hujan Ringan: 0.5 – 20 mm/hari</p>
            <p><span style="color: #FFFF00">&#9632;</span> Hujan Sedang: 20 – 50 mm/hari</p>
            <p><span style="color: #FFA500">&#9632;</span> Hujan Lebat: 50 – 100 mm/hari</p>
            <p><span style="color: #FF0000">&#9632;</span> Hujan Sangat Lebat: 100 – 150 mm/hari</p>
            <p><span style="color: #800080">&#9632;</span> Hujan Ekstrem: > 150 mm/hari</p>
            <p>Sumber: <a href="https://www.bmkg.go.id/cuaca/probabilistik-curah-hujan.bmkg">BMKG</a></p>
        </div>
    </div>

    <h2>Intensitas Hujan Per Jam</h2>
    <div class="chart-container">
        <div class="chart-wrapper">
            <canvas id="hourlyChart"></canvas>
        </div>
        <div class="legend">
            <h3>Legenda</h3>
            <p><span style="color: #008000">&#9632;</span> Intensitas Ringan: 0 – 2.5 mm/jam</p>
            <p><span style="color: #FFFF00">&#9632;</span> Intensitas Sedang: 2.5 – 10 mm/jam</p>
            <p><span style="color: #FFA500">&#9632;</span> Intensitas Lebat: 10 – 50 mm/jam</p>
            <p><span style="color: #FF0000">&#9632;</span> Intensitas Sangat Lebat: > 50 mm/jam</p>
            <p>Sumber: <a href="https://public.wmo.int/en" title="World Meteorological Organization. Guide to Instruments and Methods of Observation; World Meteorological Organization: Geneva, Switzerland, 2018; p. 548.">WMO 2018</a></p>
        </div>
    </div>

    <script>
        const data = <?php echo json_encode($data); ?>;

        const dailyData = {};
        const hourlyData = {};

        data.forEach(entry => {
            const date = entry.waktu.split(' ')[0];
            const hour = entry.waktu.split(' ')[1].split(':')[0];

            if (!dailyData[date]) dailyData[date] = 0;
            dailyData[date] += parseFloat(entry.ch);

            if (!hourlyData[date]) hourlyData[date] = {};
            if (!hourlyData[date][hour]) hourlyData[date][hour] = 0;
            hourlyData[date][hour] += parseFloat(entry.ch);
        });

        const dailyLabels = Object.keys(dailyData);
        const dailyValues = Object.values(dailyData);

        const getColorForDailyRainfall = (value) => {
            if (value === 0) return '#808080'; // Berawan
            if (value < 20) return '#008000'; // Hujan Ringan
            if (value < 50) return '#FFFF00'; // Hujan Sedang
            if (value < 100) return '#FFA500'; // Hujan Lebat
            if (value < 150) return '#FF0000'; // Hujan Sangat Lebat
            return '#800080'; // Hujan Ekstrem
        };

        const getColorForHourlyRainfall = (value) => {
            if (value < 2.5) return '#008000'; // Hujan Ringan
            if (value < 10) return '#FFFF00'; // Hujan Sedang
            if (value < 50) return '#FFA500'; // Hujan Lebat
            return '#FF0000'; // Hujan Sangat Lebat
        };

        const dailyColors = dailyValues.map(getColorForDailyRainfall);

        const dailyChartCtx = document.getElementById('dailyChart').getContext('2d');
        const dailyChart = new Chart(dailyChartCtx, {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Curah Hujan Harian (mm)',
                    data: dailyValues,
                    backgroundColor: dailyColors,
                    borderColor: dailyColors.map(color => color),
                    borderWidth: 1
                }]
            },
            options: {
                onClick: (e) => {
                    const points = dailyChart.getElementsAtEventForMode(e, 'nearest', { intersect: true }, true);
                    if (points.length) {
                        const firstPoint = points[0];
                        const label = dailyChart.data.labels[firstPoint.index];
                        showHourlyData(label);
                    }
                }
            }
        });

        const hourlyChartCtx = document.getElementById('hourlyChart').getContext('2d');
        let hourlyChart;

        function showHourlyData(date) {
            const hourlyLabels = Object.keys(hourlyData[date]);
            const hourlyValues = Object.values(hourlyData[date]);

            const hourlyColors = hourlyValues.map(getColorForHourlyRainfall);

            if (hourlyChart) {
                hourlyChart.destroy();
            }

            hourlyChart = new Chart(hourlyChartCtx, {
                type: 'bar',
                data: {
                    labels: hourlyLabels,
                    datasets: [{
                        label: `${date} (mm)`,
                        data: hourlyValues,
                        backgroundColor: hourlyColors,
                        borderColor: hourlyColors.map(color => color),
                        borderWidth: 1
                    }]
                }
            });
        }

        // Show hourly data for the latest day by default
        const latestDate = dailyLabels[dailyLabels.length - 1];
        showHourlyData(latestDate);
    </script>
</body>
</html>
