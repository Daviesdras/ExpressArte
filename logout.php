<?php
session_start();
session_destroy(); // Encerra a sessão
header("Location: princip_pagin.php"); // Redireciona para a página de login
exit();
?>
