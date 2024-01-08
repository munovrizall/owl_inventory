<?php
// Start a session
session_start();

// Fetch the username from the POST data
$username = isset($_POST['username']) ? $_POST['username'] : '';

// Check if the username is not empty
if (!empty($username)) {
    // Save the username in a session variable
    $_SESSION['username'] = $username;

    // Return a success message or any other response if needed
    echo "Username successfully saved in session.";
} else {
    // Return an error message or handle the case where the username is empty
    echo "Error: Username cannot be empty.";
}
?>
