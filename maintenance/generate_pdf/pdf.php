<?php
include ("../../connection.php");
require ("./fpdf/fpdf.php");

$bulanIndonesia = array(
    "January" => "Januari",
    "February" => "Februari",
    "March" => "Maret",
    "April" => "April",
    "May" => "Mei",
    "June" => "Juni",
    "July" => "Juli",
    "August" => "Agustus",
    "September" => "September",
    "October" => "Oktober",
    "November" => "November",
    "December" => "Desember"
);

if (isset($_GET['id'])) {
    $transaksi_id = $_GET['id'];

    // Selecting data from detail_maintenance table based on transaksi_id
    $query = "SELECT produk_mt, no_sn, garansi, keterangan FROM detail_maintenance WHERE transaksi_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $transaksi_id);
    $stmt->execute();

    $result = $stmt->get_result();

    // Fetching data from the transaksi_maintenance table
    $transaksiQuery = "SELECT nama_client, tanggal_terima, last_edit FROM transaksi_maintenance WHERE transaksi_id = ?";
    $transaksiStmt = $conn->prepare($transaksiQuery);
    $transaksiStmt->bind_param("i", $transaksi_id);
    $transaksiStmt->execute();

    $transaksiResult = $transaksiStmt->get_result();

    // Fetch data from the transaksi_maintenance table
    if ($transaksiRow = $transaksiResult->fetch_assoc()) {
        $nama_client = $transaksiRow['nama_client'];
        $last_edit = $transaksiRow['last_edit'];

        // Continue with fetching data from the user_account table based on username
        $userQuery = "SELECT nama_lengkap FROM user_account WHERE username = ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("s", $last_edit);
        $userStmt->execute();

        $userResult = $userStmt->get_result();
        $userRow = $userResult->fetch_assoc();
        $namaLengkap = $userRow["nama_lengkap"];

        // Fetching other necessary data
        // Fetching date components
        $tanggal_terima = $transaksiRow['tanggal_terima'];
        $currentYear = date("Y", strtotime($tanggal_terima));
        $currentMonth = date("F", strtotime($tanggal_terima));
        $currentMonthIndonesia = $bulanIndonesia[$currentMonth];
        $currentDate = date("j", strtotime($tanggal_terima));

        // Continue with fetching data from the client table based on nama_client
        $clientQuery = "SELECT * FROM client WHERE nama_client = ?";
        $clientStmt = $conn->prepare($clientQuery);
        $clientStmt->bind_param("s", $nama_client);
        $clientStmt->execute();

        $clientResult = $clientStmt->get_result();
        $clientRow = $clientResult->fetch_assoc();
        $alamat_perusahaan = $clientRow["alamat_perusahaan"];
        $nama_korespondensi = $clientRow["nama_korespondensi"];
        
    } else {
        echo "Nama client not found.";
    }
} else {
    echo "ID not provided.";
}

// Fetching the image link from the database
$tanda_tangan_query = "SELECT tanda_tangan FROM user_account WHERE nama_lengkap = ?";
$tanda_tangan_stmt = $conn->prepare($tanda_tangan_query);
$tanda_tangan_stmt->bind_param("s", $namaLengkap);
$tanda_tangan_stmt->execute();
$tanda_tangan_result = $tanda_tangan_stmt->get_result();

if ($tanda_tangan_row = $tanda_tangan_result->fetch_assoc()) {
    $tanda_tangan_link = $tanda_tangan_row['tanda_tangan'];
} else {
    $tanda_tangan_link = '';
}

class PDF extends FPDF
{
    function WrapAndPrintAddress($alamat_perusahaan)
    {
        $lines = explode("\n", wordwrap($alamat_perusahaan, 80, "\n"));

        foreach ($lines as $index => $line) {
            if ($index === 0) {
                $this->MultiCell(0, 10, "Alamat       : $line", 0, 1);
            } else {
                $this->Cell(22, 10); // 5 tabs
                $this->MultiCell(0, 10, $line, 0, 1);
            }
        }
    }

    function AddSignatureImage($tanda_tangan_link)
    {
        if (!empty($tanda_tangan_link)) {
            // Set the position for the signature image
            $this->SetXY(25, $this->GetY());
            // Add the signature image to the PDF
            $this->Image($tanda_tangan_link, $this->GetX(), $this->GetY(), 30, 30);
        }
    }

    function WrapAndPrintRow($row)
    {
        $this->Cell(10, 10, $row['counter'], 1, 0, 'C');
        $this->Cell(45, 10, $row['produk_mt'], 1, 0);
        $this->Cell(35, 10, $row['no_sn'], 1, 0, 'C');
        $garansiText = ($row['garansi'] == 1) ? "Ya" : "Tidak";
        $this->Cell(15, 10, $garansiText, 1, 0, 'C');

        $this->MultiCell(60, 10, $row['keterangan'], 1, 'L');
    }
}

$pdf = new PDF("P","mm","A4");

$pdf->AddPage();
$pdf->SetFont("Times","",12);
$pdf->SetMargins(20,0,20);

//Cell(Width, Height, Text, Border, End Line, Align)

$pdf->Cell(0,10,"$transaksi_id/RnD/$currentYear",0,1,"R");

