<?php
// Destroy session and direct to home page
session_start();
session_destroy();
header("location: index.php");
?>