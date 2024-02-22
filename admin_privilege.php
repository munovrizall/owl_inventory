<?php
if ($_SESSION['role'] !== "admin") {
    // Redirect to another page or display an error message
    header("Location: user/homepage.php");
    exit();
}