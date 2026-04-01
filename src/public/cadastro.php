<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZENET | Cadastro</title>
    <link rel="stylesheet" href="/GymProjectPHP/src/public/css/style.css">
</head>
<body>
<?php
session_start();
include("../config/conexao.php");

$erro    = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome    = trim($_POST['nome']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $cpf     = trim($_POST['cpf']     ?? '');
    $senha   = trim($_POST['senha']   ?? '');
    $confirm = trim($_POST['confirm'] ?? '');
    $nasc    = trim($_POST['nascimento'] ?? '');
    $obj     = trim($_POST['objetivo'] ?? '');

    // Validações básicas
    if (empty($nome) || empty($email) || empty($cpf) || empty($senha)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } elseif (strlen($cpf) !== 11 || !ctype_digit($cpf)) {
        $erro = "CPF inválido. Digite somente os 11 números.";
    } elseif ($senha !== $confirm) {
        $erro = "As senhas não coincidem.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter ao menos 6 caracteres.";
    } else {
        $cpf_safe   = mysqli_real_escape_string($conexao, $cpf);
        $email_safe = mysqli_real_escape_string($conexao, $email);

        // Checa duplicidade
        $chk = mysqli_query($conexao, "SELECT id FROM alunos WHERE cpf='$cpf_safe' OR email='$email_safe' LIMIT 1");
        if (mysqli_num_rows($chk) > 0) {
            $erro = "CPF ou e-mail já cadastrado.";
        } else {
            $nome_safe  = mysqli_real_escape_string($conexao, $nome);
            $senha_md5  = md5($senha);
            $nasc_safe  = $nasc ? "'".mysqli_real_escape_string($conexao, $nasc)."'" : "NULL";
            $obj_safe   = mysqli_real_escape_string($conexao, $obj);

            $ins = mysqli_query($conexao,
                "INSERT INTO alunos (nome, email, cpf, senha, data_nascimento, objetivo)
                 VALUES ('$nome_safe', '$email_safe', '$cpf_safe', '$senha_md5', $nasc_safe, '$obj_safe')"
            );

            if ($ins) {
                $sucesso = "Cadastro realizado com sucesso! Redirecionando...";
                header("refresh:2;url=login.php");
            } else {
                $erro = "Erro ao cadastrar. Tente novamente.";
            }
        }
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-box fade-in" style="max-width:520px">
        <div class="auth-logo">ZENET</div>
        <div class="auth-subtitle">Cadastro de Aluno</div>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Nome Completo *</label>
                <input type="text" name="nome" placeholder="Seu nome completo"
                       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>CPF * (somente números)</label>
                    <input type="text" name="cpf" maxlength="11" placeholder="12345678900"
                           value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Data de Nascimento</label>
                    <input type="date" name="nascimento"
                           value="<?= htmlspecialchars($_POST['nascimento'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>E-mail *</label>
                <input type="email" name="email" placeholder="seu@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Objetivo</label>
                <select name="objetivo">
                    <option value="">Selecione...</option>
                    <option value="Hipertrofia" <?= (($_POST['objetivo'] ?? '') === 'Hipertrofia') ? 'selected' : '' ?>>Hipertrofia (ganho de massa)</option>
                    <option value="Emagrecimento" <?= (($_POST['objetivo'] ?? '') === 'Emagrecimento') ? 'selected' : '' ?>>Emagrecimento</option>
                    <option value="Condicionamento" <?= (($_POST['objetivo'] ?? '') === 'Condicionamento') ? 'selected' : '' ?>>Condicionamento Físico</option>
                    <option value="Força" <?= (($_POST['objetivo'] ?? '') === 'Força') ? 'selected' : '' ?>>Ganho de Força</option>
                    <option value="Saúde" <?= (($_POST['objetivo'] ?? '') === 'Saúde') ? 'selected' : '' ?>>Saúde e Qualidade de Vida</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Senha * (mín. 6 caracteres)</label>
                    <input type="password" name="senha" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label>Confirmar Senha *</label>
                    <input type="password" name="confirm" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Criar Conta</button>
        </form>

        <div class="auth-link">
            Já tem conta? <a href="login.php">Fazer login</a>
        </div>
    </div>
</div>
</body>
</html>