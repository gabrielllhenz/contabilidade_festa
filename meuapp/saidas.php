<?php
include "includes/auth.php";
include "includes/header.php";
// Assumindo que $conn está definido

// ===============================================
// LÓGICA DE EXCLUSÃO (Geral)
// ===============================================

if (isset($_POST['delete_bebida'])) {
    $id = filter_input(INPUT_POST, 'bebida_id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM bebidas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: " . $_SERVER['PHP_SELF']); exit;
}

if (isset($_POST['delete_despesa'])) {
    $id = filter_input(INPUT_POST, 'despesa_id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM despesas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: " . $_SERVER['PHP_SELF']); exit;
}

// ===============================================
// LÓGICA DE TOGGLE (Geral)
// ===============================================

// Toggle Bebidas
$toggle_bebida_id = filter_input(INPUT_GET, 'toggle_bebida_id', FILTER_VALIDATE_INT);
if ($toggle_bebida_id) {
    $result_status = $conn->query("SELECT pago FROM bebidas WHERE id = $toggle_bebida_id");
    if ($row = $result_status->fetch_assoc()) {
        $novo_status = $row['pago'] ? 0 : 1; 
        $stmt = $conn->prepare("UPDATE bebidas SET pago = ? WHERE id = ?");
        $stmt->bind_param("ii", $novo_status, $toggle_bebida_id);
        $stmt->execute();
    }
    header("Location: " . $_SERVER['PHP_SELF']); exit;
}

// Toggle Despesas
$toggle_despesa_id = filter_input(INPUT_GET, 'toggle_despesa_id', FILTER_VALIDATE_INT);
if ($toggle_despesa_id) {
    $result_status = $conn->query("SELECT pago FROM despesas WHERE id = $toggle_despesa_id");
    if ($row = $result_status->fetch_assoc()) {
        $novo_status = $row['pago'] ? 0 : 1; 
        $stmt = $conn->prepare("UPDATE despesas SET pago = ? WHERE id = ?");
        $stmt->bind_param("ii", $novo_status, $toggle_despesa_id);
        $stmt->execute();
    }
    header("Location: " . $_SERVER['PHP_SELF']); exit;
}


// ===============================================
// LÓGICA DE ADICIONAR E ATUALIZAR BEBIDAS
// ===============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adicionar bebida
    if (isset($_POST['add_bebida'])) {
        $nome = $_POST['nome'];
        $valor_unidade = filter_input(INPUT_POST, 'valor_unidade', FILTER_VALIDATE_FLOAT);
        $quantidade_litros = filter_input(INPUT_POST, 'quantidade_litros', FILTER_VALIDATE_FLOAT);
        $quantidade_unidades = filter_input(INPUT_POST, 'quantidade_unidades', FILTER_VALIDATE_INT);
        $forma_pagamento = $_POST['forma_pagamento'];
        $pago = filter_input(INPUT_POST, 'pago', FILTER_VALIDATE_INT); // Agora espera 0 ou 1
        if ($pago === false) $pago = 0;

        $stmt = $conn->prepare("INSERT INTO bebidas (nome, valor_unidade, quantidade_litros, quantidade_unidades, forma_pagamento, pago) VALUES (?,?,?,?,?,?)");
        // O tipo de 'pago' é 'i' (integer/tinyint)
        $stmt->bind_param("sddisi", $nome, $valor_unidade, $quantidade_litros, $quantidade_unidades, $forma_pagamento, $pago);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']); exit;
    }

    // Atualizar bebida
    if (isset($_POST['update_bebida'])) {
        $id = filter_input(INPUT_POST, 'bebida_id', FILTER_VALIDATE_INT);
        if ($id) {
            $nome = $_POST['nome_up'];
            $valor_unidade = filter_input(INPUT_POST, 'valor_unidade_up', FILTER_VALIDATE_FLOAT);
            $quantidade_litros = filter_input(INPUT_POST, 'quantidade_litros_up', FILTER_VALIDATE_FLOAT);
            $quantidade_unidades = filter_input(INPUT_POST, 'quantidade_unidades_up', FILTER_VALIDATE_INT);
            $forma_pagamento = $_POST['forma_pagamento_up'];

            $stmt = $conn->prepare("UPDATE bebidas SET nome=?, valor_unidade=?, quantidade_litros=?, quantidade_unidades=?, forma_pagamento=? WHERE id=?");
            $stmt->bind_param("sddisi", $nome, $valor_unidade, $quantidade_litros, $quantidade_unidades, $forma_pagamento, $id);
            $stmt->execute();
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }
    }
}

// ===============================================
// LÓGICA DE ADICIONAR E ATUALIZAR DESPESAS
// ===============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adicionar despesa
    if (isset($_POST['add_despesa'])) {
        $nome = $_POST['nome'];
        $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
        $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
        $observacao = $_POST['observacao'];
        $pago_default = 0; // Novo campo

        $stmt = $conn->prepare("INSERT INTO despesas (nome, valor, quantidade, observacao, pago) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sdisi", $nome, $valor, $quantidade, $observacao, $pago_default);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']); exit;
    }

    // Atualizar despesa
    if (isset($_POST['update_despesa'])) {
        $id = filter_input(INPUT_POST, 'despesa_id', FILTER_VALIDATE_INT);
        if ($id) {
            $nome = $_POST['nome_up'];
            $valor = filter_input(INPUT_POST, 'valor_up', FILTER_VALIDATE_FLOAT);
            $quantidade = filter_input(INPUT_POST, 'quantidade_up', FILTER_VALIDATE_INT);
            $observacao = $_POST['observacao_up'];
            
            $stmt = $conn->prepare("UPDATE despesas SET nome=?, valor=?, quantidade=?, observacao=? WHERE id=?");
            $stmt->bind_param("sdisi", $nome, $valor, $quantidade, $observacao, $id);
            $stmt->execute();
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }
    }
}

