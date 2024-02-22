<?php
// Start a session
session_start();

// Check if the username is provided in the POST data
if(isset($_POST['username'])) {
    // Fetch the username from the POST data
    $username = $_POST['username'];

    // Database connection parameters
    $serverName = "localhost";
    $userNameDb = "root";
    $password = "";
    $dbName = "databaseinventory";

    // Create a new database connection
    $conn = new mysqli($serverName, $userNameDb, $password, $dbName);

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query to fetch the full name based on the username
    $queryNama = "SELECT nama_lengkap, role FROM user_account WHERE username = ?";
    $stmt = $conn->prepare($queryNama);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($nama, $role);
    $stmt->fetch();
    $stmt->close();

    if ($nama) {
        // Save the full name in a session variable
        $_SESSION['username'] = $nama;
        $_SESSION['role'] = $role;

        $userData = array(
            'username' => $username,
            'role' => $role
        );
    
        // Return the user data as JSON
        echo json_encode($userData);
    } else {
        echo "Error: Full name not found for the provided username.";
    }
} else {
    echo "Error: Username not provided.";
}
?>