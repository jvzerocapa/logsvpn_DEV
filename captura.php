<?php
date_default_timezone_set('America/Sao_Paulo'); 

$mysqli = new mysqli("DB_HOST", "USER", "PASSWORD", "DB_NAME");
if ($mysqli->connect_error) { die("Erro na conexão: " . $mysqli->connect_error); }
$mysqli->set_charset("utf8");

// Recebe os parâmetros enviados pelo Mikrotik
$action   = $_POST['action'] ?? '';
$username = $_POST['username'] ?? '';
$ip       = $_POST['ip'] ?? '';
$duration = $_POST['duration'] ?? 0;


if ($username && $action) {
    $now = date('Y-m-d H:i:s'); 
    $stmt = $mysqli->prepare("INSERT INTO ppp_logs (username, ip, action, duration, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $username, $ip, $action, $duration, $now);
    $stmt->execute();
    $stmt->close();
}

echo "Registro inserido com sucesso!";
?>
