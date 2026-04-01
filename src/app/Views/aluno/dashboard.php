<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZENET | Minha Área</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
session_start();
require_once('../Models/auth.php');
requerAluno();
require_once('../config/conexao.php');

$aluno_id = $_SESSION['usuario_id'];
$nome     = $_SESSION['nome'];

// Info do aluno
$aluno = mysqli_fetch_assoc(mysqli_query($conexao,
    "SELECT * FROM alunos WHERE id=$aluno_id"));

// Divisões com treinos
$divisoes = mysqli_query($conexao,
    "SELECT tipo_divisao, COUNT(*) as qtd
     FROM treinos WHERE aluno_id=$aluno_id AND ativo=1
     GROUP BY tipo_divisao ORDER BY tipo_divisao"
);

// Próximos treinos (todos os treinos organizados)
$treinos = mysqli_query($conexao,
    "SELECT t.*,
     (SELECT COUNT(*) FROM treino_exercicios te WHERE te.treino_id=t.id) AS qtd_ex
     FROM treinos t
     WHERE t.aluno_id=$aluno_id AND t.ativo=1
     ORDER BY t.tipo_divisao, t.letra_treino"
);
$total_treinos = mysqli_num_rows($treinos);
mysqli_data_seek($treinos, 0);

$total_ex = mysqli_fetch_assoc(mysqli_query($conexao,
    "SELECT COALESCE(SUM(
      (SELECT COUNT(*) FROM treino_exercicios te WHERE te.treino_id=t.id)
     ),0) AS n FROM treinos t WHERE t.aluno_id=$aluno_id"
))['n'];
?>
<?php include('navbar.php'); ?>

<div class="container">
    <!-- Saudação -->
    <div class="page-header fade-in">
        <h1>OLÁ, <span><?= strtoupper(explode(' ', $nome)[0]) ?></span> 💪</h1>
        <p>
            <?= htmlspecialchars($aluno['objetivo'] ?? 'Bem-vindo à ZENET Academia') ?>
        </p>
    </div>

    <!-- Stats -->
    <div class="grid grid-3 mb-3 fade-in">
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_treinos ?></div>
                <div class="stat-label">Treinos</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏋️</div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_ex ?></div>
                <div class="stat-label">Exercícios</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-info">
                <div class="stat-value"><?= mysqli_num_rows($divisoes) ?></div>
                <div class="stat-label">Divisões</div>
            </div>
        </div>
    </div>

    <?php if ($total_treinos === 0): ?>
        <div class="card fade-in">
            <div class="empty-state">
                <span class="icon">⏳</span>
                <strong>Aguardando seu professor</strong><br>
                <span class="text-muted">Seu professor ainda não cadastrou nenhum treino para você.</span>
            </div>
        </div>
    <?php else: ?>

    <!-- Treinos organizados por divisão -->
    <div class="card fade-in">
        <div class="card-header">
            <span class="card-title">MEUS TREINOS</span>
            <a href="meus_treinos.php" class="btn btn-secondary btn-sm">Ver detalhes</a>
        </div>

        <?php
        $divisao_atual = null;
        mysqli_data_seek($treinos, 0);
        while ($t = mysqli_fetch_assoc($treinos)):
        if ($t['tipo_divisao'] !== $divisao_atual):
        if ($divisao_atual !== null) echo '</div>';
        $divisao_atual = $t['tipo_divisao'];
        ?>
        <p class="text-muted mb-1 mt-2" style="font-size:0.75rem;font-weight:700;letter-spacing:2px;text-transform:uppercase">
            📂 <?= htmlspecialchars($t['tipo_divisao']) ?>
        </p>
        <div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem">
            <?php endif; ?>

            <a href="ver_treino.php?id=<?= $t['id'] ?>" style="text-decoration:none">
                <div class="card" style="cursor:pointer;border-color:var(--border)">
                    <div style="font-family:var(--font-display);font-size:2.5rem;color:var(--accent);line-height:1">
                        <?= htmlspecialchars($t['letra_treino']) ?>
                    </div>
                    <div style="font-weight:700;margin-top:0.3rem"><?= htmlspecialchars($t['descricao'] ?: $t['tipo_divisao']) ?></div>
                    <div class="text-muted" style="font-size:0.8rem;margin-top:0.3rem">
                        <?= $t['qtd_ex'] ?> exercícios
                    </div>
                </div>
            </a>

            <?php endwhile; ?>
            <?php if ($divisao_atual) echo '</div>'; ?>
        </div>

        <?php endif; ?>
    </div>
</body>
</html>