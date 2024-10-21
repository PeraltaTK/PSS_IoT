<?php
// session start
session_start();
// unset all vars
$_SESSION = array();
// destroy session
session_destroy();
// reirect to index.php
header("Location: index.php");
exit();
?>
