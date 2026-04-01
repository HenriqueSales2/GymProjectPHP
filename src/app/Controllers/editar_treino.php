<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZENET | Editar Treino</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
session_start();
require_once('../Models/auth.php');
requerProfessor();
require_once('../config/conexao.php');

$prof_id = $_SESSION['usuario_id'];
$tid     = (int)($_GET['id'] ?? 0);

if (!$tid) { header("Location: treinos.php"); exit(); }

// Verifica ownership
$treino = mysqli_fetch_assoc(mysqli_query($conexao,
    "SELECT t.*, a.nome AS aluno_nome, a.objetivo AS aluno_objetivo
     FROM treinos t JOIN alunos a ON a.id=t.aluno_id
     WHERE t.id=$tid AND t.professor_id=$prof_id"
));
if (!$treino) { header("Location: treinos.php"); exit(); }

$msg = ''; $msg_tipo = 'success';

// ============================================
// AÇÕES POST
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // ADICIONAR EXERCÍCIO
    if ($acao === 'adicionar') {
        $ex_id    = (int)($_POST['exercicio_id'] ?? 0);
        $series   = (int)($_POST['series']       ?? 3);
        $reps     = mysqli_real_escape_string($conexao, $_POST['repeticoes'] ?? '12');
        $carga    = trim($_POST['carga_kg'] ?? '');
        $carga_v  = $carga !== '' ? (float)$carga : 'NULL';
        $descanso = (int)($_POST['descanso_seg'] ?? 60);
        $obs      = mysqli_real_escape_string($conexao, $_POST['observacao'] ?? '');

        if ($ex_id) {
            // Pega próxima ordem
            $ordem = mysqli_fetch_assoc(mysqli_query($conexao,
                "SELECT COALESCE(MAX(ordem),0)+1 AS prox FROM treino_exercicios WHERE treino_id=$tid"
            ))['prox'];

            $carga_sql = is_numeric($carga_v) ? $carga_v : 'NULL';
            mysqli_query($conexao,
                "INSERT INTO treino_exercicios
                 (treino_id, exercicio_id, series, repeticoes, carga_kg, descanso_seg, observacao, ordem)
                 VALUES ($tid, $ex_id, $series, '$reps', $carga_sql, $descanso, '$obs', $ordem)"
            );
            $msg = "Exercício adicionado!";
        }
    }

    // REMOVER EXERCÍCIO
    if ($acao === 'remover') {
        $item_id = (int)($_POST['item_id'] ?? 0);
        mysqli_query($conexao, "DELETE FROM treino_exercicios WHERE id=$item_id AND treino_id=$tid");
        $msg = "Exercício removido.";
    }

    // ATUALIZAR ITEM
    if ($acao === 'atualizar_item') {
        $item_id  = (int)($_POST['item_id'] ?? 0);
        $series   = (int)($_POST['series']  ?? 3);
        $reps     = mysqli_real_escape_string($conexao, $_POST['repeticoes'] ?? '12');
        $carga    = trim($_POST['carga_kg'] ?? '');
        $carga_v  = $carga !== '' ? (float)$carga : 'NULL';
        $descanso = (int)($_POST['descanso_seg'] ?? 60);
        $obs      = mysqli_real_escape_string($conexao, $_POST['observacao'] ?? '');
        $carga_sql = is_numeric($carga_v) ? $carga_v : 'NULL';

        mysqli_query($conexao,
            "UPDATE treino_exercicios SET
             series=$series, repeticoes='$reps', carga_kg=$carga_sql,
             descanso_seg=$descanso, observacao='$obs'
             WHERE id=$item_id AND treino_id=$tid"
        );
        $msg = "Exercício atualizado!";
    }

    // ATUALIZAR INFO DO TREINO
    if ($acao === 'atualizar_treino') {
        $desc = mysqli_real_escape_string($conexao, $_POST['descricao'] ?? '');
        mysqli_query($conexao,
            "UPDATE treinos SET descricao='$desc' WHERE id=$tid AND professor_id=$prof_id"
        );
        $treino['descricao'] = htmlspecialchars_decode($desc);
        $msg = "Treino atualizado!";
    }
}

