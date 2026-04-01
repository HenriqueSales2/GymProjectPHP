<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZENET | Catálogo de Exercícios</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
session_start();
require_once('../Models/auth.php');
requerProfessor();
require_once('../config/conexao.php');

$grupo_filtro = (int)($_GET['grupo'] ?? 0);
$busca        = trim($_GET['busca'] ?? '');

$where = [];
if ($grupo_filtro) $where[] = "e.grupo_muscular_id = $grupo_filtro";
if ($busca) {
    $b = mysqli_real_escape_string($conexao, $busca);
    $where[] = "(e.nome LIKE '%$b%' OR e.descricao LIKE '%$b%' OR e.equipamento LIKE '%$b%')";
}
$sql_where = $where ? "WHERE " . implode(' AND ', $where) : '';

$exercicios = mysqli_query($conexao,
    "SELECT e.*, gm.nome AS grupo_nome
     FROM exercicios e
     JOIN grupos_musculares gm ON gm.id = e.grupo_muscular_id
     $sql_where
     ORDER BY gm.nome, e.nome"
);
$grupos = mysqli_query($conexao, "SELECT * FROM grupos_musculares ORDER BY nome");
?>
<?php include('professor_navbar.php'); ?>

<div class="container">
    <div class="page-header fade-in">
        <h1>CATÁLOGO DE <span>EXERCÍCIOS</span></h1>
        <p><?= mysqli_num_rows($exercicios) ?> exercícios disponíveis no sistema</p>
    </div>

    <!-- Filtros -->
    <div class="card mb-3 fade-in">
        <form method="get" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">
            <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0">
                <label>Buscar</label>
                <input type="text" name="busca" placeholder="Nome ou equipamento..."
                       value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="form-group" style="min-width:200px;margin-bottom:0">
                <label>Grupo Muscular</label>
                <select name="grupo">
                    <option value="">Todos os grupos</option>
                    <?php while ($g = mysqli_fetch_assoc($grupos)): ?>
                        <option value="<?= $g['id'] ?>" <?= ($grupo_filtro == $g['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['nome']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="exercicios.php" class="btn btn-secondary">Limpar</a>
        </form>
    </div>

    <!-- Tabela -->
    <div class="card fade-in">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Exercício</th>
                    <th>Grupo Muscular</th>
                    <th>Equipamento</th>
                    <th>Descrição</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; while ($ex = mysqli_fetch_assoc($exercicios)): ?>
                    <tr>
                        <td class="text-muted"><?= $i++ ?></td>
                        <td><strong><?= htmlspecialchars($ex['nome']) ?></strong></td>
                        <td><span class="tag tag-accent"><?= htmlspecialchars($ex['grupo_nome']) ?></span></td>
                        <td class="text-muted"><?= htmlspecialchars($ex['equipamento'] ?? '—') ?></td>
                        <td class="text-muted" style="font-size:0.85rem;max-width:300px">
                            <?= htmlspecialchars($ex['descricao'] ?? '') ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>