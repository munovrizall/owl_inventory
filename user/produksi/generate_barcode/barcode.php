<?php

include '../../../connection.php';
include 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

// Function to generate and save barcode image
function generateBarcodeImage($data, $generator)
{
    $barcodeHeight = 50; // Height of each barcode image
    $barcodeWidth = 200; // Width of each barcode image (assuming a fixed width)
    $textHeight = 20; // Height of text area

    $combinedHeight = ($barcodeHeight + $textHeight) * count($data) + 100; // Calculate combined image height
    $combinedBarcodeImage = imagecreatetruecolor(800, $combinedHeight); // Initialize combined image

    // Set white background for combined image
    imagefill($combinedBarcodeImage, 0, 0, imagecolorallocate($combinedBarcodeImage, 255, 255, 255));

    // Initialize Y-coordinate (top)
    $y = 50;

    // Generate and combine barcode images with data text
    foreach ($data as $fieldName => $value) {
        // Generate barcode image
        $barcodeImage = imagecreatefromstring($generator->getBarcode($value, $generator::TYPE_CODE_128));

        // Calculate horizontal and vertical position for barcode
        $barcodeWidth = imagesx($barcodeImage); // Get the actual width of the barcode image
        $xBarcode = (imagesx($combinedBarcodeImage) - $barcodeWidth) / 2; // Center barcode horizontally
        $yBarcode = $y + ($barcodeHeight - imagesy($barcodeImage)) / 2;

        // Copy barcode image to combined image
        imagecopy($combinedBarcodeImage, $barcodeImage, $xBarcode, $yBarcode, 0, 0, $barcodeWidth, imagesy($barcodeImage));
        imagedestroy($barcodeImage);

        // Calculate vertical position for text
        $yText = $y + $barcodeHeight - 7;

        // Calculate width of text area
        $textWidth = imagefontwidth(5) * strlen("$fieldName: $value");

        // Calculate X-coordinate for text based on the barcode above it
        $xText = $xBarcode + ($barcodeWidth - $textWidth) / 2; // Center text horizontally based on the barcode

        // Add text below the barcode
        $textColor = imagecolorallocate($combinedBarcodeImage, 0, 0, 0); // Black color for text
        imagestring($combinedBarcodeImage, 5, $xText, $yText, "$fieldName: $value", $textColor);

        // Update Y-coordinate for next barcode and text
        $y += $barcodeHeight + $textHeight;
    }

    // Output combined barcode image directly to the browser
    header('Content-Type: image/png');
    header("Content-Disposition: attachment; filename=\"combined_barcodes.png\"");
    imagepng($combinedBarcodeImage);
    imagedestroy($combinedBarcodeImage);

    exit(); // Terminate script after sending the image
}

// Check if ID is set in the URL
if(isset($_GET['id'])){
    $rowId = $_GET['id'];

    // Fetch data associated with the row ID from the database
    $query = "SELECT no_sn, mac_bluetooth, mac_wifi FROM inventaris_produk WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $rowId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if result exists
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Create barcode generator instance
        $generator = new BarcodeGeneratorPNG();

        // Generate and download combined barcode image
        generateBarcodeImage($data, $generator);
    } else {
        echo "No data found for ID: $rowId";
    }
} else {
    echo "ID not provided in the URL!";
}