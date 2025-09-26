<?php
include "../connection.php";
include "../admin_privilege.php";

header('Content-Type: application/json');

$query  = "SELECT stok_id, nama, kelompok, harga_bahan, link FROM masterbahan ORDER BY kelompok, nama"; 
$result = $conn->query($query );

$dropdownOptions = [];

while ($row = $result->fetch_assoc()) {
    $dropdownOptions[] = [
        'stok_id' => $row['stok_id'],
        'nama' => $row['nama'],
        'kelompok' => $row['kelompok'],
        'harga_bahan' => $row['harga_bahan'],
        'link' => $row['link'] ?? ''
    ];
}

echo json_encode($dropdownOptions);
?>