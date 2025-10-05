<?php
include "includes/auth.php";
include "includes/header.php";
// Assume que includes/db.php foi incluído no header ou auth para $conn
// Se não, inclua: include "includes/db.php"; 


// ===============================================
// LÓGICA DE ATUALIZAÇÃO (Edição Inline)
// ===============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_convidado'])) {
    $convidado_id_update = filter_input(INPUT_POST, 'convidado_id', FILTER_VALIDATE_INT);
    
    if ($convidado_id_update) {
        $nome_update = $_POST['nome_update'] ?? '';
        $observacao_update = $_POST['observacao_update'] ?? '';
        $valor_pago_update = filter_input(INPUT_POST, 'valor_pago_update', FILTER_VALIDATE_FLOAT);
        if ($valor_pago_update === false) $valor_pago_update = 0;

        $stmt = $conn->prepare("UPDATE convidados SET nome = ?, valor_pago = ?, observacao = ? WHERE id = ?");
        $stmt->bind_param("sdsi", $nome_update, $valor_pago_update, $observacao_update, $convidado_id_update);
        $stmt->execute();

        header("Location: " . $_SERVER['PHP_SELF']); 
        exit;
    }
}

// ===============================================
// LÓGICA DE TOGGLE (Entrou na Festa)
// O status é invertido a cada clique (0 para 1, 1 para 0)
// ===============================================
$id_toggle = filter_input(INPUT_GET, 'toggle_id', FILTER_VALIDATE_INT);
if ($id_toggle) {
    // Busca o status atual
    $result_status = $conn->query("SELECT entrou_na_festa FROM convidados WHERE id = $id_toggle");
    if ($result_status->num_rows > 0) {
        $row_status = $result_status->fetch_assoc();
        $novo_status = $row_status['entrou_na_festa'] ? 0 : 1; 

        $stmt = $conn->prepare("UPDATE convidados SET entrou_na_festa = ? WHERE id = ?");
        $stmt->bind_param("ii", $novo_status, $id_toggle);
        $stmt->execute();
    }
    
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit;
}

// ===============================================
// LÓGICA DE ADICIONAR CONVIDADO
// ===============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_convidado'])) {
    $nome = $_POST['nome'];
    $valor_pago = $_POST['valor_pago'];
    $observacao = $_POST['observacao'];
    $entrou_na_festa_default = 0; // Novo campo, sempre começa como Não Entrou

    $stmt = $conn->prepare("INSERT INTO convidados (nome, valor_pago, observacao, entrou_na_festa) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdsi", $nome, $valor_pago, $observacao, $entrou_na_festa_default);
    $stmt->execute();
    
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit;
}


// ===============================================
// LÓGICA DE EXCLUIR CONVIDADO
// ===============================================
if (isset($_POST['delete_convidado'])) {
    $id_para_excluir = filter_input(INPUT_POST, 'convidado_id', FILTER_VALIDATE_INT);

    if ($id_para_excluir !== null && $id_para_excluir !== false) {
        $stmt = $conn->prepare("DELETE FROM convidados WHERE id = ?");
        $stmt->bind_param("i", $id_para_excluir);
        $stmt->execute();
        
        header("Location: " . $_SERVER['PHP_SELF']); 
        exit;
    }
}


// ===============================================
// LISTAR CONVIDADOS (AGORA INCLUI entrou_na_festa)
// ===============================================
$result = $conn->query("SELECT id, nome, valor_pago, observacao, entrou_na_festa FROM convidados ORDER BY nome");

// INICIALIZA O CONTADOR
$contador = 0; 
?>

<h2>Convidados</h2>
<form method="post">
    Nome: <input type="text" name="nome" required autofocus>
    Valor Pago: <input type="number" step="0.01" name="valor_pago" required>
    Observação: <input type="text" name="observacao">
    <button type="submit" name="add_convidado">Adicionar</button>
</form>

<table border="1">
<tr>
    <th>Nº</th> 
    <th>Nome</th>
    <th>Valor Pago</th>
    <th>Observação</th>
    <th>Entrou na Festa?</th>
    <th>Ação</th>
</tr>

<?php while($row = $result->fetch_assoc()): 
    $contador++; 
    $row_class = $row['entrou_na_festa'] ? 'style="background-color: #e8f5e9;"' : '';
?>
<tr <?= $row_class ?>> 
    <td><?= $contador ?></td> 
    

    <!-- Este formulário envia os campos de input e o botão "Atualizar" -->
    <form method="post" style="display:contents;">
        
        <!-- CAMPOS EDITÁVEIS (Inputs para edição inline) -->
        <td><input type="text" name="nome_update" value="<?= htmlspecialchars($row['nome']) ?>" required style="width: 150px;"></td>
        <td><input type="number" step="0.01" name="valor_pago_update" value="<?= htmlspecialchars($row['valor_pago']) ?>" required style="width: 80px;"></td>
        <td><input type="text" name="observacao_update" value="<?= htmlspecialchars($row['observacao']) ?>" style="width: 180px;"></td>
        
        <!-- TOGGLE DE PRESENÇA (Link GET para inversão) -->
        <td>
            <?php
                if ($row['entrou_na_festa']) {
                    $status_text = 'Sim';
                    $status_color = 'background-color: #4CAF50;';
                } else {
                    $status_text = 'Não';
                    $status_color = 'background-color: #f44336;';
                }
            ?>
            <a href="?toggle_id=<?= $row['id'] ?>" 
               style="text-decoration: none; padding: 5px 10px; border-radius: 5px; font-weight: bold; <?= $status_color ?> color: white;">
                <?= $status_text ?>
            </a>
        </td>
        
        <!-- BOTÕES DE AÇÃO -->
        <td>
            <input type="hidden" name="convidado_id" value="<?= $row['id'] ?>">
            
            <!-- Botão Atualizar (submete o formulário da linha com os dados editados) -->
            <button type="submit" 
                    name="update_convidado" 
                    style="background-color: #007bff; color: white; margin-right: 5px; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;">
                Atualizar
            </button>
            
        <!-- O botão Excluir usa um FORMULÁRIO POST SEPARADO para evitar conflito com a atualização -->
        </form>
        
        <form method="post" style="display:inline;">
            <input type="hidden" name="convidado_id" value="<?= $row['id'] ?>">
            <button type="submit" 
                    name="delete_convidado" 
                    onclick="return confirm('Tem certeza que deseja excluir <?= $row['nome'] ?>?')"
                    style="background-color: #f44336; color: white; margin-right: 5px; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;">
                Excluir
            </button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>