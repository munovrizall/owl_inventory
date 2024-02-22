<?php
include "../connection.php";
include "../admin_privilege.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $userId = $_GET["id"];

    // Periksa apakah pengguna dengan ID tertentu ada di database
    $checkUserQuery = "SELECT * FROM user_account WHERE account_id = ?";
    $stmtCheckUser = $conn->prepare($checkUserQuery);
    $stmtCheckUser->bind_param("i", $userId);
    $stmtCheckUser->execute();
    $resultCheckUser = $stmtCheckUser->get_result();

    if ($resultCheckUser->num_rows > 0) {
        // Data pengguna ditemukan, lakukan penghapusan
        $deleteUserQuery = "DELETE FROM user_account WHERE account_id = ?";
        $stmtDeleteUser = $conn->prepare($deleteUserQuery);
        $stmtDeleteUser->bind_param("i", $userId);
        $stmtDeleteUser->execute();
        $stmtDeleteUser->close();

        // Redirect ke halaman list setelah penghapusan
        header("Location: list.php");
        exit();
    } else {
        // Data pengguna tidak ditemukan, mungkin ID tidak valid
        echo "Data pengguna tidak ditemukan.";
    }

    $stmtCheckUser->close();
} else {
    // Jika tidak ada ID yang diterima atau request bukan GET, tampilkan pesan kesalahan
    echo "ID pengguna tidak valid atau request tidak sesuai.";
}
?>
