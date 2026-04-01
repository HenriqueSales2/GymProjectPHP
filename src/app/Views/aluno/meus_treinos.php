<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZENET | Meus Treinos</title>
    <link rel="stylesheet" href="/GymProjectPHP/src/public/css/style.css">
</head>
<body>
<?php
session_start();
require_once __DIR__ . '/../../Models/auth.php';
requerAluno();
require_once __DIR__ . '/../../../config/conexao.php';

$aluno_id = $_SESSION['usuario_id'];

// Todos os treinos agrupados
$treinos = mysqli_query($conexao,
    "SELECT t.*,
     (SELECT COUNT(*) FROM treino_exercicios te WHERE te.treino_id=t.id) AS qtd_ex
     FROM treinos t
     WHERE t.aluno_id=$aluno_id AND t.ativo=1
     ORDER BY t.tipo_divisao, t.letra_treino"
);

// Agrupar por divisão
$por_divisao = [];
while ($t = mysqli_fetch_assoc($treinos)) {
    $por_divisao[$t['tipo_divisao']][] = $t;
}
?>

<?php include __DIR__ . '/../layouts/aluno/navbar.php'; ?>

<div class="container">
    <div class="page-header fade-in">
        <h1>MEUS <span>TREINOS</span></h1>
        <p>Todos os seus treinos organizados por divisão</p>
    </div>

    <?php if (empty($por_divisao)): ?>
        <div class="card fade-in">
            <div class="empty-state">
                <span class="icon">⏳</span>
                Seu professor ainda não cadastrou nenhum treino para você.
            </div>
        </div>
    <?php else: ?>

        <!-- Tabs de divisão -->
        <div class="tabs fade-in" id="tabsDivisao">
            <?php $primeiro = true; foreach ($por_divisao as $div => $treinos_div): ?>
                <button class="tab-btn <?= $primeiro ? 'active' : '' ?>"
                        onclick="mostrarDivisao('<?= htmlspecialchars($div) ?>', this)">
                    <?= htmlspecialchars($div) ?>
                </button>
                <?php $primeiro = false; endforeach; ?>
        </div>

        <?php $primeiro = true; foreach ($por_divisao as $div => $treinos_div): ?>
            <div class="tab-div fade-in <?= $primeiro ? 'active' : '' ?>"
                 id="div-<?= htmlspecialchars(str_replace(['/','.',' '], '-', $div)) ?>">

                <div class="grid grid-3">
                    <?php foreach ($treinos_div as $t): ?>
                        <div class="card" style="cursor:pointer" onclick="location.href='ver_treino.php?id=<?= $t['id'] ?>'">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem">
                                <div style="font-family:var(--font-display);font-size:3rem;color:var(--accent);line-height:1">
                                    <?= htmlspecialchars($t['letra_treino']) ?>
                                </div>
                                <span class="tag tag-accent"><?= htmlspecialchars($div) ?></span>
                            </div>
                            <div style="font-weight:700;font-size:1rem">
                                <?= htmlspecialchars($t['descricao'] ?: 'Treino '.$t['letra_treino']) ?>
                            </div>
                            <div class="text-muted mt-1" style="font-size:0.85rem">
                                🏋️ <?= $t['qtd_ex'] ?> exercícios
                            </div>
                            <div class="text-muted" style="font-size:0.8rem">
                                Atualizado: <?= date('d/m/Y', strtotime($t['data_atualizacao'])) ?>
                            </div>
                            <div class="mt-2">
                                <span class="btn btn-primary btn-sm">Ver Treino →</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php $primeiro = false; endforeach; ?>

    <?php endif; ?>
</div>

<script>
    function mostrarDivisao(div, btn) {
        document.querySelectorAll('.tab-div').forEach(d => d.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        const id = 'div-' + div.replace(/[\/\. ]/g, '-');
        const el = document.getElementById(id);
        if (el) el.classList.add('active');
        btn.classList.add('active');
    }

    // Ativar primeira tab
    document.addEventListener('DOMContentLoaded', () => {
        const first = document.querySelector('.tab-div');
        if (first) first.classList.add('active');
    });
</script>
</body>
</html>