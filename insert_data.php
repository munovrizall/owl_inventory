<?php
$serverName = "localhost";
$userName = "root";
$password = "";
$dbName = "databaseinventory";

// Create connection
$conn = new mysqli($serverName, $userName, $password, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle the insertion based on table and parameters
function insertData($conn, $table, $params) {
    $columns = implode(', ', array_keys($params));
    $values = "'" . implode("', '", $params) . "'";

    $sql = "INSERT INTO $table ($columns) VALUES ($values)";

    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Function to handle updating stokbahan quantity and historis
function updateStokBahan($conn, $stokId, $quantity, $pengguna, $activity) {
    $operation = ($activity === 'restock') ? '+' : '-';

    // Get the current quantity
    $sqlGetQuantity = "SELECT quantity FROM stokbahan WHERE stok_id = ?";
    
    // Use prepared statement
    $stmtGetQuantity = $conn->prepare($sqlGetQuantity);
    $stmtGetQuantity->bind_param('i', $stokId);
    $stmtGetQuantity->execute();
    $result = $stmtGetQuantity->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentQuantity = $row['quantity'];

        // Check if there is sufficient material
        if ($operation === '-' && $quantity > $currentQuantity) {
            echo "Bahan tidak mencukupi";
            return;
        }
    } else {
        echo "Error retrieving quantity: " . $conn->error;
        return;
    }

    // Update stokbahan quantity
    $sqlUpdateStokBahan = "UPDATE stokbahan SET quantity = quantity $operation ? WHERE stok_id = ?";
    
    // Use prepared statement
    $stmtUpdateStokBahan = $conn->prepare($sqlUpdateStokBahan);
    $stmtUpdateStokBahan->bind_param('ii', $quantity, $stokId);
    $stmtUpdateStokBahan->execute();

    if ($stmtUpdateStokBahan->affected_rows > 0) {
        // Get the updated quantity
        $sqlGetUpdatedQuantity = "SELECT quantity FROM stokbahan WHERE stok_id = ?";
        
        // Use prepared statement
        $stmtGetUpdatedQuantity = $conn->prepare($sqlGetUpdatedQuantity);
        $stmtGetUpdatedQuantity->bind_param('i', $stokId);
        $stmtGetUpdatedQuantity->execute();
        $result = $stmtGetUpdatedQuantity->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $updatedQuantity = $row['quantity'];

            // Calculate the difference and insert into historis
            $difference = $updatedQuantity - $currentQuantity;

            $timestamp = date("Y-m-d H:i:s");
            $historisParams = [
                'stok_id' => $stokId,
                'pengguna' => $pengguna,
                'waktu' => $timestamp,
                'quantity' => $difference,
                'activity' => $activity,
            ];

            insertData($conn, 'historis', $historisParams);
            echo "Stokbahan quantity updated successfully!";
        } else {
            echo "Error retrieving updated quantity: " . $conn->error;
        }
    } else {
        echo "Error updating stokbahan quantity: " . $stmtUpdateStokBahan->error;
    }

    // Close prepared statements
    $stmtGetQuantity->close();
    $stmtUpdateStokBahan->close();
    $stmtGetUpdatedQuantity->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity = $_POST["activity"];

    if ($activity === 'insert_masterbahan') {
        // Handle insert data masterbahan
        $kode = $_POST["kode"];
        $marking = $_POST["marking"];
        $ukuran = $_POST["ukuran"];
        
        $masterbahanParams = [
            'kode' => $kode,
            'marking' => $marking,
            'ukuran' => $ukuran,
        ];
        insertData($conn, 'masterbahan', $masterbahanParams);
        
    } elseif ($activity === 'insert_produksi') {
        // Handle insert data produksi
        $produk = $_POST["produk"];
        $stokId = $_POST["stok_id"]; // APAKAH PERLU?
        $quantity = $_POST["quantity"];
        $produksiParams = [
            'produk' => $produk,
            'stok_id' => $stokId,
            'quantity' => $quantity,
        ];
        insertData($conn, 'produksi', $produksiParams);

    } elseif ($activity === 'restock' || $activity === 'prototype' || $activity === 'produksi') {
        $stokId = $_POST["stok_id"]; // APAKAH PERLU?
        $quantity = $_POST["quantity"];
        $pengguna = $_POST["pengguna"];  // Harusnya nama user pas awal masuk, gatau gmn
        updateStokBahan($conn, $stokId, $quantity, $pengguna, $activity);
    } else {
        echo "Invalid activity!";
    }
}   

$conn->close();

echo "koneksi berhasil";
?>
