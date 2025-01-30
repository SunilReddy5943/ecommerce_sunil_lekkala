<?php
session_start();
session_unset();
session_destroy();
header("Location: ../index.php"); // Redirect admin to home page after logout
exit();
?>
