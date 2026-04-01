<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZENET | Treinos</title>
    <link rel="stylesheet" href="/GymProjectPHP/src/public/css/style.css">
</head>
<body>
<?php
session_start();
require_once __DIR__ . '/../../Models/auth.php';
requerProfessor();
require_once __DIR__ . '/../../../config/conexao.php';

$prof_id = $_SESSION['usuario_id'];
$msg     = '';
$msg_tipo = 'success';

// ============================================
// AÇÕES POST (criar treino)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'criar_treino') {
        $aluno_id    = (int)($_POST['aluno_id'] ?? 0);
        $tipo_div    = mysqli_real_escape_string($conexao, $_POST['tipo_divisao'] ?? '');
        $letra       = mysqli_real_escape_string($conexao, $_POST['letra_treino'] ?? '');
        $descricao   = mysqli_real_escape_string($conexao, $_POST['descricao'] ?? '');

        if ($aluno_id && $tipo_div && $letra) {
            mysqli_query($conexao,
                "INSERT INTO treinos (aluno_id, professor_id, tipo_divisao, letra_treino, descricao)
                 VALUES ($aluno_id, $prof_id, '$tipo_div', '$letra', '$descricao')"
            );
            $novo_id = mysqli_insert_id($conexao);
            header("Location: editar_treino.php?id=$novo_id&novo=1");
            exit();
        } else {
            $msg = "Preencha todos os campos obrigatórios.";
            $msg_tipo = 'danger';
        }
    }

    if ($acao === 'deletar_treino') {
        $tid = (int)($_POST['treino_id'] ?? 0);
        // Verifica se o professor é dono
        $r = mysqli_fetch_assoc(mysqli_query($conexao,
            "SELECT id FROM treinos WHERE id=$tid AND professor_id=$prof_id"));
        if ($r) {
            mysqli_query($conexao, "DELETE FROM treino_exercicios WHERE treino_id=$tid");
            mysqli_query($conexao, "DELETE FROM treinos WHERE id=$tid");
            $msg = "Treino excluído com sucesso.";
        }
    }
}

// Filtro por aluno
$filtro_aluno = (int)($_GET['aluno_id'] ?? 0);
$aluno_info   = null;
if ($filtro_aluno) {
    $aluno_info = mysqli_fetch_assoc(mysqli_query($conexao,
        "SELECT * FROM alunos WHERE id=$filtro_aluno"));
}

// Lista de alunos para o select
$lista_alunos = mysqli_query($conexao, "SELECT id, nome FROM alunos ORDER BY nome");

// Lista de treinos
$where_treino = $filtro_aluno ? "WHERE t.aluno_id=$filtro_aluno AND t.professor_id=$prof_id" : "WHERE t.professor_id=$prof_id";
$treinos = mysqli_query($conexao,
    "SELECT t.*, a.nome AS aluno_nome,
     (SELECT COUNT(*) FROM treino_exercicios te WHERE te.treino_id = t.id) AS qtd_exercicios
     FROM treinos t
     JOIN alunos a ON a.id = t.aluno_id
     $where_treino
     ORDER BY a.nome, t.tipo_divisao, t.letra_treino"
);

// Divisões e letras
$divisoes = [
    'A/B/C/D/E'       => ['A','B','C','D','E'],
    'Upper/Lower'      => ['Upper','Lower'],
    'Full Body'        => ['Full Body', 'Full Body A', 'Full Body B'],
    'Push/Pull/Legs'   => ['Push','Pull','Legs'],
];
?>

<?php include __DIR__ . '/../layouts/professor/navbar.php'; ?>

