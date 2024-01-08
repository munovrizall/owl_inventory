<?php
// process_username.php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"])) {
    $receivedUsername = $_POST["username"];
    // Send a response (optional)
    echo "Username received and processed successfully.";
} else {
    // Invalid request
    http_response_code(400);
    echo "Invalid request.";
}
?>
