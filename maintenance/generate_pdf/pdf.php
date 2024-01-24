<?php
include ("../../connection.php");
require ("./fpdf/fpdf.php");

$pdf = new FPDF("P","mm","A4");

$pdf->AddPage();
$pdf->SetFont("Times","",12);
$pdf->SetMargins(20,0,20);

//Cell(Width, Height, Text, Border, End Line, Align)

$pdf->Cell(0,10,"transaksi_id/Rnd/YYYY",0,1,"R");

//Jarak
$pdf->Cell(0,20,"",0,1,);

$pdf->SetFont("Times","B",12);
$pdf->Cell(0,10,"BERITA ACARA SERAH TERIMA BARANG",0,1,"C");

$pdf->SetFont("Times","",12);
$pdf->Cell(0,10,"Kami yang bertanda tangan di bawah ini, pada tanggal DD, bulan MM, tahun YYYY",0,1);
$pdf->Cell(0,10,"Nama         : username",0,1);
$pdf->Cell(0,10,"Alamat       : Komplek Golden Plaza Fatmawati (Lottemart Fatmawati) Blok E No. 12A, Jl. R.S. Fatmawati No. 15 Kel. Gandaria, Kec. Cilandak, Jakarta Selatan",0,1);
$pdf->Cell(0,10,"Selanjutnya disebut PIHAK PERTAMA",0,1);
$pdf->Cell(0,10,"Nama         : nama_responden",0,1);
$pdf->Cell(0,10,"Alamat       : alamat_client",0,1);

$pdf->Cell(0,10,"",0,1,);

$pdf->Cell(0,10,"Selanjutnya disebut PIHAK KEDUA",0,1);
$pdf->MultiCell(0,6,"PIHAK PERTAMA menyerahkan barnag kepada PIHAK KEDUA, dan PIHAK KEDUA menyatakan telah menerima barang dari PIHAK PERTAMA berupa datar terlampir:",0,1);

//TABEL PRODUK dan Jarak
$pdf->Cell(0,6,"",0,1,);
$pdf->Cell(0,10,"",1,1);
$pdf->Cell(0,10,"",0,1,);

$pdf->MultiCell(0,6,"Demikian berita acara serah terima barang ini dibuat oleh kedua belah pihak, adapun barang-barang tersebut dalam keadaan baik dan lengkap, sejak penandatanganan berita acara ini, maka barang tersebut menjadi tanggung jawab PIHAK KEDUA.",0,1);

$pdf->Cell(0,10,"Yang Menyerahkan :",0,0, 'L');
$pdf->Cell(0,10,"Yang Menerima :",0,1, 'R');

//Beri Underline
$pdf->SetFont("Times","U",12);
//Beri Jarak TTD
$pdf->Cell(0,20,"",0,1,);

$pdf->Cell(0,10,"username",0,0, 'L');
$pdf->Cell(0,10,"nama_responden",0,1, 'R');


$pdf->Output();