// ===============================================
// LISTAR BEBIDAS E DESPESAS
// ===============================================

// Listar bebidas - Agora inclui ID e 'pago'
$bebidas = $conn->query("SELECT id, nome, valor_unidade, quantidade_litros, quantidade_unidades, forma_pagamento, pago, (valor_unidade*quantidade_unidades) as total, (quantidade_litros*quantidade_unidades) as total_litros FROM bebidas ORDER BY nome");

// Listar despesas - Agora inclui ID e 'pago'
$despesas = $conn->query("SELECT id, nome, valor, quantidade, observacao, pago, (valor*quantidade) as total FROM despesas ORDER BY nome");
?>

<h2>Bebidas</h2>
<form method="post">
    Nome: <input type="text" name="nome" required autofocus>
    Valor Unidade: <input type="number" step="0.01" name="valor_unidade" required>
    Litros por Unidade: <input type="number" step="0.001" name="quantidade_litros" required>
    Unidades: <input type="number" name="quantidade_unidades" required>
    Pagamento: 
    <select name="forma_pagamento">
        <option value="pix">Pix</option>
        <option value="dinheiro">Dinheiro</option>
    </select>
    <!-- O campo 'pago' para adicionar agora envia 0 ou 1 -->
    Pago: 
    <select name="pago">
        <option value="1">Sim</option>
        <option value="0" selected>Não</option>
    </select>
    <button type="submit" name="add_bebida">Adicionar</button>
</form>

<table border="1">
<tr>
    <th>Nome</th>
    <th>Valor Unit.</th>
    <th>Litros/U</th>
    <th>Unidades</th>
    <th>Total Litros</th>
    <th>Total</th>
    <th>Pagamento</th>
    <th>Pago</th> <!-- AGORA É UM TOGGLE -->
    <th>Ação</th>
