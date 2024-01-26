<?php 

include "../connection.php";

// Check if POST data is set and not empty
if(isset($_POST['chip_id']) && isset($_POST['produk']) && !empty($_POST['chip_id']) && !empty($_POST['produk'])) {
    // Receive POST data
    $chip_id = $_POST['chip_id'];
    $product = $_POST['produk'];

    // Get current Julian date
    $currentJulianDate = date('z') + 1; // Adding 1 to match the range 1-365

    // Get last two digits of the current year
    $currentYear = date('y');

    // Prepare and execute SQL query
    $stmt = $conn->prepare("SELECT no_sn FROM inventaris_produk WHERE chip_id = ? AND produk = ?");
    $stmt->bind_param("is", $chip_id, $product);
    $stmt->execute();
    $stmt->store_result();

    // Check if combination exists
    if ($stmt->num_rows > 0) {
        // Combination exists, fetch and return no_sn
        $stmt->bind_result($no_sn);
        $stmt->fetch();
        echo json_encode(array("no_sn" => $no_sn));
    } else {
        // Combination doesn't exist, generate no_sn with template
        $no_sn_prefix = $currentYear . sprintf('%03d', $currentJulianDate);
        $no_sn_like = $no_sn_prefix . '%';
        
        // Check if similar no_sn exists with different endings
        $stmt_check = $conn->prepare("SELECT no_sn FROM inventaris_produk WHERE no_sn LIKE ? ORDER BY no_sn DESC LIMIT 1");
        $stmt_check->bind_param("s", $no_sn_like);
        $stmt_check->execute();
        $stmt_check->bind_result($similar_no_sn);
        $stmt_check->fetch();
        $stmt_check->close(); // Close the statement after fetching the result
        
        if ($similar_no_sn) {
            // Use similar_no_sn with different endings
            $no_sn = $similar_no_sn + 1;
        } else {
            // Generate new no_sn with template
            $no_sn = intval($no_sn_prefix . '001'); // Convert to integer
        }
        
        // Insert new record with the generated no_sn
        $stmt_insert = $conn->prepare("INSERT INTO inventaris_produk (chip_id, produk, no_sn) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("isi", $chip_id, $product, $no_sn);
        
        // Execute the insertion statement
        if($stmt_insert->execute()) {
            echo json_encode(array("no_sn" => $no_sn));
        } else {
            // Handle insertion failure
            echo json_encode(array("error" => "Failed to insert data"));
        }
        
        // Close the insertion statement
        $stmt_insert->close();
    }

    // Close the initial statement
    $stmt->close();
} else {
    // Handle the case where chip_id or produk is not set or empty
    echo json_encode(array("error" => "Invalid or missing POST data"));
}