<?php
// Cek apakah request method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari POST
    $chipid = isset($_POST['chipid']) ? $_POST['chipid'] : null;
    $status_code = isset($_POST['status_code']) ? $_POST['status_code'] : null;
    $error_date = isset($_POST['error_date']) ? $_POST['error_date'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;

    // Validasi sederhana
    if (empty($chipid) || empty($status_code) || empty($error_date) || empty($description)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Semua field wajib diisi."
        ]);
        exit;
    }

    // Koneksi ke database
    $serverName = "182.23.67.40";
    $userNameDb = "localuser";
    $password = "0wL5quadD3f1n1tion";
    $dbName = "wbbgmpa";

    $conn = new mysqli($serverName, $userNameDb, $password, $dbName);

    // Periksa koneksi
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Koneksi database gagal: " . $conn->connect_error
        ]);
        exit;
    }

    // Query insert menggunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("INSERT INTO odsou_error (chipid, status_code, error_date, description) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Persiapan query gagal: " . $conn->error
        ]);
        $conn->close();
        exit;
    }

    // Bind parameter
    $stmt->bind_param("iiss", $chipid, $status_code, $error_date, $description);

    // Eksekusi
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Data berhasil disimpan."
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Gagal menyimpan data: " . $stmt->error
        ]);
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $conn->close();
} else {
    // Jika bukan POST
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Metode request tidak diizinkan."
    ]);
}
?>
