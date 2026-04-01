<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZENET | Login</title>
    <link rel="stylesheet" href="/GymProjectPHP/src/public/css/style.css">
</head>
<body>
<?php
session_start();
include("../config/conexao.php");

// Se já está logado, redireciona
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['tipo'] === 'professor') header("Location: ../app/Views/professor/dashboard.php");
    else header("Location: ../app/Views/professor/dashboard.php");
    exit();
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf   = trim($_POST['cpf'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $tipo  = trim($_POST['tipo'] ?? 'aluno');

    if (empty($cpf) || empty($senha)) {// se os campos estão vazios, lança uma "exceção" que no caso é uma mensagem
        $erro = "Preencha todos os campos.";
    } else {
        $cpf_safe = mysqli_real_escape_string($conexao, $cpf);

        if ($tipo === 'professor') {
            $sql = "SELECT * FROM professores WHERE cpf = '$cpf_safe' LIMIT 1";
        } else {
            $sql = "SELECT * FROM alunos WHERE cpf = '$cpf_safe' LIMIT 1";
        }

        $res = mysqli_query($conexao, $sql);

        if (mysqli_num_rows($res) > 0) {
            $user = mysqli_fetch_assoc($res);
            if (md5($senha) === $user['senha']) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nome']       = $user['nome'];
                $_SESSION['cpf']        = $user['cpf'];
                $_SESSION['tipo']       = $tipo;

                if ($tipo === 'professor') header("Location: ../app/Views/professor/dashboard.php");
                else header("Location: ../app/Views/aluno/dashboard.php");
                exit();
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "Usuário não encontrado.";
        }
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-box fade-in">
        <div class="auth-logo">ZENET</div>
        <div class="auth-subtitle">Academia — Sistema de Treinos</div>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="post">
            <!-- Seletor de tipo -->
            <div class="tipo-selector" id="tipoSelector">
                <button type="button" class="tipo-btn active" onclick="setTipo('aluno')">
                    🏋️ Aluno
                </button>
                <button type="button" class="tipo-btn" onclick="setTipo('professor')">
                    📋 Professor
                </button>
            </div>
            <input type="hidden" name="tipo" id="tipoInput" value="aluno">

            <div class="form-group">
                <label for="cpf">CPF (somente números)</label>
                <input type="text" name="cpf" id="cpf" maxlength="11"
                       placeholder="Ex: 12345678900"
                       value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>

        <div class="auth-link">
            Novo aluno? <a href="cadastro.php">Cadastre-se aqui</a>
        </div>
    </div>
</div>

<script>
    function setTipo(tipo) {
        document.getElementById('tipoInput').value = tipo;
        document.querySelectorAll('.tipo-btn').forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }
</script>
</body>
</html>