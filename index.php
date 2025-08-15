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
        background: linear-gradient(to right, #e0f7fa, #e1bee7);
        margin: 0;
        padding: 20px;
    }

    h1 {
        text-align: center;
        color: #4a148c;
        margin-bottom: 30px;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
    }

    /* Formulário */
    form {
        background: linear-gradient(to right, #8e24aa, #3949ab);
        padding: 20px;
        border-radius: 12px;
        color: white;
        max-width: 900px;
        margin: 0 auto 40px auto;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
        transition: transform 0.3s ease;
    }

    form:hover {
        transform: translateY(-3px);
    }

    form label {
        font-weight: bold;
        margin-right: 5px;
    }

    form input, form select {
        padding: 12px 15px;
        border-radius: 8px;
        border: none;
        outline: none;
        font-size: 14px;
        flex: 1 1 150px;
        transition: all 0.3s ease;
    }

    form input:focus, form select:focus {
        box-shadow: 0 0 10px rgba(255,255,255,0.9);
    }

    form button {
        padding: 12px 25px;
        border-radius: 8px;
        border: none;
        background: #d81b60;
        color: white;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
        flex: 0 0 auto;
    }

    form button:hover {
        background: #880e4f;
    }

    /* Tabela */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }

    table tr {
        transition: background 0.3s ease, transform 0.2s ease;
    }

    table tr:hover {
        background: linear-gradient(90deg, #f3e5f5, #e1f5fe);
        transform: scale(1.01);
    }

    th {
        background: linear-gradient(90deg, #8e24aa, #3949ab);
        color: white;
        padding: 15px;
        text-align: left;
        font-size: 15px;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        color: #333;
    }

    td:first-child {
        font-weight: bold;
        color: #4a148c;
    }

    @media (max-width: 700px) {
        form {
            flex-direction: column;
            gap: 12px;
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
