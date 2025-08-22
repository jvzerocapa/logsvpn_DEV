<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'remotelog.php'; // conexão

$filtro_usuario = $_GET['usuario'] ?? '';
$filtro_acao = $_GET['acao'] ?? '';
$filtro_data_inicio = $_GET['data_inicio'] ?? '';
$filtro_data_fim = $_GET['data_fim'] ?? '';

// ---- PAGINAÇÃO ----
$registros_por_pagina = isset($_GET['limit']) ? (int)$_GET['limit'] : 8; 
if ($registros_por_pagina <= 0) $registros_por_pagina = 8;

$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual <= 0) $pagina_atual = 1;

$offset = ($pagina_atual - 1) * $registros_por_pagina;

// ---- QUERY PRINCIPAL ----
$sql_base = "FROM ppp_logs WHERE 1=1";

if ($filtro_usuario) {
    $sql_base .= " AND username LIKE '%" . $mysqli->real_escape_string($filtro_usuario) . "%'";
}
if ($filtro_acao) {
    $sql_base .= " AND action = '" . $mysqli->real_escape_string($filtro_acao) . "'";
}
if ($filtro_data_inicio && $filtro_data_fim) {
    $sql_base .= " AND DATE(created_at) BETWEEN '" . $mysqli->real_escape_string($filtro_data_inicio) . "' 
                                        AND '" . $mysqli->real_escape_string($filtro_data_fim) . "'";
} elseif ($filtro_data_inicio) {
    $sql_base .= " AND DATE(created_at) >= '" . $mysqli->real_escape_string($filtro_data_inicio) . "'";
} elseif ($filtro_data_fim) {
    $sql_base .= " AND DATE(created_at) <= '" . $mysqli->real_escape_string($filtro_data_fim) . "'";
}

// ---- TOTAL DE REGISTROS ----
$sql_count = "SELECT COUNT(*) AS total " . $sql_base;
$total_result = $mysqli->query($sql_count);
$total_registros = $total_result->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// ---- BUSCA COM LIMIT ----
$sql = "SELECT *, SEC_TO_TIME(duration) AS tempo_formatado 
        $sql_base 
        ORDER BY created_at DESC 
        LIMIT $registros_por_pagina OFFSET $offset";

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
        min-height: 100vh;
        display: flex;
        flex-direction: column;
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
        max-width: 100%;
        margin: 0 auto 40px auto;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        display: flex;
        gap: 15px;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
        transition: transform 0.3s ease;
    }

    form:hover {
        transform: translateY(-3px);
    }

    form label {
        font-weight: bold;
        margin-right: 5px;
        white-space: nowrap;
    }

    form input, form select {
        padding: 10px 12px;
        border-radius: 8px;
        border: none;
        outline: none;
        font-size: 14px;
        min-width: 120px;
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

    @media (max-width: 900px) {
        form {
            flex-wrap: wrap;
            justify-content: flex-start;
        }
        form input, form select, form button {
            flex: 1 1 150px;
        }
    }

    /* Rodapé */
    footer {
        margin-top: auto;
        text-align: center;
        padding: 15px;
    }

    footer .rodape {
        display: inline-block;
        background: white;
        padding: 10px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    footer .rodape a {
        color: #4a148c;
        text-decoration: none;
        font-weight: 600;
    }

    footer .rodape a:hover {
        text-decoration: underline;
    }
/* seu CSS permanece igual */
.pagination {
    margin: 20px 0;
    text-align: center;
}
.pagination a {
    margin: 0 5px;
    padding: 8px 12px;
    background: #8e24aa;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}
.pagination a:hover {
    background: #3949ab;
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

    <label>Data inicial:</label>
    <input type="date" name="data_inicio" value="<?=htmlspecialchars($filtro_data_inicio)?>">

    <label>Data final:</label>
    <input type="date" name="data_fim" value="<?=htmlspecialchars($filtro_data_fim)?>">

    <label>Por página:</label>
    <select name="limit">
        <option value="8" <?=($registros_por_pagina==8?'selected':'')?>>8</option>
        <option value="25" <?=($registros_por_pagina==25?'selected':'')?>>25</option>
        <option value="30" <?=($registros_por_pagina==30?'selected':'')?>>30</option>
        <option value="50" <?=($registros_por_pagina==50?'selected':'')?>>50</option>
    </select>

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
    <td><?= date('d/m/Y H:i:s', strtotime($row['created_at'])) ?></td>
</tr>
<?php endwhile; ?>
</table>

<!-- Paginação -->
<div class="pagination">
<?php if ($pagina_atual > 1): ?>
    <a href="?<?=http_build_query(array_merge($_GET,['pagina'=>$pagina_atual-1]))?>">Anterior</a>
<?php endif; ?>

<?php for($i=1; $i<=$total_paginas; $i++): ?>
    <a href="?<?=http_build_query(array_merge($_GET,['pagina'=>$i]))?>"
       style="<?=($i==$pagina_atual?'background:#d81b60;':'')?>"><?= $i ?></a>
<?php endfor; ?>

<?php if ($pagina_atual < $total_paginas): ?>
    <a href="?<?=http_build_query(array_merge($_GET,['pagina'=>$pagina_atual+1]))?>">Próxima</a>
<?php endif; ?>
</div>

<footer>
  <div class="rodape">
    <p>
      <a href="https://github.com/jvzerocapa" target="_blank">
        Powered by João Vitor
      </a>
    </p>
  </div>
</footer>

</body>
</html>
