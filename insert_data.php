<?php
$serverName = "localhost";
$userName = "root";
$password = "";
$dbName = "databaseinventory";

$conn = new mysqli($serverName, $userName, $password, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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

function updateStokBahan($conn, $stokId, $quantity, $pengguna, $activity) {
    $operation = ($activity === 'Restock') ? '+' : '-';

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

function updateDampakProduksi($conn, $stokId, $quantity, $pengguna) {
    // Get the current stokbahan quantity
    $sqlGetStokBahanQuantity = "SELECT quantity FROM stokbahan WHERE stok_id = ?";
    
    // Use prepared statement
    $stmtGetStokBahanQuantity = $conn->prepare($sqlGetStokBahanQuantity);
    $stmtGetStokBahanQuantity->bind_param('i', $stokId);
    $stmtGetStokBahanQuantity->execute();
    $result = $stmtGetStokBahanQuantity->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentQuantity = $row['quantity'];

        // Check if there is sufficient material
        if ($quantity > $currentQuantity) {
            echo "Bahan tidak mencukupi";
            return;
        }
    } else {
        echo "Error retrieving stokbahan quantity: " . $conn->error;
        return;
    }

    // Update stokbahan quantity
    $sqlUpdateStokBahan = "UPDATE stokbahan SET quantity = quantity - ? WHERE stok_id = ?";
    
    // Use prepared statement
    $stmtUpdateStokBahan = $conn->prepare($sqlUpdateStokBahan);
    $stmtUpdateStokBahan->bind_param('ii', $quantity, $stokId);
    $stmtUpdateStokBahan->execute();

    if ($stmtUpdateStokBahan->affected_rows > 0) {
        // Insert into historis
        $timestamp = date("Y-m-d H:i:s");
        $historisParams = [
            'stok_id' => $stokId,
            'pengguna' => $pengguna,  // Replace with actual user information
            'waktu' => $timestamp,
            'quantity' => -$quantity,
            'activity' => 'Produksi',
        ];

        insertData($conn, 'historis', $historisParams);
        echo "Stokbahan quantity updated successfully!";
    } else {
        echo "Error updating stokbahan quantity: " . $stmtUpdateStokBahan->error;
    }

    // Close prepared statements
    $stmtGetStokBahanQuantity->close();
    $stmtUpdateStokBahan->close();
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
        insertData($conn, 'Produksi', $produksiParams);
    } elseif ($activity === 'Produksi') {
        $produk = $_POST["produk"];
        $pengguna = $_POST["pengguna"];
        
        // Fetch stok_id and quantity from produksi table
        $sqlProduksi = "SELECT stok_id, SUM(quantity) AS total_quantity FROM produksi WHERE produk = ? GROUP BY stok_id";
        $stmtProduksi = $conn->prepare($sqlProduksi);
        $stmtProduksi->bind_param('s', $produk);
        $stmtProduksi->execute();
        $resultProduksi = $stmtProduksi->get_result();

        if ($resultProduksi->num_rows > 0) {
            while ($rowProduksi = $resultProduksi->fetch_assoc()) {
                $stokId = $rowProduksi['stok_id'];
                $quantity = $rowProduksi['total_quantity'];

                // Update stokbahan quantity
                updateDampakProduksi($conn, $stokId, $quantity, $pengguna);
            }
        } else {
            echo "Produksi not found!";
        }

        $stmtProduksi->close();
    } elseif ($activity === 'Restock' || $activity === 'Maintenance') {
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
