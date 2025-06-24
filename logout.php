<?php
include 'dbConnection.php';

session_destroy();
header("Location: account.php");
exit();
?>
