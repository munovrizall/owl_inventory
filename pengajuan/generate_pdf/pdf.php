<?php
include ("../../connection.php");

require ("pdf_mc_table.php");

if (isset($_GET['id'])) {
    $pengajuan_id = $_GET['id'];

    $query = "SELECT judul_pengajuan, unit, no_surat, batch, keterangan, waktu, grand_total, pengaju, penyetuju FROM pengajuan WHERE pengajuan_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pengajuan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $pengajuan = $result->fetch_assoc();

    $queryDetail = "SELECT nama_barang, kuantitas, harga, total_harga, keterangan FROM pengajuan_detail WHERE pengajuan_id = ?";
    $stmtDetail = $conn->prepare($queryDetail);
    $stmtDetail->bind_param("i", $pengajuan_id);
    $stmtDetail->execute();
    $resultDetail = $stmtDetail->get_result();

    $querySignature = "SELECT tanda_tangan FROM user_account WHERE nama_lengkap = ?";
    $stmtSignature = $conn->prepare($querySignature);
    $stmtSignature->bind_param("s", $pengajuan["pengaju"]);
    $stmtSignature->execute();
    $resultSignature = $stmtSignature->get_result();

    $SignatureUrl = $resultSignature->fetch_assoc();

    $pdf = new PDF_MC_Table("P","mm","A4");

    $pdf->SetMargins(15,15,15,15);
    $pdf->AddPage();

    //Cell(Width, Height, Text, Border, End Line, Align)
    //Multicell(Width, Height, Text, Border, Align)

    $Height = 6;
    $WidthJudul = 125;

    $pdf->SetFont("Times","B",12);
    $x = ($pdf->GetPageWidth() - $WidthJudul) / 2;
    $pdf->SetX($x);
    $judulNew="Permintaan Pembelian\n".$pengajuan['judul_pengajuan'];
    $pdf->Multicell($WidthJudul,$Height,strtoupper($judulNew),0,"C");
    $pdf->Ln(5);

    $pdf->SetFont("Times","",12);
    
    $pdf->Cell(130,$Height, "PT ORIGIN WIRACIPTA LESTARI",0,0,"L");
    $pdf->Cell(50,$Height, "Kepada",0,1,"L");

    $pdf->Cell(25,$Height, "Unit",0,0,"L");
    $pdf->Cell(105,$Height, ": {$pengajuan['unit']}",0,1,"L");

    $pdf->Cell(25,$Height, "No",0,0,"L");
    $pdf->Cell(105,$Height, ": {$pengajuan['no_surat']}",0,0,"L");
    $pdf->Cell(50,$Height, "Bagian Pengadaan",0,1,"L");

    $tanggalPengajuan = date('d-m-Y', strtotime($pengajuan['waktu']));
    $pdf->Cell(25,$Height, "Batch",0,0,"L");
    $pdf->Cell(105,$Height, ": ",0,0,"L");
    $pdf->Cell(50,$Height, "Tanggal : {$tanggalPengajuan}",0,1,"L");

    $pdf->Cell(25,$Height, "Keterangan",0,0,"L");
    $pdf->Cell(2,$Height, ":",0,0,"L");
    $pdf->Multicell(153,$Height, $pengajuan['keterangan'],0,"L");

    // Tabel Detail
    $pdf->Ln(3);

    $col1 = 10;
    $col2 = 70;
    $col3 = 15;
    $col4 = 20;
    $col5 = 25;
    $col6 = 40;
    $colheight = 6;

    // Header Tabel
    $pdf->SetFont("Times","B",12);
    $pdf->Cell($col1, $colheight, "No", 1, 0, "C");
    $pdf->Cell($col2, $colheight, "Nama", 1, 0, "C");
    $pdf->Cell($col3, $colheight, "Qty", 1, 0, "C");
    $pdf->Cell($col4, $colheight, "Harga", 1, 0, "C");
    $pdf->Cell($col5, $colheight, "Total", 1, 0, "C");
    $pdf->Cell($col6, $colheight, "Keterangan", 1, 1, "C");

    // Isi Tabel
    $pdf->SetFont("Times","",12);

    // pdf_mc_table

    // Set width masing masing kolom
    $pdf->SetWidths(Array($col1, $col2, $col3, $col4, $col5, $col6));

    // Set height per baris line (bukan table row)
    $pdf->SetLineHeight($Height);

    // Set align masing masing kolom
    $pdf->SetAligns(Array("R", "L", "C", "R", "R", "L"));

    $no = 1;
    $grandTotal = 0;

    while ($row = $resultDetail->fetch_assoc()) {
        $hargaFormatted = number_format($row['harga'], 0, ',', '.');
        $totalHarga = $row['kuantitas']*$row['harga'];
        $totalHargaFormatted = number_format($totalHarga, 0, ',', '.');

        $pdf->Row(Array(
            $no,
            $row['nama_barang'],
            $row['kuantitas'],
            $hargaFormatted,
            $totalHargaFormatted,
            $row['keterangan']
        ));

        $no++;
        $grandTotal += $totalHarga;
    }
    $grandTotal = number_format($grandTotal, 0, ',', '.');

    $colTotalTeks = $col1 + $col2 + $col3 + $col4;
    $colGrandTotal = $col5 + $col6;

    $pdf->SetFont("Times","B",11);
    $pdf->Cell($colTotalTeks, $colheight, "Total", 1, 0, "C");

    $pdf->Cell($col5, $colheight, $grandTotal, 1, 0, "R");
    $pdf->Cell($col6, $colheight, "", 1, 1);

    $pdf->Ln(6);

    $pdf->SetFont("Times","",12);
    $pdf->Cell(90, $Height, "Diajukan Oleh", 0, 0, "C");
    $pdf->Cell(90, $Height, "Disetujui Oleh", 0, 1, "C");

    if (isset($SignatureUrl['tanda_tangan']) && filter_var($SignatureUrl['tanda_tangan'], FILTER_VALIDATE_URL)) {
        // Jika valid URL, tampilkan gambar
        $signatureWitdh = 50;
        $pdf->Image($SignatureUrl['tanda_tangan'], (60-($signatureWitdh/2)), $pdf->GetY()-5, $signatureWitdh, 30);
    }

    $pdf->Ln($Height*3);
    $pdf->SetFont("Times","U",12);
    $pdf->Cell(90, $Height, $pengajuan['pengaju'], 0, 0, "C");
    $pdf->Cell(90, $Height, $pengajuan['penyetuju'], 0, 1, "C");




    $pdf->Output();

    }
