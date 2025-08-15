<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'remotelog.php'; // apenas conexão

$filtro_usuario = $_GET['usuario'] ?? '';
$filtro_acao = $_GET['acao'] ?? '';
$filtro_data = $_GET['data'] ?? '';

$sql = "SELECT *, SEC_TO_TIME(duration) AS tempo_formatado 
        FROM ppp_logs 
        WHERE 1=1";

if ($filtro_usuario) {
    $sql .= " AND username LIKE '%" . $mysqli->real_escape_string($filtro_usuario) . "%'";
}
if ($filtro_acao) {
    $sql .= " AND action = '" . $mysqli->real_escape_string($filtro_acao) . "'";
}
if ($filtro_data) {
    $sql .= " AND DATE(created_at) = '" . $mysqli->real_escape_string($filtro_data) . "'";
}

$sql .= " ORDER BY created_at DESC";

$result = $mysqli->query($sql);

if (!$result) {
    die("Erro na consulta SQL: " . $mysqli->error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Logs de Usuários</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to right, #f0f4f8, #d9e2ec);
        margin: 0;
        padding: 20px;
    }

    h1 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    /* Formulário */
    form {
        background: linear-gradient(to right, #6a11cb, #2575fc);
        padding: 20px;
        border-radius: 12px;
        color: white;
        max-width: 900px;
        margin: 0 auto 30px auto;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
    }

    form label {
        font-weight: bold;
        margin-right: 5px;
    }

    form input, form select {
        padding: 10px 12px;
        border-radius: 6px;
        border: none;
        outline: none;
        font-size: 14px;
        flex: 1 1 150px;
        transition: all 0.3s ease;
    }

    form input:focus, form select:focus {
        box-shadow: 0 0 8px rgba(255,255,255,0.8);
    }

    form button {
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        background:rgb(189, 40, 209);
        color: white;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
        flex: 0 0 auto;
    }

    form button:hover {
        background:rgb(123, 34, 134);
    }

    /* Tabela */
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    table tr {
        transition: background 0.3s ease;
    }

    table tr:hover {
        background: linear-gradient(90deg, #dfe9f3, #ffffff);
    }

    th {
        background: linear-gradient(90deg, #6a11cb, #2575fc);
        color: white;
        padding: 12px;
        text-align: left;
    }

    td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    @media (max-width: 700px) {
        form {
            flex-direction: column;
            gap: 10px;
        }

        form input, form select, form button {
            width: 100%;
        }

        table, th, td {
            font-size: 14px;
        }
    }
</style>
</head>
<body>

<h1>Logs de acessos VPN</h1>

<form method="GET">
    <label>Usuário:</label>
    <input type="text" name="usuario" value="<?=htmlspecialchars($filtro_usuario)?>">

    <label>Ação:</label>
    <select name="acao">
        <option value="">Todas</option>
        <option value="login" <?=($filtro_acao=='login'?'selected':'')?>>Login</option>
        <option value="logout" <?=($filtro_acao=='logout'?'selected':'')?>>Logout</option>
    </select>

    <label>Data:</label>
    <input type="date" name="data" value="<?=htmlspecialchars($filtro_data)?>">

    <button type="submit">Filtrar</button>
</form>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Usuário</th>
    <th>Ação</th>
    <th>Data/Hora</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?=htmlspecialchars($row['id'])?></td>
    <td><?=htmlspecialchars($row['username'])?></td>
    <td><?=htmlspecialchars($row['action'])?></td>
    <td><?=htmlspecialchars($row['created_at'])?></td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
