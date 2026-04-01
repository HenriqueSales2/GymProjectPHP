<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZENET | Professor</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
session_start();
require_once('../Models/auth.php');
requerProfessor();
require_once('../config/conexao.php');

$prof_id = $_SESSION['usuario_id'];
$nome    = $_SESSION['nome'];

// Stats
$total_alunos = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as n FROM alunos"))['n'];
$total_treinos = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as n FROM treinos WHERE professor_id = $prof_id"))['n'];
$total_exercicios = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as n FROM exercicios"))['n'];

// Últimos treinos montados pelo professor
$ultimos = mysqli_query($conexao,
    "SELECT t.*, a.nome AS aluno_nome
     FROM treinos t
     JOIN alunos a ON a.id = t.aluno_id
     WHERE t.professor_id = $prof_id
     ORDER BY t.data_atualizacao DESC
     LIMIT 8"
);
?>

<?php include('navbar.php'); ?>

<div class="container">
    <div class="page-header fade-in">
        <h1>OLÁ, <span><?= strtoupper(explode(' ', $nome)[0]) ?></span></h1>
        <p>Painel do professor — gerencie os treinos dos seus alunos</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-4 mb-3 fade-in">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_alunos ?></div>
                <div class="stat-label">Alunos</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_treinos ?></div>
                <div class="stat-label">Meus Treinos</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏋️</div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_exercicios ?></div>
                <div class="stat-label">Exercícios</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚡</div>
            <div class="stat-info">
                <div class="stat-value">4</div>
                <div class="stat-label">Divisões</div>
            </div>
        </div>
    </div>

    <!-- Ações rápidas -->
    <div class="grid grid-2 mb-3 fade-in">
        <div class="card">
            <div class="card-header">
                <span class="card-title">AÇÕES RÁPIDAS</span>
            </div>
            <div class="grid" style="grid-template-columns:1fr 1fr; gap:0.75rem">
                <a href="treinos.php" class="btn btn-primary">📋 Gerenciar Treinos</a>
                <a href="alunos.php" class="btn btn-secondary">👥 Ver Alunos</a>
                <a href="treinos.php?acao=novo" class="btn btn-secondary">➕ Novo Treino</a>
                <a href="exercicios.php" class="btn btn-secondary">🏋️ Catálogo</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">DIVISÕES DISPONÍVEIS</span>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem">
                <span class="tag tag-accent">A/B/C/D/E</span>
                <span class="tag tag-accent">Upper / Lower</span>
                <span class="tag tag-accent">Full Body</span>
                <span class="tag tag-accent">Push / Pull / Legs</span>
            </div>
            <p class="text-muted mt-2" style="font-size:0.85rem">
                Monte qualquer divisão para os alunos, escolhendo exercícios do catálogo.
            </p>
        </div>
    </div>

    <!-- Últimos treinos -->
    <div class="card fade-in">
        <div class="card-header">
            <span class="card-title">ÚLTIMOS TREINOS</span>
            <a href="treinos.php" class="btn btn-secondary btn-sm">Ver todos</a>
        </div>

        <?php if (mysqli_num_rows($ultimos) === 0): ?>
            <div class="empty-state">
                <span class="icon">📋</span>
                Nenhum treino montado ainda. <a href="treinos.php?acao=novo" class="text-accent">Criar o primeiro!</a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Aluno</th>
                        <th>Divisão</th>
                        <th>Treino</th>
                        <th>Atualizado</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($t = mysqli_fetch_assoc($ultimos)): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['aluno_nome']) ?></td>
                            <td><span class="tag tag-accent"><?= htmlspecialchars($t['tipo_divisao']) ?></span></td>
                            <td><strong><?= htmlspecialchars($t['letra_treino']) ?></strong>
                                <?php if ($t['descricao']): ?>
                                    <span class="text-muted"> — <?= htmlspecialchars($t['descricao']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= date('d/m/Y', strtotime($t['data_atualizacao'])) ?></td>
                            <td>
                                <a href="editar_treino.php?id=<?= $t['id'] ?>" class="btn btn-secondary btn-sm">✏️ Editar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>