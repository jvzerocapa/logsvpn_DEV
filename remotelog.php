<?php
$mysqli = new mysqli("192.168.150.131", "root", "436904D31T3C@", "logsvpn");

if ($mysqli->connect_error) {
    die("Erro na conexão: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");
?>
