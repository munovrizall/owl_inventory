<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

//header("Content-Type:application/json");

$resultPrep = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // echo 'masuk post';
    if (isset($_POST['chip_id']) AND isset($_POST['produk'])) {
        
        echo 'Semua parameter ada';

        $type_produk = isset($_POST['type_produk']) ? $_POST['type_produk'] : '';
        $produk = $_POST['produk'];
        $chip_id = $_POST['chip_id'];
        $nama_client = isset($_POST['nama_client']) ? $_POST['nama_client'] : '';
        $garansi_awal = isset($_POST['garansi_awal']) ? $_POST['garansi_awal'] : '';
        $garansi_akhir = isset($_POST['garansi_akhir']) ? $_POST['garansi_akhir'] : '';
        $garansi_void = isset($_POST['garansi_void']) ? $_POST['garansi_void'] : '';
        $keterangan_void = isset($_POST['keterangan_void']) ? $_POST['keterangan_void'] : '';
        $ip_address = isset($_POST['ip_address']) ? $_POST['ip_address'] : '';
        $mac_wifi = isset($_POST['mac_wifi']) ? $_POST['mac_wifi'] : '';
        $mac_bluetooth = isset($_POST['mac_bluetooth']) ? $_POST['mac_bluetooth'] : '';
        $firmware_version = isset($_POST['firmware_version']) ? $_POST['firmware_version'] : '';
        $hardware_version = isset($_POST['hardware_version']) ? $_POST['hardware_version'] : '';
        $free_ram = isset($_POST['free_ram']) ? $_POST['free_ram'] : '';
        $min_ram = isset($_POST['min_ram']) ? $_POST['min_ram'] : '';
        $batt_low = isset($_POST['batt_low']) ? $_POST['batt_low'] : '';
        $batt_high = isset($_POST['batt_high']) ? $_POST['batt_high'] : '';
        $temperature = isset($_POST['temperature']) ? $_POST['temperature'] : '';
        $status_error = isset($_POST['status_error']) ? $_POST['status_error'] : '';
        $gps_latitude = isset($_POST['gps_latitude']) ? $_POST['gps_latitude'] : '';
        $gps_longitude = isset($_POST['gps_longitude']) ? $_POST['gps_longitude'] : '';
        $status_qc_sensor_1 = isset($_POST['status_qc_sensor_1']) ? $_POST['status_qc_sensor_1'] : '';
        $status_qc_sensor_2 = isset($_POST['status_qc_sensor_2']) ? $_POST['status_qc_sensor_2'] : '';
        $status_qc_sensor_3 = isset($_POST['status_qc_sensor_3']) ? $_POST['status_qc_sensor_3'] : '';
        $status_qc_sensor_4 = isset($_POST['status_qc_sensor_4']) ? $_POST['status_qc_sensor_4'] : '';
        $status_qc_sensor_5 = isset($_POST['status_qc_sensor_5']) ? $_POST['status_qc_sensor_5'] : '';
        $status_qc_sensor_6 = isset($_POST['status_qc_sensor_6']) ? $_POST['status_qc_sensor_6'] : '';
        

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

        // Prepare SQL statement to check if the combination exists
        $sql = "SELECT no_sn FROM inventaris_produk WHERE chip_id = ? AND produk = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $chip_id, $produk);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Combination exists, send the existing no_sn back to the hardware
            $row = $result->fetch_assoc();
            $no_sn = $row['no_sn'];
            $response = array("no_sn" => $no_sn);
        } else {
            // Combination doesn't exist, generate no_sn with template
            $currentJulianDate = date('z') + 1;
            $currentYear = date('y');
            $no_sn_prefix = $currentYear . sprintf('%03d', $currentJulianDate);
            $no_sn_like = $no_sn_prefix . '%';
            echo 'no sn like';
            echo $no_sn_like;
            echo 'no sn prefix';
            echo $no_sn_prefix;
            // Check if similar no_sn exists with different endings
            $sql_check = "SELECT no_sn FROM inventaris_produk WHERE no_sn LIKE ? ORDER BY no_sn DESC LIMIT 1";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $no_sn_like);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                echo 'similar found';
                // Similar no_sn found, increment the last one
                $row_check = $result_check->fetch_assoc();
                $last_no_sn = $row_check['no_sn'];
                $no_sn = intval($last_no_sn) + 1;
                echo $no_sn;
            } else {
                echo 'similar notfound new sn';
                // No similar no_sn found, use the first one in the series
                $no_sn = intval($no_sn_prefix . '001'); // Convert to integer
                echo $no_sn;
            }
            echo 'insert';
            // Insert new record with the generated no_sn
            $sql_insert = "INSERT INTO inventaris_produk (type_produk, produk, chip_id, no_sn, ip_address, mac_wifi, mac_bluetooth, firmware_version, 
            hardware_version, free_ram, min_ram, batt_low, batt_high, temperature, status_error, gps_latitude, gps_longitude, 
            status_qc_sensor_1, status_qc_sensor_2, status_qc_sensor_3, status_qc_sensor_4, status_qc_sensor_5, status_qc_sensor_6)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            if (!$stmt_insert) {
                die('Error in preparing statement: ' . $conn->error);
            }
            $stmt_insert->bind_param("ssiisssssiiiididdssssss",$type_produk, $produk, $chip_id, $no_sn, $ip_address, $mac_wifi, $mac_bluetooth, 
                $firmware_version, $hardware_version, $free_ram, $min_ram, $batt_low, $batt_high, 
                $temperature, $status_error, $gps_latitude, $gps_longitude, $status_qc_sensor_1, 
                $status_qc_sensor_2, $status_qc_sensor_3, $status_qc_sensor_4, $status_qc_sensor_5, $status_qc_sensor_6);
            // Execute the insertion statement
            
            if ($stmt_insert->execute()) {
                $response = array("no_sn" => $no_sn);
                echo 'sukses insert';
                echo json_encode($response);
            } else {
                // Handle insertion failure
                $response = array("error" => "Failed to insert data");
                echo 'sukses insert';
                echo json_encode($response);
            }
            echo 'after insert';
            
        }
        $resultPrep['results'] = [
            "no_sn" => $no_sn,
            "chip_id" => $chip_id,
            "produk"=> $produk,
        ];
    } 
    else {
        $resultPrep['status'] = [
            "code" => 400,
            "description" => 'Parameter invalid'
        ];    
    }
} else {
    $resultPrep['status'] = [
        "code" => 400,
        "description" => 'Method invalid'
    ];
}

echo json_encode($resultPrep);