// Exercícios já no treino
$itens = mysqli_query($conexao,
    "SELECT te.*, e.nome AS ex_nome, e.descricao AS ex_desc, e.equipamento,
     gm.nome AS grupo_nome
     FROM treino_exercicios te
     JOIN exercicios e ON e.id = te.exercicio_id
     JOIN grupos_musculares gm ON gm.id = e.grupo_muscular_id
     WHERE te.treino_id = $tid
     ORDER BY te.ordem ASC"
);

// Catálogo completo agrupado por grupo muscular
$grupos_raw = mysqli_query($conexao, "SELECT * FROM grupos_musculares ORDER BY nome");
$grupos = [];
while ($g = mysqli_fetch_assoc($grupos_raw)) $grupos[$g['id']] = $g['nome'];

$exercicios_raw = mysqli_query($conexao,
    "SELECT e.*, gm.nome AS grupo_nome FROM exercicios e
     JOIN grupos_musculares gm ON gm.id = e.grupo_muscular_id
     ORDER BY gm.nome, e.nome"
);
$catalogo = [];
while ($ex = mysqli_fetch_assoc($exercicios_raw)) {
    $catalogo[$ex['grupo_nome']][] = $ex;
}
?>
<?php include('professor_navbar.php'); ?>

<div class="container">
    <!-- Header -->
    <div class="page-header fade-in">
        <div class="flex-between">
            <div>
                <h1>TREINO <span><?= htmlspecialchars($treino['letra_treino']) ?></span></h1>
                <p>
                    Aluno: <strong><?= htmlspecialchars($treino['aluno_nome']) ?></strong>
                    &nbsp;·&nbsp;
                    <span class="tag tag-accent"><?= htmlspecialchars($treino['tipo_divisao']) ?></span>
                    <?php if ($treino['aluno_objetivo']): ?>
                        &nbsp;·&nbsp; Objetivo: <span class="text-muted"><?= htmlspecialchars($treino['aluno_objetivo']) ?></span>
                    <?php endif; ?>
                </p>
            </div>
            <a href="treinos.php" class="btn btn-secondary">← Voltar</a>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_tipo ?> fade-in"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['novo'])): ?>
        <div class="alert alert-success fade-in">✅ Treino criado! Agora adicione os exercícios abaixo.</div>
    <?php endif; ?>

    <div class="grid grid-2 fade-in" style="align-items:start">

        <!-- COLUNA ESQUERDA: Exercícios do treino -->
        <div>
            <div class="card">
                <div class="card-header">
                    <span class="card-title">EXERCÍCIOS DO TREINO</span>
                    <span class="text-muted" id="contExer">
            <?= mysqli_num_rows($itens) ?> exercício(s)
          </span>
                </div>

                <div id="listaExercicios">
                    <?php if (mysqli_num_rows($itens) === 0): ?>
                        <div class="empty-state" id="emptyMsg">
                            <span class="icon">🏋️</span>
                            Nenhum exercício adicionado.<br>
                            <span class="text-muted" style="font-size:0.85rem">Use o catálogo ao lado →</span>
                        </div>
                    <?php else: ?>
                        <?php $ord = 1; while ($item = mysqli_fetch_assoc($itens)): ?>
                            <div class="exercise-item mb-1" id="item-<?= $item['id'] ?>">
                                <div class="exercise-order"><?= $ord++ ?></div>
                                <div class="exercise-info">
                                    <div class="exercise-name"><?= htmlspecialchars($item['ex_nome']) ?></div>
                                    <div class="exercise-meta">
                                        <span class="exercise-stat"><strong><?= $item['series'] ?>x</strong> séries</span>
                                        <span class="exercise-stat"><strong><?= htmlspecialchars($item['repeticoes']) ?></strong> reps</span>
                                        <?php if ($item['carga_kg']): ?>
                                            <span class="exercise-stat"><strong><?= $item['carga_kg'] ?>kg</strong></span>
                                        <?php endif; ?>
                                        <span class="exercise-stat"><?= $item['descanso_seg'] ?>s descanso</span>
                                        <span class="tag tag-muted" style="font-size:0.7rem"><?= htmlspecialchars($item['grupo_nome']) ?></span>
                                    </div>
                                    <?php if ($item['observacao']): ?>
                                        <div class="text-muted mt-1" style="font-size:0.8rem">📝 <?= htmlspecialchars($item['observacao']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="exercise-actions">
                                    <button class="btn btn-secondary btn-sm" onclick="abrirEditar(<?= htmlspecialchars(json_encode($item)) ?>)">✏️</button>
                                    <form method="post" onsubmit="return confirm('Remover?')" style="display:inline">
                                        <input type="hidden" name="acao" value="remover">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <button class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

                <!-- Editar descrição -->
                <hr class="divider">
                <form method="post">
                    <input type="hidden" name="acao" value="atualizar_treino">
                    <div class="form-group mb-1">
                        <label>Descrição / Foco do treino</label>
                        <input type="text" name="descricao"
                               value="<?= htmlspecialchars($treino['descricao'] ?? '') ?>"
                               placeholder="Ex: Peito e Tríceps">
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm">Salvar Descrição</button>
                </form>
            </div>
        </div>

        <!-- COLUNA DIREITA: Catálogo -->
        <div>
            <div class="card">
                <div class="card-header">
                    <span class="card-title">CATÁLOGO</span>
                </div>

                <div class="search-bar mb-2">
                    <input type="text" id="buscaEx" placeholder="Filtrar exercícios..." oninput="filtrarEx(this.value)">
                </div>

                <!-- Filtro por grupo -->
                <div style="display:flex;flex-wrap:wrap;gap:0.4rem;margin-bottom:1rem" id="btnGrupos">
                    <button class="tab-btn active" onclick="filtrarGrupo('', this)">Todos</button>
                    <?php foreach ($catalogo as $grupo => $exs): ?>
                        <button class="tab-btn" onclick="filtrarGrupo('<?= htmlspecialchars($grupo) ?>', this)">
                            <?= htmlspecialchars($grupo) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div id="listaExCatalogo" style="max-height:500px;overflow-y:auto">
                    <?php foreach ($catalogo as $grupo => $exs): ?>
                        <div class="grupo-bloco" data-grupo="<?= htmlspecialchars($grupo) ?>">
                            <p class="text-muted mb-1" style="font-size:0.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase">
                                <?= htmlspecialchars($grupo) ?>
                            </p>
                            <?php foreach ($exs as $ex): ?>
                                <div class="exercise-item mb-1 ex-item"
                                     data-nome="<?= strtolower($ex['nome']) ?>"
                                     data-grupo="<?= htmlspecialchars($grupo) ?>">
                                    <div class="exercise-info">
                                        <div class="exercise-name"><?= htmlspecialchars($ex['nome']) ?></div>
                                        <div class="exercise-meta">
                                            <span class="exercise-stat"><?= htmlspecialchars($ex['equipamento'] ?? '') ?></span>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm"
                                            onclick="abrirAdicionar(<?= $ex['id'] ?>, '<?= addslashes($ex['nome']) ?>')">
                                        + Add
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Adicionar Exercício -->
<div class="modal-overlay" id="modalAdd">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">ADICIONAR EXERCÍCIO</span>
            <button class="modal-close" onclick="fecharModal('modalAdd')">×</button>
        </div>
        <form method="post">
            <input type="hidden" name="acao" value="adicionar">
            <input type="hidden" name="exercicio_id" id="addExId">

            <div class="alert alert-info mb-2" id="addExNome" style="font-weight:700;font-size:1rem"></div>

            <div class="form-row">
                <div class="form-group">
                    <label>Séries</label>
                    <input type="number" name="series" value="3" min="1" max="10" required>
                </div>
                <div class="form-group">
                    <label>Repetições</label>
                    <input type="text" name="repeticoes" value="12" placeholder="Ex: 12 ou 8-12 ou Falha">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Carga (kg) — deixe vazio p/ peso corporal</label>
                    <input type="number" name="carga_kg" step="0.5" min="0" placeholder="Ex: 20">
                </div>
                <div class="form-group">
                    <label>Descanso (segundos)</label>
                    <input type="number" name="descanso_seg" value="60" min="0" max="600">
                </div>
            </div>

            <div class="form-group">
                <label>Observação (opcional)</label>
                <input type="text" name="observacao" placeholder="Ex: Executar lentamente">
            </div>

            <div style="display:flex;gap:0.75rem;justify-content:flex-end">
                <button type="button" class="btn btn-secondary" onclick="fecharModal('modalAdd')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Adicionar ao Treino</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Editar Item -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">EDITAR EXERCÍCIO</span>
            <button class="modal-close" onclick="fecharModal('modalEdit')">×</button>
        </div>
        <form method="post">
            <input type="hidden" name="acao" value="atualizar_item">
            <input type="hidden" name="item_id" id="editItemId">

            <div class="alert alert-info mb-2" id="editExNome" style="font-weight:700;font-size:1rem"></div>

            <div class="form-row">
                <div class="form-group">
                    <label>Séries</label>
                    <input type="number" name="series" id="editSeries" min="1" max="10" required>
                </div>
                <div class="form-group">
                    <label>Repetições</label>
                    <input type="text" name="repeticoes" id="editReps">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Carga (kg)</label>
                    <input type="number" name="carga_kg" id="editCarga" step="0.5" min="0">
                </div>
                <div class="form-group">
                    <label>Descanso (seg)</label>
                    <input type="number" name="descanso_seg" id="editDescanso" min="0" max="600">
                </div>
            </div>

            <div class="form-group">
                <label>Observação</label>
                <input type="text" name="observacao" id="editObs">
            </div>

            <div style="display:flex;gap:0.75rem;justify-content:flex-end">
                <button type="button" class="btn btn-secondary" onclick="fecharModal('modalEdit')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
    function fecharModal(id) {
        document.getElementById(id).classList.remove('open');
    }
    function abrirModal(id) {
        document.getElementById(id).classList.add('open');
    }

    // Fechar clicando fora
    ['modalAdd','modalEdit'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) fecharModal(id);
        });
    });

    function abrirAdicionar(exId, exNome) {
        document.getElementById('addExId').value = exId;
        document.getElementById('addExNome').textContent = exNome;
        abrirModal('modalAdd');
    }

    function abrirEditar(item) {
        document.getElementById('editItemId').value   = item.id;
        document.getElementById('editExNome').textContent = item.ex_nome;
        document.getElementById('editSeries').value   = item.series;
        document.getElementById('editReps').value     = item.repeticoes;
        document.getElementById('editCarga').value    = item.carga_kg || '';
        document.getElementById('editDescanso').value = item.descanso_seg;
        document.getElementById('editObs').value      = item.observacao || '';
        abrirModal('modalEdit');
    }

    // Busca no catálogo
    function filtrarEx(q) {
        q = q.toLowerCase();
        document.querySelectorAll('.ex-item').forEach(el => {
            el.style.display = el.dataset.nome.includes(q) ? '' : 'none';
        });
        document.querySelectorAll('.grupo-bloco').forEach(bloco => {
            const visivel = [...bloco.querySelectorAll('.ex-item')].some(e => e.style.display !== 'none');
            bloco.style.display = visivel ? '' : 'none';
        });
    }

    // Filtro por grupo
    function filtrarGrupo(grupo, btn) {
        document.querySelectorAll('#btnGrupos .tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('buscaEx').value = '';
        document.querySelectorAll('.ex-item').forEach(el => {
            el.style.display = (!grupo || el.dataset.grupo === grupo) ? '' : 'none';
        });
        document.querySelectorAll('.grupo-bloco').forEach(bloco => {
            bloco.style.display = (!grupo || bloco.dataset.grupo === grupo) ? '' : 'none';
        });
    }
</script>
</body>
</html>