</tr>
<?php while($row = $bebidas->fetch_assoc()): ?>
<?php $row_class = $row['pago'] ? 'style="background-color: #e8f5e9;"' : ''; ?>
<tr <?= $row_class ?>>
    <!-- Edição Inline (Formulário para Atualizar) -->
    <form method="post" style="display:contents;">
        <input type="hidden" name="bebida_id" value="<?= $row['id'] ?>">

        <td><input type="text" name="nome_up" value="<?= htmlspecialchars($row['nome']) ?>" required style="width: 100px;"></td>
        <td><input type="number" step="0.01" name="valor_unidade_up" value="<?= htmlspecialchars($row['valor_unidade']) ?>" required style="width: 70px;"></td>
        <td><input type="number" step="0.001" name="quantidade_litros_up" value="<?= htmlspecialchars($row['quantidade_litros']) ?>" required style="width: 60px;"></td>
        <td><input type="number" name="quantidade_unidades_up" value="<?= htmlspecialchars($row['quantidade_unidades']) ?>" required style="width: 50px;"></td>
        
        <td><?= number_format($row['total_litros'],3,",",".") ?></td>
        <td><?= number_format($row['total'],2,",",".") ?></td>

        <td>
            <select name="forma_pagamento_up" style="width: 70px;">
                <option value="pix" <?= ($row['forma_pagamento'] == 'pix') ? 'selected' : '' ?>>Pix</option>
                <option value="dinheiro" <?= ($row['forma_pagamento'] == 'dinheiro') ? 'selected' : '' ?>>Dinheiro</option>
            </select>
        </td>

        <!-- TOGGLE PAGO -->
        <td>
            <?php
                if ($row['pago']) {
                    $status_text = 'Sim';
                    $status_color = 'background-color: #4CAF50;';
                } else {
                    $status_text = 'Não';
                    $status_color = 'background-color: #f44336;';
                }
            ?>
            <a href="?toggle_bebida_id=<?= $row['id'] ?>" 
               style="text-decoration: none; padding: 5px 10px; border-radius: 5px; font-weight: bold; <?= $status_color ?> color: white;">
                <?= $status_text ?>
            </a>
        </td>

        <!-- BOTÕES DE AÇÃO -->
        <td>
            <button type="submit" name="update_bebida" style="background-color: #007bff; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; margin-right: 5px;">
                Atualizar
            </button>
        </form>
        
        <form method="post" style="display:inline;">
            <input type="hidden" name="bebida_id" value="<?= $row['id'] ?>">
            <button type="submit" name="delete_bebida" onclick="return confirm('Tem certeza?')" style="background-color: #f44336; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;">
                Excluir
            </button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>

---

<h2>Despesas</h2>
<form method="post">
    Nome: <input type="text" name="nome" required>
    Valor: <input type="number" step="0.01" name="valor" required>
    Quantidade: <input type="number" name="quantidade" required>
    Observação: <input type="text" name="observacao">
    <button type="submit" name="add_despesa">Adicionar</button>
</form>

<table border="1">
<tr>
    <th>Nome</th>
    <th>Valor Unit.</th>
    <th>Quantidade</th>
    <th>Total</th>
    <th>Observação</th>
    <th>Pago</th> <!-- NOVO TOGGLE -->
    <th>Ação</th>
</tr>
<?php while($row = $despesas->fetch_assoc()): ?>
<?php $row_class = $row['pago'] ? 'style="background-color: #e8f5e9;"' : ''; ?>
<tr <?= $row_class ?>>
    <!-- Edição Inline (Formulário para Atualizar) -->
    <form method="post" style="display:contents;">
        <input type="hidden" name="despesa_id" value="<?= $row['id'] ?>">

        <td><input type="text" name="nome_up" value="<?= htmlspecialchars($row['nome']) ?>" required style="width: 150px;"></td>
        <td><input type="number" step="0.01" name="valor_up" value="<?= htmlspecialchars($row['valor']) ?>" required style="width: 70px;"></td>
        <td><input type="number" name="quantidade_up" value="<?= htmlspecialchars($row['quantidade']) ?>" required style="width: 50px;"></td>
        
        <td><?= number_format($row['total'],2,",",".") ?></td>
        <td><input type="text" name="observacao_up" value="<?= htmlspecialchars($row['observacao']) ?>" style="width: 150px;"></td>

        <!-- TOGGLE PAGO -->
        <td>
            <?php
                if ($row['pago']) {
                    $status_text = 'Sim';
                    $status_color = 'background-color: #4CAF50;';
                } else {
                    $status_text = 'Não';
                    $status_color = 'background-color: #f44336;';
                }
            ?>
            <a href="?toggle_despesa_id=<?= $row['id'] ?>" 
               style="text-decoration: none; padding: 5px 10px; border-radius: 5px; font-weight: bold; <?= $status_color ?> color: white;">
                <?= $status_text ?>
            </a>
        </td>

        <!-- BOTÕES DE AÇÃO -->
        <td>
            <button type="submit" name="update_despesa" style="background-color: #007bff; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; margin-right: 5px;">
                Atualizar
            </button>
        </form>
        
        <form method="post" style="display:inline;">
            <input type="hidden" name="despesa_id" value="<?= $row['id'] ?>">
            <button type="submit" name="delete_despesa" onclick="return confirm('Tem certeza?')" style="background-color: #f44336; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;">
                Excluir
            </button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
