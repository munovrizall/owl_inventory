<?php 

//header("Content-Type:application/json");

//$method = ;

$resultPrep = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['chip_id']) AND isset($_POST['produk'])) {
        
        echo 'Semua parameter ada';

        $chip_id = $_POST['chip_id'];
        $produk = $_POST['produk'];

        $resultPrep['status'] = [
            "code" => 200,
            "description" => 'Request valid'
        ];
        
        include "../connection.php";
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
            
            // Check if similar no_sn exists with different endings
            $sql_check = "SELECT no_sn FROM inventaris_produk WHERE no_sn LIKE ? ORDER BY no_sn DESC LIMIT 1";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $no_sn_like);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                // Similar no_sn found, increment the last one
                $row_check = $result_check->fetch_assoc();
                $last_no_sn = $row_check['no_sn'];
                $no_sn = intval($last_no_sn) + 1;
            } else {
                // No similar no_sn found, use the first one in the series
                $no_sn = intval($no_sn_prefix . '001'); // Convert to integer
            }
            
            // Insert new record with the generated no_sn
            $sql_insert = "INSERT INTO inventaris_produk (chip_id, produk, no_sn) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isi", $chip_id, $produk, $no_sn);
            
            // Execute the insertion statement
            if ($stmt_insert->execute()) {
                $response = array("no_sn" => $no_sn);
            } else {
                // Handle insertion failure
                $response = array("error" => "Failed to insert data");
            }
        }
        $resultPrep['results'] = [
            "no_sn" => $no_sn,
            "chip_id" => $chip_id,
            "produk"=> $produk,
        ];
        
    } else {
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