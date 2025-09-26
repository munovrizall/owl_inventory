<?php
//include "./connection.php";
header("Content-Type:application/json");

$resultPrep = [];

$input_json = file_get_contents("php://input");
$request_data = json_decode($input_json, true);
        // Establish database connection
        $serverName = "localhost";
        $userNameDb = "root";
        $password = "Sem4ng4tsukses!";
        $dbName = "devices";

        $conn = new mysqli($serverName, $userNameDb, $password, $dbName);        

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }        

        else{
            die ("Connection successful");
        }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($request_data['chip_id']) && isset($request_data['produk'])) {

        $type_produk = $request_data['type_produk'] ?? '';
        $produk = $request_data['produk'];
        $chip_id = $request_data['chip_id'];
        $nama_client = $request_data['nama_client'] ?? '';
        $garansi_awal = $request_data['garansi_awal'] ?? '';
        $garansi_akhir = $request_data['garansi_akhir'] ?? '';
        $garansi_void = $request_data['garansi_void'] ?? 0;
        $keterangan_void = $request_data['keterangan_void'] ?? '';
        $ip_address = $request_data['ip_address'] ?? '';
        $mac_wifi = $request_data['mac_wifi'] ?? '';
        $mac_bluetooth = $request_data['mac_bluetooth'] ?? '';
        $firmware_version = $request_data['firmware_version'] ?? '';
        $hardware_version = $request_data['hardware_version'] ?? '';
        $free_ram = $request_data['free_ram'] ?? 0;
        $min_ram = $request_data['min_ram'] ?? 0;
        $batt_low = $request_data['batt_low'] ?? 0;
        $batt_high = $request_data['batt_high'] ?? 0;
        $temperature = $request_data['temperature'] ?? 0;
        $status_error = $request_data['status_error'] ?? 0;
        $gps_latitude = $request_data['gps_latitude'] ?? 0;
        $gps_longitude = $request_data['gps_longitude'] ?? 0;
        $status_qc_sensor_1 = $request_data['status_qc_sensor_1'] ?? '';
        $status_qc_sensor_2 = $request_data['status_qc_sensor_2'] ?? '';
        $status_qc_sensor_3 = $request_data['status_qc_sensor_3'] ?? '';
        $status_qc_sensor_4 = $request_data['status_qc_sensor_4'] ?? '';
        $status_qc_sensor_5 = $request_data['status_qc_sensor_5'] ?? '';
        $status_qc_sensor_6 = $request_data['status_qc_sensor_6'] ?? '';
        $bat = $request_data['bat'] ?? 0;
        $pt = $request_data['pt'] ?? '';
        $unit = $request_data['unit'] ?? '';

        // Pastikan $chip_id dan $produk ada karena ini adalah cek awal
        if (!isset($chip_id) || !isset($produk)) {
             http_response_code(400);
             $resultPrep['status'] = [
                 "code" => 400,
                 "description" => 'Parameter chip_id or produk missing'
             ];
             echo json_encode($resultPrep);
             exit();
        }

        // Establish database connection
        $serverName = "localhost";
        $userNameDb = "root";
        $password = "Sem4ng4tsukses!";
        $dbName = "devices";

        $conn = new mysqli($serverName, $userNameDb, $password, $dbName);        

               // Check for database connection errors
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }        

        // Cek apakah kombinasi chip_id dan produk sudah ada
        $stmt = $conn->prepare("SELECT no_sn FROM inventaris_produk WHERE chip_id = ? AND produk = ?");
        $stmt->bind_param("is", $chip_id, $produk); // chip_id (int), produk (string)
        $stmt->execute();
        $result = $stmt->get_result();

        $no_sn = null; // Inisialisasi no_sn

        if ($result->num_rows > 0) {
            // Data sudah ada, ambil no_sn yang ada dan lakukan UPDATE
            $row = $result->fetch_assoc();
            $no_sn = $row['no_sn']; // SN sudah ada

            // SQL UPDATE untuk data yang sudah ada
            // Count of ? in SET clause = 29
            // Count of ? in WHERE clause = 2 (chip_id, produk)
            // Total ? = 31
            $sql_update_existing = "UPDATE inventaris_produk SET
                type_produk=?, produk=?, chip_id=?, no_sn=?,
                nama_client=?, garansi_awal=?, garansi_akhir=?, garansi_void=?, keterangan_void=?,
                ip_address=?, mac_wifi=?, mac_bluetooth=?,
                firmware_version=?, hardware_version=?,
                free_ram=?, min_ram=?, batt_low=?, batt_high=?,
                temperature=?, status_error=?, gps_latitude=?, gps_longitude=?,
                status_qc_sensor_1=?, status_qc_sensor_2=?, status_qc_sensor_3=?,
                status_qc_sensor_4=?, status_qc_sensor_5=?, status_qc_sensor_6=?,
                bat=?, pt=?, unit=?, last_online=NOW()
            WHERE chip_id = ? AND produk = ?"; // <<< Perhatikan WHERE clause ini

            $stmt_update = $conn->prepare($sql_update_existing);

            // Type definition string for 31 variables:
            // ssissssissssssiiidddssssssssisss (29 from SET) + is (2 from WHERE)
            // String (type_produk), String (produk), Int (chip_id), Int (no_sn),
            // String (nama_client), String (garansi_awal), String (garansi_akhir), Int (garansi_void), String (keterangan_void),
            // String (ip_address), String (mac_wifi), String (mac_bluetooth),
            // String (firmware_version), String (hardware_version),
            // Int (free_ram), Int (min_ram), Int (batt_low), Int (batt_high),
            // Double (temperature), Int (status_error), Double (gps_latitude), Double (gps_longitude),
            // String (status_qc_sensor_1 to 6) - 6 s's
            // Int (bat), String (pt), String (unit)
            // THEN Int (chip_id for WHERE), String (produk for WHERE)
            $stmt_update->bind_param("ssissssissssssiiidddssssssssissis", // Count this! It should be 31 chars
                $type_produk, $produk, $chip_id, $no_sn,
                $nama_client, $garansi_awal, $garansi_akhir, $garansi_void, $keterangan_void,
                $ip_address, $mac_wifi, $mac_bluetooth,
                $firmware_version, $hardware_version,
                $free_ram, $min_ram, $batt_low, $batt_high,
                $temperature, $status_error, $gps_latitude, $gps_longitude,
                $status_qc_sensor_1, $status_qc_sensor_2, $status_qc_sensor_3,
                $status_qc_sensor_4, $status_qc_sensor_5, $status_qc_sensor_6,
                $bat, $pt, $unit,
                $chip_id, $produk // <<< Variables for WHERE clause
            );

            if ($stmt_update->execute()) {
                http_response_code(200); // 200 OK untuk update
                $resultPrep['status'] = ["code" => 200, "description" => 'Data updated successfully'];
            } else {
                http_response_code(500);
                $resultPrep['status'] = ["code" => 500, "description" => 'Failed to update data: ' . $conn->error];
            }
            $stmt_update->close();

        } else {
            // Data belum ada, generate SN baru dan INSERT
            $currentJulianDate = date('z') + 1;
            $currentYear = date('y');
            $no_sn_prefix = $currentYear . sprintf('%03d', $currentJulianDate);
            $no_sn_like = $no_sn_prefix . '%';

            $stmt_check = $conn->prepare("SELECT no_sn FROM inventaris_produk WHERE no_sn LIKE ? ORDER BY no_sn DESC LIMIT 1");
            $stmt_check->bind_param("s", $no_sn_like);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                $row_check = $result_check->fetch_assoc();
                $last_no_sn = $row_check['no_sn'];
                $no_sn = intval($last_no_sn) + 1;
            } else {
                $no_sn = intval($no_sn_prefix . '001');
            }

            // SQL INSERT untuk data baru
            // Count of ? in VALUES clause = 30 (last_online is NOW())
            $sql_insert = "INSERT INTO inventaris_produk (
                type_produk, produk, chip_id, no_sn, 
                nama_client, garansi_awal, garansi_akhir, garansi_void, keterangan_void, 
                ip_address, mac_wifi, mac_bluetooth, 
                firmware_version, hardware_version, 
                free_ram, min_ram, batt_low, batt_high, 
                temperature, status_error, gps_latitude, gps_longitude, 
                status_qc_sensor_1, status_qc_sensor_2, status_qc_sensor_3, 
                status_qc_sensor_4, status_qc_sensor_5, status_qc_sensor_6, 
                bat, pt, unit, last_online
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
            )";
            $stmt_insert = $conn->prepare($sql_insert);

            // Type definition string for 30 variables:
            // ssissssissssssiiidddssssssssiss
            $stmt_insert->bind_param("ssissssissssssiiidddssssssssiss", // Count this! It should be 30 chars
                $type_produk, $produk, $chip_id, $no_sn,
                $nama_client, $garansi_awal, $garansi_akhir, $garansi_void, $keterangan_void,
                $ip_address, $mac_wifi, $mac_bluetooth,
                $firmware_version, $hardware_version,
                $free_ram, $min_ram, $batt_low, $batt_high,
                $temperature, $status_error, $gps_latitude, $gps_longitude,
                $status_qc_sensor_1, $status_qc_sensor_2, $status_qc_sensor_3,
                $status_qc_sensor_4, $status_qc_sensor_5, $status_qc_sensor_6,
                $bat, $pt, $unit
            );

            if ($stmt_insert->execute()) {
                http_response_code(201); // 201 Created untuk INSERT data baru
                $resultPrep['status'] = ["code" => 201, "description" => 'Data created successfully'];
            } else {
                http_response_code(500);
                $resultPrep['status'] = ["code" => 500, "description" => 'Failed to create data: ' . $conn->error];
            }
            $stmt_insert->close();
        }

        $resultPrep["no_sn"] = $no_sn;
        $resultPrep["chip_id"] = $chip_id;
        $resultPrep["produk"] = $produk;

    } else {
        http_response_code(400);
        $resultPrep['status'] = [
            "code" => 400,
            "description" => 'Parameter invalid: chip_id or produk are required.'
        ];
    }
} else {
    http_response_code(400);
    $resultPrep['status'] = [
        "code" => 400,
        "description" => 'Method invalid: Only POST requests are allowed.'
    ];
}

echo json_encode($resultPrep);
$conn->close();
?>