<div class="container">
    <div class="page-header flex-between fade-in">
        <div>
            <h1><?= $aluno_info ? 'TREINOS DE <span>'.strtoupper($aluno_info['nome']).'</span>' : 'TREINOS' ?></h1>
            <p>Crie e gerencie os treinos dos seus alunos</p>
        </div>
        <button class="btn btn-primary" onclick="abrirModal()">➕ Novo Treino</button>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_tipo ?> fade-in"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if ($filtro_aluno): ?>
        <div class="alert alert-info fade-in">
            Filtrando treinos de: <strong><?= htmlspecialchars($aluno_info['nome'] ?? 'Aluno') ?></strong>
            &nbsp; <a href="treinos.php" class="text-accent">Ver todos</a>
        </div>
    <?php endif; ?>

    <!-- Tabela de treinos -->
    <div class="card fade-in">
        <div class="card-header">
            <span class="card-title">LISTA DE TREINOS</span>
            <span class="text-muted"><?= mysqli_num_rows($treinos) ?> treino(s)</span>
        </div>

        <?php if (mysqli_num_rows($treinos) === 0): ?>
            <div class="empty-state">
                <span class="icon">📋</span>
                Nenhum treino encontrado. <br>
                <button class="btn btn-primary mt-2" onclick="abrirModal()">Criar primeiro treino</button>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Aluno</th>
                        <th>Divisão</th>
                        <th>Treino</th>
                        <th>Descrição</th>
                        <th>Exercícios</th>
                        <th>Criado</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($t = mysqli_fetch_assoc($treinos)): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($t['aluno_nome']) ?></strong></td>
                            <td><span class="tag tag-accent"><?= htmlspecialchars($t['tipo_divisao']) ?></span></td>
                            <td>
                                <strong style="color:var(--accent);font-size:1.1rem"><?= htmlspecialchars($t['letra_treino']) ?></strong>
                            </td>
                            <td class="text-muted"><?= htmlspecialchars($t['descricao'] ?: '—') ?></td>
                            <td>
              <span class="tag <?= $t['qtd_exercicios'] > 0 ? 'tag-success' : 'tag-muted' ?>">
                <?= $t['qtd_exercicios'] ?> ex.
              </span>
                            </td>
                            <td class="text-muted"><?= date('d/m/Y', strtotime($t['data_criacao'])) ?></td>
                            <td style="display:flex;gap:0.4rem;flex-wrap:wrap">
                                <a href="editar_treino.php?id=<?= $t['id'] ?>" class="btn btn-secondary btn-sm">✏️ Editar</a>
                                <form method="post" onsubmit="return confirm('Excluir este treino?')">
                                    <input type="hidden" name="acao" value="deletar_treino">
                                    <input type="hidden" name="treino_id" value="<?= $t['id'] ?>">
                                    <button class="btn btn-danger btn-sm">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Novo Treino -->
<div class="modal-overlay" id="modalNovo">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">NOVO TREINO</span>
            <button class="modal-close" onclick="fecharModal()">×</button>
        </div>

        <form method="post">
            <input type="hidden" name="acao" value="criar_treino">

            <div class="form-group">
                <label>Aluno *</label>
                <select name="aluno_id" required>
                    <option value="">Selecione o aluno...</option>
                    <?php
                    mysqli_data_seek($lista_alunos, 0);
                    while ($al = mysqli_fetch_assoc($lista_alunos)):
                        ?>
                        <option value="<?= $al['id'] ?>"
                            <?= ($filtro_aluno == $al['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($al['nome']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Divisão *</label>
                    <select name="tipo_divisao" id="selDivisao" required onchange="atualizarLetras()">
                        <option value="">Selecione...</option>
                        <?php foreach ($divisoes as $div => $letras): ?>
                            <option value="<?= $div ?>"><?= $div ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Treino (Letra) *</label>
                    <select name="letra_treino" id="selLetra" required>
                        <option value="">— primeiro escolha a divisão —</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Descrição / Foco (opcional)</label>
                <input type="text" name="descricao" placeholder="Ex: Peito e Tríceps, Pernas, etc.">
            </div>

            <div style="display:flex;gap:0.75rem;justify-content:flex-end">
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar e Montar Treino ▶</button>
            </div>
        </form>
    </div>
</div>

<script>
    const divisoesLetras = <?= json_encode($divisoes) ?>;

    function abrirModal() {
        document.getElementById('modalNovo').classList.add('open');
    }
    function fecharModal() {
        document.getElementById('modalNovo').classList.remove('open');
    }
    document.getElementById('modalNovo').addEventListener('click', function(e) {
        if (e.target === this) fecharModal();
    });

    function atualizarLetras() {
        const div   = document.getElementById('selDivisao').value;
        const sel   = document.getElementById('selLetra');
        sel.innerHTML = '<option value="">Selecione...</option>';
        if (divisoesLetras[div]) {
            divisoesLetras[div].forEach(l => {
                sel.innerHTML += `<option value="${l}">${l}</option>`;
            });
        }
    }

    // Abre modal automaticamente se vier com ?acao=novo
    <?php if (($_GET['acao'] ?? '') === 'novo'): ?>
    abrirModal();
    <?php endif; ?>
</script>
</body>
</html>