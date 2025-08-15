<?php
$mysqli = new mysqli("DB_HOST", "DB_USER", "DB_PASS", "DB_NAME");

if ($mysqli->connect_error) {
    die("Erro na conexão: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");
?>
