<?php
include "includes/auth.php";
include "includes/header.php";

// Total bebidas
$res = $conn->query("SELECT SUM(valor_unidade*quantidade_unidades) as total_valor, SUM(quantidade_litros*quantidade_unidades) as total_litros FROM bebidas");
$tb = $res->fetch_assoc();

// Total despesas
$res = $conn->query("SELECT SUM(valor*quantidade) as total FROM despesas");
$td = $res->fetch_assoc();

// Total Pix / Dinheiro (bebidas)
$res = $conn->query("SELECT SUM(valor_unidade*quantidade_unidades) as total_pix FROM bebidas WHERE forma_pagamento='pix'");
$total_pix = $res->fetch_assoc()['total_pix'] ?? 0;

$res = $conn->query("SELECT SUM(valor_unidade*quantidade_unidades) as total_dinheiro FROM bebidas WHERE forma_pagamento='dinheiro'");
$total_dinheiro = $res->fetch_assoc()['total_dinheiro'] ?? 0;

// Total recebido
$res = $conn->query("SELECT SUM(valor_pago) as total_recebido FROM convidados");
$total_recebido = $res->fetch_assoc()['total_recebido'] ?? 0;

// Saldo
$saldo = $total_recebido - (($tb['total_valor'] ?? 0) + ($td['total'] ?? 0));
?>

<h2>Contabilidade</h2>
<table border="1">
<tr><th>Descrição</th><th>Valor</th></tr>
<tr><td>Total Bebidas</td><td><?= number_format($tb['total_valor'] ?? 0,2,",",".") ?></td></tr>
<tr><td>Total Litros Bebidas</td><td><?= number_format($tb['total_litros'] ?? 0,3,",",".") ?></td></tr>
<tr><td>Total Despesas</td><td><?= number_format($td['total'] ?? 0,2,",",".") ?></td></tr>
<tr><td>Total Pix</td><td><?= number_format($total_pix,2,",",".") ?></td></tr>
<tr><td>Total Dinheiro</td><td><?= number_format($total_dinheiro,2,",",".") ?></td></tr>
<tr><td>Total Recebido (Convidados)</td><td><?= number_format($total_recebido,2,",",".") ?></td></tr>
<tr><td>Saldo</td><td><?= number_format($saldo,2,",",".") ?></td></tr>
</table>
