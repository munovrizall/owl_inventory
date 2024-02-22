<?php
// Include your database connection file
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
$serverName = "localhost";
$userNameDb = "root";
$password = "Sem4ng4tsukses!";
$dbName = "devices";

$conn = new mysqli($serverName, $userNameDb, $password, $dbName);

// Check if all required parameters are provided
if(isset($_POST['sn'], $_POST['produk'], $_POST['firmware'], $_POST['bat'], $_POST['temperature'], $_POST['hardware'])) {
    $sn = $_POST['sn'];
    $produk = $_POST['produk'];
    $firmware = $_POST['firmware'];
    $bat = $_POST['bat'];
    $temperature = $_POST['temperature'];
    $hardware = $_POST['hardware'];
    $ip_address = $_POST['ip_address'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    // Check the validity of the parameters (you should replace this with your validation logic)
    if(validateParameters($sn, $produk, $firmware, $bat, $temperature, $hardware)) {
        // Update the table inventaris_produk with new parameters
        updateInventarisProduk($conn, $sn, $produk, $firmware, $bat, $temperature, $hardware, $ip_address, );

        // Check if the combination of parameters exists in firmware_setup
        if(checkFirmwareSetup($conn, $produk, $hardware)) {
            // Select from firmware_setup where produk, hardware as posted parameter AND flag_active = 1
            $selectedFirmware = selectActiveFirmware($conn, $produk, $hardware);

            // Check if posted firmware is different from selected values
            if($firmware != $selectedFirmware['firmware']) {
                $file_path = $selectedFirmware['path'];
                $file_name = basename($file_path);

                // Set headers for force download
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"$file_name\"");
                header("Content-Length: " . filesize($file_path));
                readfile($file_path);
                exit;
            } else {
                http_response_code(409); // Conflict
                echo "Posted firmware is the same as the selected active firmware. Cannot download the file.";
            }
        } else {
            http_response_code(404); // Not Found
            echo "Combination of parameters does not exist in firmware_setup.";
        }
    } else {
        http_response_code(400); // Bad Request
        echo "Invalid parameters!";
    }
} else {
    http_response_code(400); // Bad Request
    echo "Required parameters not provided!";
}

// Function to validate the parameters (replace this with your validation logic)
function validateParameters($sn, $produk, $firmware, $bat, $temperature, $hardware) {
    // Add your validation logic here
    // For example, you might check the parameters against a database or some predefined criteria.
    // Return true if valid, false otherwise.
    return true; // Replace with your actual validation logic
}

// Function to update the table inventaris_produk with new parameters
function updateInventarisProduk($conn, $sn, $produk, $firmware, $bat, $temperature, $hardware,$ip_address) {
    // Add your database update logic here
    // Make sure to use proper database connection and sanitize input to prevent SQL injection
    $currentDateTime = date('Y-m-d H:i:s');
    // Replace the placeholders and update the table
    $sql = "UPDATE inventaris_produk SET produk = '$produk', firmware_version = '$firmware', bat = '$bat', ip_address = '$ip_address', temperature = '$temperature', hardware_version = '$hardware', last_online = '$currentDateTime' WHERE no_sn = '$sn'";
    // Execute the update query using your database connection
    $conn->query($sql);
}

// Function to check if the combination of parameters exists in firmware_setup
function checkFirmwareSetup($conn, $produk, $hardware) {
    // Add your database check logic here
    // Make sure to use proper database connection and sanitize input to prevent SQL injection
    // Return true if the combination exists, false otherwise.
    $sql = "SELECT COUNT(*) as count FROM firmware_setup WHERE produk = '$produk' AND hardware = '$hardware'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return ($row['count'] > 0);
}

// Function to select the active firmware from firmware_setup where produk, hardware AND flag_active = 1
function selectActiveFirmware($conn, $produk, $hardware) {
    // Add your database select logic here
    // Make sure to use proper database connection and sanitize input to prevent SQL injection
    // Return the selected row as an associative array.
    $sql = "SELECT * FROM firmware_setup WHERE produk = '$produk' AND hardware = '$hardware' AND flag_active = 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}
?>
