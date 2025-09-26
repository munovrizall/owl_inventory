<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Tangki CPO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #95a5a6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .device-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .device-details h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .device-details p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 3px;
        }
        
        .status-indicators {
            display: flex;
            gap: 15px;
        }
        
        .status-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 15px;
            border-radius: 8px;
            min-width: 100px;
        }
        
        .status-value {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .status-label {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .main-content {
            display: grid;
            grid-template-columns: 1fr 3fr;
            gap: 20px;
        }
        
        .sidebar {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .filter-section {
            margin-bottom: 25px;
        }
        
        .filter-section h3 {
            margin-bottom: 15px;
            color: var(--primary);
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        
        .date-filter {
            margin-bottom: 15px;
        }
        
        .date-filter label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .date-filter input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .parameter-selection {
            margin-top: 20px;
        }
        
        .param-checkbox {
            margin-bottom: 10px;
        }
        
        .param-checkbox label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        
        .content-area {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .chart-title {
            font-size: 18px;
            color: var(--primary);
            font-weight: 600;
        }
        
        .chart-actions {
            display: flex;
            gap: 10px;
        }
        
        .chart-actions button {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
        }
        
        .chart-canvas-container {
            height: 300px;
            position: relative;
        }
        
        .data-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .data-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        
        .data-card h3 {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .data-card .value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .data-card .unit {
            font-size: 12px;
            color: var(--gray);
        }
        
        .data-card .trend {
            font-size: 12px;
            margin-top: 5px;
        }
        
        .trend.up {
            color: var(--success);
        }
        
        .trend.down {
            color: var(--danger);
        }
        
        .data-table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--primary);
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: var(--gray);
        }
        
        .loading i {
            font-size: 40px;
            margin-bottom: 15px;
            color: var(--secondary);
        }
        
        @media (max-width: 992px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .device-info {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .status-indicators {
                margin-top: 15px;
                width: 100%;
                justify-content: space-between;
            }
            
            .data-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .status-indicators {
                flex-wrap: wrap;
            }
            
            .status-item {
                flex: 1 0 45%;
                margin-bottom: 10px;
            }
            
            .data-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="device-info">
                <div class="device-details">
                    <h1 id="custom-name">THEP ST01</h1>
                    <p id="chip-id">Chip ID: 2786842935</p>
                    <p id="placement">Lokasi: THEP</p>
                </div>
                <div class="status-indicators">
                    <div class="status-item">
                        <span class="status-label">BATERAI</span>
                        <span class="status-value" id="battery-value">76%</span>
                        <span class="status-label" id="battery-status">Baik</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">SUHU</span>
                        <span class="status-value" id="temperature-value">40.8°C</span>
                        <span class="status-label" id="temperature-status">Normal</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">STATUS</span>
                        <span class="status-value" id="connection-status">Online</span>
                        <span class="status-label" id="last-update">3 menit lalu</span>
                    </div>
                </div>
            </div>
        </header>
        
        <div class="main-content">
            <div class="sidebar">
                <div class="filter-section">
                    <h3><i class="fas fa-filter"></i> Filter Data</h3>
                    <div class="date-filter">
                        <label for="start-date">Tanggal Mulai:</label>
                        <input type="date" id="start-date" value="2025-08-31">
                    </div>
                    <button id="load-data" class="btn"><i class="fas fa-sync"></i> Muat Data</button>
                </div>
                
                <div class="parameter-selection">
                    <h3><i class="fas fa-chart-line"></i> Parameter Grafik</h3>
                    <div class="param-checkbox">
                        <label>
                            <input type="checkbox" checked data-param="minyak">
                            <span>Ketinggian Minyak</span>
                        </label>
                    </div>
                    <div class="param-checkbox">
                        <label>
                            <input type="checkbox" checked data-param="air">
                            <span>Ketinggian Air</span>
                        </label>
                    </div>
                    <div class="param-checkbox">
                        <label>
                            <input type="checkbox" checked data-param="lumpur">
                            <span>Ketinggian Lumpur</span>
                        </label>
                    </div>
                    <div class="param-checkbox">
                        <label>
                            <input type="checkbox" data-param="tonase_minyak">
                            <span>Tonase Minyak</span>
                        </label>
                    </div>
                    <div class="param-checkbox">
                        <label>
                            <input type="checkbox" data-param="suhu">
                            <span>Suhu</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="content-area">
                <div class="data-cards">
                    <div class="data-card">
                        <h3>MINYAK</h3>
                        <div class="value" id="current-oil">1140.91</div>
                        <div class="unit">cm</div>
                        <div class="trend up"><i class="fas fa-arrow-up"></i> 2.3% dari sebelumnya</div>
                    </div>
                    <div class="data-card">
                        <h3>TONASE MINYAK</h3>
                        <div class="value" id="current-tonnage">3724802.06</div>
                        <div class="unit">kg</div>
                        <div class="trend up"><i class="fas fa-arrow-up"></i> 1.7% dari sebelumnya</div>
                    </div>
                    <div class="data-card">
                        <h3>AIR</h3>
                        <div class="value" id="current-water">0</div>
                        <div class="unit">cm</div>
                        <div class="trend">Stabil</div>
                    </div>
                    <div class="data-card">
                        <h3>LUMPUR</h3>
                        <div class="value" id="current-sludge">0</div>
                        <div class="unit">cm</div>
                        <div class="trend">Stabil</div>
                    </div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-header">
                        <div class="chart-title">Grafik Data Sensor</div>
                        <div class="chart-actions">
                            <button data-range="day">Hari Ini</button>
                            <button data-range="week">Minggu Ini</button>
                            <button data-range="month">Bulan Ini</button>
                        </div>
                    </div>
                    <div class="chart-canvas-container">
                        <canvas id="sensor-chart"></canvas>
                    </div>
                </div>
                
                <div class="data-table-container">
                    <h3><i class="fas fa-table"></i> Data Historis</h3>
                    <div class="table-responsive">
                        <table id="data-table">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Minyak (cm)</th>
                                    <th>Tonase (kg)</th>
                                    <th>Air (cm)</th>
                                    <th>Lumpur (cm)</th>
                                    <th>Suhu (°C)</th>
                                    <th>Baterai (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data dari halaman sebelumnya (diterima melalui POST)
        // Dalam implementasi nyata, data ini akan di-pass dari PHP
        const postData = {
            client_id: 'iot_thep',
            user_name: 'THEP ST02',
            client_secret: 'aabh9adfbvz872',
            chip_id: '2786842935',
            placement: 'THEP',
            custom_name: 'THEP ST01'
        };

        // Inisialisasi variabel global
        let sensorChart;
        let apiKey = '';
        let sensorData = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Set nilai dari data POST
            document.getElementById('custom-name').textContent = postData.custom_name;
            document.getElementById('chip-id').textContent = `Chip ID: ${postData.chip_id}`;
            document.getElementById('placement').textContent = `Lokasi: ${postData.placement}`;
            
            // Set tanggal default (7 hari terakhir)
            const sevenDaysAgo = new Date();
            sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
            document.getElementById('start-date').value = formatDate(sevenDaysAgo);
            
            // Inisialisasi grafik
            initChart();
            
            // Event listener untuk tombol muat data
            document.getElementById('load-data').addEventListener('click', function() {
                loadDataFromAPI();
            });
            
            // Event listener untuk checkbox parameter
            document.querySelectorAll('.param-checkbox input').forEach(checkbox => {
                checkbox.addEventListener('change', updateChart);
            });
            
            // Event listener untuk tombol range grafik
            document.querySelectorAll('.chart-actions button').forEach(button => {
                button.addEventListener('click', function() {
                    // Hapus class active dari semua tombol
                    document.querySelectorAll('.chart-actions button').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Tambah class active ke tombol yang diklik
                    this.classList.add('active');
                    
                    // Filter data berdasarkan range
                    filterDataByRange(this.dataset.range);
                });
            });
            
            // Muat data pertama kali
            authenticateAndLoadData();
        });

        // Format tanggal ke YYYY-MM-DD
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Inisialisasi grafik
        function initChart() {
            const ctx = document.getElementById('sensor-chart').getContext('2d');
            sensorChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Minyak (cm)',
                            data: [],
                            borderColor: 'rgb(46, 204, 113)',
                            backgroundColor: 'rgba(46, 204, 113, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Air (cm)',
                            data: [],
                            borderColor: 'rgb(52, 152, 219)',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Lumpur (cm)',
                            data: [],
                            borderColor: 'rgb(155, 89, 182)',
                            backgroundColor: 'rgba(155, 89, 182, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Tonase Minyak (kg)',
                            data: [],
                            borderColor: 'rgb(241, 196, 15)',
                            backgroundColor: 'rgba(241, 196, 15, 0.1)',
                            tension: 0.3,
                            fill: false,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Suhu (°C)',
                            data: [],
                            borderColor: 'rgb(231, 76, 60)',
                            backgroundColor: 'rgba(231, 76, 60, 0.1)',
                            tension: 0.3,
                            fill: false,
                            yAxisID: 'y2'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Waktu'
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Ketinggian (cm)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: false,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Tonase (kg)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        y2: {
                            type: 'linear',
                            display: false,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Suhu (°C)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Autentikasi dan muat data
        function authenticateAndLoadData() {
            // Tampilkan loading state
            document.querySelector('tbody').innerHTML = `
                <tr>
                    <td colspan="7" class="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Memuat data...</p>
                    </td>
                </tr>
            `;
            
            // Dalam implementasi nyata, ini akan melakukan request ke API
            // Simulasi request API autentikasi
            setTimeout(() => {
                // Response autentikasi simulasi
                const authResponse = {
                    username: postData.user_name,
                    client_secret: '5bc48cf2e4emdf76a5799472f24048eb',
                    appname: 'IOT',
                    versi: '1.0.0',
                    api_key: 'a92648e9eb404479ec84ee37512eb557',
                    datelogin: '2025-09-03 11:05:55',
                    explogin: '2025-09-04 11:05:55'
                };
                
                apiKey = authResponse.api_key;
                
                // Setelah autentikasi berhasil, muat data
                loadDataFromAPI();
            }, 1500);
        }

        // Memuat data dari API
        function loadDataFromAPI() {
            // Tampilkan loading state
            document.querySelector('tbody').innerHTML = `
                <tr>
                    <td colspan="7" class="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Memuat data...</p>
                    </td>
                </tr>
            `;
            
            const startDate = document.getElementById('start-date').value;
            
            // Dalam implementasi nyata, ini akan melakukan request ke API dengan fetch
            // Simulasi request API data
            setTimeout(() => {
                // Response data simulasi (disederhanakan)
                const apiResponse = {
                    status: 200,
                    error: false,
                    message: "",
                    content_type: "json",
                    result: [
                        {
                            chip_id: postData.chip_id,
                            baterai_bandul: "76",
                            recorded_at: "2025-09-01T10:29:28",
                            status: "success",
                            suhu: "40.8125",
                            mejaukur: "1359.39",
                            minyak: "1140.91",
                            air: "0",
                            lumpur: "0",
                            tinggi_meja: "0",
                            offset: "0",
                            tonase_minyak: 3724802.06
                        },
                        {
                            chip_id: postData.chip_id,
                            baterai_bandul: "74",
                            recorded_at: "2025-09-01T09:09:10",
                            status: "success",
                            suhu: "41.375",
                            mejaukur: "1359.39",
                            minyak: "1132.02",
                            air: "0",
                            lumpur: "0",
                            tinggi_meja: "0",
                            offset: "0",
                            tonase_minyak: 3724874.95
                        },
                        {
                            chip_id: postData.chip_id,
                            baterai_bandul: "75",
                            recorded_at: "2025-08-31T22:01:00",
                            status: "success",
                            suhu: "39.5",
                            mejaukur: "1359.39",
                            minyak: "1125.45",
                            air: "0",
                            lumpur: "0",
                            tinggi_meja: "0",
                            offset: "0",
                            tonase_minyak: 3724920.12
                        }
                    ]
                };
                
                sensorData = apiResponse.result;
                
                // Perbarui tampilan dengan data baru
                updateDisplay(sensorData);
                
                // Perbarui grafik
                updateChart();
                
                // Perbarui kartu data
                updateDataCards(sensorData);
            }, 2000);
        }

        // Memperbarui tampilan dengan data
        function updateDisplay(data) {
            const tableBody = document.querySelector('#data-table tbody');
            tableBody.innerHTML = '';
            
            data.sort((a, b) => new Date(b.recorded_at) - new Date(a.recorded_at));
            
            data.forEach(item => {
                const row = document.createElement('tr');
                
                // Format tanggal
                const recordedDate = new Date(item.recorded_at);
                const formattedDate = recordedDate.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                row.innerHTML = `
                    <td>${formattedDate}</td>
                    <td>${parseFloat(item.minyak).toLocaleString('id-ID')}</td>
                    <td>${parseFloat(item.tonase_minyak).toLocaleString('id-ID')}</td>
                    <td>${parseFloat(item.air).toLocaleString('id-ID')}</td>
                    <td>${parseFloat(item.lumpur).toLocaleString('id-ID')}</td>
                    <td>${parseFloat(item.suhu).toLocaleString('id-ID')}</td>
                    <td>${item.baterai_bandul}%</td>
                `;
                
                tableBody.appendChild(row);
            });
        }

        // Memperbarui grafik
        function updateChart() {
            if (sensorData.length === 0) return;
            
            // Urutkan data berdasarkan waktu (terlama ke terbaru)
            const sortedData = [...sensorData].sort((a, b) => 
                new Date(a.recorded_at) - new Date(b.recorded_at)
            );
            
            // Siapkan data untuk grafik
            const labels = sortedData.map(item => {
                const date = new Date(item.recorded_at);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            });
            
            // Dapatkan parameter yang dipilih
            const selectedParams = [];
            document.querySelectorAll('.param-checkbox input:checked').forEach(checkbox => {
                selectedParams.push(checkbox.dataset.param);
            });
            
            // Sembunyikan/tampilkan dataset berdasarkan pilihan
            sensorChart.data.datasets.forEach(dataset => {
                const param = dataset.label.split(' ')[0].toLowerCase();
                if (param === 'minyak') {
                    dataset.hidden = !selectedParams.includes('minyak');
                } else if (param === 'air') {
                    dataset.hidden = !selectedParams.includes('air');
                } else if (param === 'lumpur') {
                    dataset.hidden = !selectedParams.includes('lumpur');
                } else if (param === 'tonase') {
                    dataset.hidden = !selectedParams.includes('tonase_minyak');
                } else if (param === 'suhu') {
                    dataset.hidden = !selectedParams.includes('suhu');
                }
            });
            
            // Update data untuk setiap dataset
            sensorChart.data.labels = labels;
            sensorChart.data.datasets[0].data = sortedData.map(item => parseFloat(item.minyak));
            sensorChart.data.datasets[1].data = sortedData.map(item => parseFloat(item.air));
            sensorChart.data.datasets[2].data = sortedData.map(item => parseFloat(item.lumpur));
            sensorChart.data.datasets[3].data = sortedData.map(item => parseFloat(item.tonase_minyak));
            sensorChart.data.datasets[4].data = sortedData.map(item => parseFloat(item.suhu));
            
            sensorChart.update();
        }

        // Memperbarui kartu data dengan nilai terkini
        function updateDataCards(data) {
            if (data.length === 0) return;
            
            const latestData = data[0];
            const previousData = data.length > 1 ? data[1] : null;
            
            document.getElementById('current-oil').textContent = parseFloat(latestData.minyak).toLocaleString('id-ID');
            document.getElementById('current-tonnage').textContent = parseFloat(latestData.tonase_minyak).toLocaleString('id-ID');
            document.getElementById('current-water').textContent = parseFloat(latestData.air).toLocaleString('id-ID');
            document.getElementById('current-sludge').textContent = parseFloat(latestData.lumpur).toLocaleString('id-ID');
            
            // Update status baterai
            document.getElementById('battery-value').textContent = `${latestData.baterai_bandul}%`;
            const batteryStatus = document.getElementById('battery-status');
            if (parseInt(latestData.baterai_bandul) > 70) {
                batteryStatus.textContent = 'Baik';
                batteryStatus.style.color = '#2ecc71';
            } else if (parseInt(latestData.baterai_bandul) > 40) {
                batteryStatus.textContent = 'Cukup';
                batteryStatus.style.color = '#f39c12';
            } else {
                batteryStatus.textContent = 'Perlu Charge';
                batteryStatus.style.color = '#e74c3c';
            }
            
            // Update status suhu
            document.getElementById('temperature-value').textContent = `${parseFloat(latestData.suhu).toLocaleString('id-ID')}°C`;
            const tempStatus = document.getElementById('temperature-status');
            if (parseFloat(latestData.suhu) < 45) {
                tempStatus.textContent = 'Normal';
                tempStatus.style.color = '#2ecc71';
            } else {
                tempStatus.textContent = 'Tinggi';
                tempStatus.style.color = '#e74c3c';
            }
            
            // Update waktu terakhir
            const lastUpdate = new Date(latestData.recorded_at);
            const now = new Date();
            const diffMinutes = Math.floor((now - lastUpdate) / (1000 * 60));
            
            const lastUpdateEl = document.getElementById('last-update');
            if (diffMinutes < 5) {
                lastUpdateEl.textContent = 'Baru saja';
                document.getElementById('connection-status').textContent = 'Online';
                document.getElementById('connection-status').style.color = '#2ecc71';
            } else if (diffMinutes < 60) {
                lastUpdateEl.textContent = `${diffMinutes} menit lalu`;
                document.getElementById('connection-status').textContent = 'Online';
                document.getElementById('connection-status').style.color = '#2ecc71';
            } else {
                const diffHours = Math.floor(diffMinutes / 60);
                lastUpdateEl.textContent = `${diffHours} jam lalu`;
                document.getElementById('connection-status').textContent = 'Offline';
                document.getElementById('connection-status').style.color = '#e74c3c';
            }
        }

        // Filter data berdasarkan range waktu
        function filterDataByRange(range) {
            // Dalam implementasi nyata, ini akan memfilter data yang sudah dimuat
            // atau memuat ulang data dari API dengan parameter yang sesuai
            alert(`Filter by ${range} akan diimplementasi dengan pemanggilan API yang sesuai`);
        }
    </script>
</body>
</html>
