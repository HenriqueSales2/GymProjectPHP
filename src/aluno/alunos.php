<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZENET | Alunos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
session_start();
require_once('../Model/auth.php');
requerProfessor();
require_once('../Config/conexao.php');

$busca = trim($_GET['busca'] ?? '');
$where = '';
if ($busca) {
    $b = mysqli_real_escape_string($conexao, $busca);
    $where = "WHERE nome LIKE '%$b%' OR cpf LIKE '%$b%' OR email LIKE '%$b%'";
}

$alunos = mysqli_query($conexao,
    "SELECT a.*,
     (SELECT COUNT(*) FROM treinos WHERE aluno_id = a.id) AS total_treinos
     FROM alunos a $where
     ORDER BY a.nome ASC"
);
?>
<?php include('professor_navbar.php'); ?>

<div class="container">
    <div class="page-header flex-between fade-in">
        <div>
            <h1>ALUNOS</h1>
            <p>Selecione um aluno para gerenciar seus treinos</p>
        </div>
    </div>

    <!-- Busca -->
    <div class="card mb-3 fade-in">
        <form method="get" style="display:flex;gap:1rem;align-items:flex-end">
            <div class="form-group w-full mb-1">
                <label>Buscar aluno</label>
                <input type="text" name="busca" placeholder="Nome, CPF ou e-mail..."
                       value="<?= htmlspecialchars($busca) ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="height:42px">Buscar</button>
            <?php if ($busca): ?>
                <a href="alunos.php" class="btn btn-secondary" style="height:42px">Limpar</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Lista -->
    <div class="card fade-in">
        <div class="card-header">
            <span class="card-title">LISTA DE ALUNOS</span>
            <span class="text-muted"><?= mysqli_num_rows($alunos) ?> encontrado(s)</span>
        </div>

        <?php if (mysqli_num_rows($alunos) === 0): ?>
            <div class="empty-state">
                <span class="icon">👥</span>
                Nenhum aluno encontrado.
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Objetivo</th>
                        <th>Treinos</th>
                        <th>Desde</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; while ($a = mysqli_fetch_assoc($alunos)): ?>
                        <tr>
                            <td class="text-muted"><?= $i++ ?></td>
                            <td><strong><?= htmlspecialchars($a['nome']) ?></strong></td>
                            <td class="text-muted"><?= $a['cpf'] ?></td>
                            <td>
                                <?php if ($a['objetivo']): ?>
                                    <span class="tag tag-muted"><?= htmlspecialchars($a['objetivo']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
              <span class="tag <?= $a['total_treinos'] > 0 ? 'tag-success' : 'tag-muted' ?>">
                <?= $a['total_treinos'] ?> treinos
              </span>
                            </td>
                            <td class="text-muted"><?= date('d/m/Y', strtotime($a['criado_em'])) ?></td>
                            <td>
                                <a href="treinos.php?aluno_id=<?= $a['id'] ?>" class="btn btn-primary btn-sm">📋 Treinos</a>
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