<?php
// MOSTRAR TODOS OS ERROS (ESSENCIAL PARA DEBUG)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "includes/db.php"; // Agora este arquivo está 100% correto

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha   = $_POST['senha'] ?? '';

    $stmt = $conn->prepare("SELECT id, usuario, senha FROM usuario WHERE usuario = ?");
    if ($stmt === false) {
        die("Erro prepare: " . $conn->error);
    }
    $stmt->bind_param("s", $usuario);
    $stmt->execute();

    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $erro = "Usuário não encontrado.";
    } else {
        $stmt->bind_result($id, $dbusuario, $dbsenha);
        $stmt->fetch();
        if (password_verify($senha, $dbsenha)) {
            $_SESSION['user_id'] = $id;
            header("Location: entradas.php");
            exit;
        } else {
            $erro = "Senha incorreta.";
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post">
        Usuário: <input type="text" name="usuario" required><br>
        Senha: <input type="password" name="senha" required><br>
        <button type="submit">Entrar</button>
    </form>
    <?php if(isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
</body>
</html>