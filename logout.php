<?php
// início da sessão
session_start();
// anular todas as vars
$_SESSION = array();
// destruir sessão
session_destroy();
// redirecionar para index.php
header("Location: index.php");
exit();
?>