//Jarak
$pdf->Cell(0,20,"",0,1,);

$pdf->SetFont("Times","B",12);
$pdf->Cell(0,10,"BERITA ACARA SERAH TERIMA BARANG",0,1,"C");

$pdf->SetFont("Times","",12);
$pdf->Cell(0,10,"Kami yang bertanda tangan di bawah ini, pada tanggal $currentDate, bulan $currentMonthIndonesia, tahun $currentYear",0,1);
$pdf->Cell(0,10,"Nama         : $namaLengkap",0,1);
$pdf->MultiCell(0, 10, "Alamat       : Komplek Golden Plaza Fatmawati (Lottemart Fatmawati) Blok E No. 12A, Jl. R.S.
                     Fatmawati No. 15 Kel. Gandaria, Kec. Cilandak, Jakarta Selatan", 0, 1);
$pdf->Cell(0,10,"Selanjutnya disebut PIHAK PERTAMA",0,1);
$pdf->Cell(0,10,"Nama         : $nama_korespondensi",0,1);
$pdf->WrapAndPrintAddress($alamat_perusahaan);

$pdf->Cell(0,10,"Selanjutnya disebut PIHAK KEDUA",0,1);

$pdf->Cell(0,10,"",0,1,);

$pdf->MultiCell(0,5,"PIHAK PERTAMA menyerahkan barang kepada PIHAK KEDUA, dan PIHAK KEDUA menyatakan telah menerima barang dari PIHAK PERTAMA berupa daftar terlampir:",0,1);

//TABEL PRODUK dan Jarak
$pdf->Cell(0,5,"",0,1,);

$pdf->Cell(10, 5, "No", 1, 0, 'C');
$pdf->Cell(45, 5, "Nama Barang", 1, 0, 'C');
$pdf->Cell(35, 5, "SN", 1, 0, 'C');
$pdf->Cell(15, 5, "Garansi", 1, 0, 'C');
$pdf->Cell(60, 5, "Kerusakan", 1, 0, 'C');
$pdf->Ln();

$counter = 1;

while ($row = $result->fetch_assoc()) {
    $cellWidth = 60; // wrapped cell width
    $cellHeight = 5; // normal one-line cell height

    // check whether the text is overflowing
    if ($pdf->GetStringWidth($row['keterangan']) < $cellWidth) {
        // if not, then do nothing
        $line = 1;
    } else {
        // if it is, then calculate the height needed for wrapped cell
        // by splitting the text to fit the cell width
        // then count how many lines are needed for the text to fit the cell

        $textLength = strlen($row['keterangan']); // total text length
        $errMargin = 10; // cell width error margin, just in case
        $startChar = 0; // character start position for each line
        $maxChar = 0; // maximum character in a line, to be incremented later
        $textArray = array(); // to hold the strings for each line
        $tmpString = ""; // to hold the string for a line (temporary)

        while ($startChar < $textLength) { // loop until end of text
            // loop until the maximum character is reached
            while (
                $pdf->GetStringWidth($tmpString) < ($cellWidth - $errMargin) &&
                ($startChar + $maxChar) < $textLength
            ) {
                $maxChar++;
                $tmpString = substr($row['keterangan'], $startChar, $maxChar);
            }
            // move startChar to the next line
            $startChar = $startChar + $maxChar;
            // then add it into the array so we know how many lines are needed
            array_push($textArray, $tmpString);
            // reset maxChar and tmpString
            $maxChar = 0;
            $tmpString = '';
        }
        // get the number of lines
        $line = count($textArray);
    }

    // write the cells
    $pdf->Cell(10, ($line * $cellHeight), $counter++, 1, 0, 'C'); // increment and display the counter, adapt height to the number of lines
    $pdf->Cell(45, ($line * $cellHeight), $row['produk_mt'], 1, 0); // adapt height to the number of lines
    $pdf->Cell(35, ($line * $cellHeight), $row['no_sn'], 1, 0); // adapt height to the number of lines
    $garansiText = ($row['garansi'] == 1) ? "Ya" : "Tidak";
    $pdf->Cell(15, ($line * $cellHeight), $garansiText, 1, 0, 'C');
    $pdf->MultiCell($cellWidth, $cellHeight, $row['keterangan'], 1);
}

$pdf->Cell(0,10,"",0,1,);

$pdf->MultiCell(0,5,"Demikian berita acara serah terima barang ini dibuat oleh kedua belah pihak, adapun barang-barang tersebut dalam keadaan baik dan lengkap, sejak penandatanganan berita acara ini, maka barang tersebut menjadi tanggung jawab PIHAK KEDUA.",0,1);

$pdf->Cell(0,10,"Yang Menyerahkan :",0,0, 'L');
$pdf->Cell(0,10,"Yang Menerima :",0,1, 'R');

//Beri Underline
$pdf->SetFont("Times","U",12);
//Beri Jarak TTD
$pdf->AddSignatureImage($tanda_tangan_link);
$pdf->Cell(0,30,"",0,1,);

$pdf->Cell(0,10,"$namaLengkap",0,0, 'L');
$pdf->Cell(0,10,"$nama_korespondensi",0,1, 'R');


$pdf->Output('I',"Berita Acara $nama_client.pdf");