<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZENET | Treino</title>
    <link rel="stylesheet" href="/GymProjectPHP/src/public/css/style.css">
    <style>
        .exercise-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            margin-bottom: 0.75rem;
            transition: var(--transition);
        }
        .exercise-card:hover { border-color: var(--accent); }
        .exercise-card.done { border-color: var(--success); opacity: 0.7; }
        .exercise-card.done .ex-check { color: var(--success); }

        .ex-number {
            font-family: var(--font-display);
            font-size: 3rem;
            color: var(--accent);
            line-height: 1;
            min-width: 50px;
        }
        .sets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.5rem;
            margin-top: 0.75rem;
        }
        .set-box {
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.8rem;
        }
        .set-box.done {
            background: rgba(46,204,113,0.15);
            border-color: var(--success);
            color: var(--success);
        }
        .set-box .set-num {
            font-family: var(--font-display);
            font-size: 1.3rem;
            color: var(--accent);
        }
        .set-box.done .set-num { color: var(--success); }

        .timer-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg2);
            border-top: 2px solid var(--accent);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            z-index: 50;
            transform: translateY(100%);
            transition: transform 0.3s;
        }
        .timer-bar.show { transform: translateY(0); }
        .timer-display {
            font-family: var(--font-display);
            font-size: 2rem;
            color: var(--accent);
            min-width: 80px;
        }
    </style>
</head>
<body>

<?php
session_start();
require_once __DIR__ . '/../../Models/auth.php';
requerAluno();
require_once __DIR__ . '/../../../config/conexao.php';

$aluno_id = $_SESSION['usuario_id'];
$tid      = (int)($_GET['id'] ?? 0);

if (!$tid) { header("Location: meus_treinos.php"); exit(); }

// Busca treino (só do próprio aluno)
$treino = mysqli_fetch_assoc(mysqli_query($conexao,
    "SELECT t.* FROM treinos t WHERE t.id=$tid AND t.aluno_id=$aluno_id AND t.ativo=1"
));
if (!$treino) { header("Location: meus_treinos.php"); exit(); }

// Exercícios
$itens = mysqli_query($conexao,
    "SELECT te.*, e.nome AS ex_nome, e.descricao AS ex_desc, e.equipamento,
     gm.nome AS grupo_nome
     FROM treino_exercicios te
     JOIN exercicios e ON e.id = te.exercicio_id
     JOIN grupos_musculares gm ON gm.id = e.grupo_muscular_id
     WHERE te.treino_id=$tid
     ORDER BY te.ordem ASC"
);
$exercicios_arr = [];
while ($ex = mysqli_fetch_assoc($itens)) $exercicios_arr[] = $ex;
?>

<?php include __DIR__ . '/../layouts/aluno/navbar.php'; ?>

<div class="container" style="padding-bottom:5rem">
    <!-- Header do treino -->
    <div class="page-header fade-in">
        <div class="flex-between">
            <div>
                <h1>TREINO <span><?= htmlspecialchars($treino['letra_treino']) ?></span></h1>
                <p>
                    <span class="tag tag-accent"><?= htmlspecialchars($treino['tipo_divisao']) ?></span>
                    <?php if ($treino['descricao']): ?>
                        &nbsp; <?= htmlspecialchars($treino['descricao']) ?>
                    <?php endif; ?>
                </p>
            </div>
            <div style="display:flex;gap:0.75rem">
                <button class="btn btn-primary" onclick="resetarTreino()">🔄 Reiniciar</button>
                <a href="meus_treinos.php" class="btn btn-secondary">← Voltar</a>
            </div>
        </div>
    </div>

    <!-- Barra de progresso -->
    <div class="card mb-3 fade-in">
        <div class="flex-between mb-1">
            <span class="text-muted" style="font-size:0.85rem">PROGRESSO DO TREINO</span>
            <span id="progressoTexto" class="text-muted" style="font-size:0.85rem">
        0 / <?= count($exercicios_arr) ?> exercícios
      </span>
        </div>
        <div style="background:var(--bg3);border-radius:20px;height:8px;overflow:hidden">
            <div id="progressoBar" style="background:var(--accent);height:100%;width:0%;border-radius:20px;transition:width 0.4s ease"></div>
        </div>
    </div>

    <?php if (empty($exercicios_arr)): ?>
        <div class="card fade-in">
            <div class="empty-state">
                <span class="icon">🏋️</span>
                Seu professor ainda não adicionou exercícios a este treino.
            </div>
        </div>
    <?php else: ?>

        <!-- Lista de exercícios -->
        <?php foreach ($exercicios_arr as $i => $ex): ?>
            <div class="exercise-card fade-in" id="excard-<?= $i ?>"
                 style="animation-delay:<?= $i * 0.05 ?>s">
                <div style="display:flex;align-items:flex-start;gap:1rem">
                    <div class="ex-number"><?= $i + 1 ?></div>
                    <div style="flex:1">
                        <div style="display:flex;align-items:center;justify-content:space-between">
                            <div>
                                <div style="font-weight:700;font-size:1.05rem"><?= htmlspecialchars($ex['ex_nome']) ?></div>
                                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-top:0.3rem">
                                    <span class="tag tag-muted"><?= htmlspecialchars($ex['grupo_nome']) ?></span>
                                    <?php if ($ex['equipamento']): ?>
                                        <span class="tag tag-muted">🔧 <?= htmlspecialchars($ex['equipamento']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div style="text-align:right">
                                <div style="font-family:var(--font-display);font-size:1.5rem;color:var(--accent)">
                                    <?= $ex['series'] ?>×<?= htmlspecialchars($ex['repeticoes']) ?>
                                </div>
                                <?php if ($ex['carga_kg']): ?>
                                    <div class="text-muted" style="font-size:0.85rem">⚖️ <?= $ex['carga_kg'] ?>kg</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($ex['ex_desc']): ?>
                            <p class="text-muted mt-1" style="font-size:0.85rem">
                                📌 <?= htmlspecialchars($ex['ex_desc']) ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($ex['observacao']): ?>
                            <p style="color:var(--warning);font-size:0.85rem;margin-top:0.3rem">
                                ⚠️ <?= htmlspecialchars($ex['observacao']) ?>
                            </p>
                        <?php endif; ?>

                        <!-- Sets para marcar -->
                        <div class="sets-grid">
                            <?php for ($s = 1; $s <= $ex['series']; $s++): ?>
                                <div class="set-box" id="set-<?= $i ?>-<?= $s ?>"
                                     onclick="marcarSet(<?= $i ?>, <?= $s ?>, <?= $ex['descanso_seg'] ?>)">
                                    <div class="set-num"><?= $s ?>ª</div>
                                    <div><?= htmlspecialchars($ex['repeticoes']) ?></div>
                                    <?php if ($ex['carga_kg']): ?>
                                        <div style="color:var(--text-muted)"><?= $ex['carga_kg'] ?>kg</div>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div class="mt-1" style="font-size:0.8rem;color:var(--text-muted)">
                            ⏱ <?= $ex['descanso_seg'] ?>s de descanso entre séries
                        </div>

                        <div class="ex-check" style="margin-top:0.5rem;display:none" id="done-<?= $i ?>">
                            ✅ <strong style="color:var(--success)">Exercício concluído!</strong>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<!-- Timer de descanso fixo -->
<div class="timer-bar" id="timerBar">
    <div>
        <div style="font-size:0.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text-muted)">
            DESCANSO
        </div>
        <div class="timer-display" id="timerDisplay">0:00</div>
    </div>
    <div style="flex:1">
        <div style="background:var(--bg3);border-radius:20px;height:6px;overflow:hidden">
            <div id="timerProg" style="background:var(--accent);height:100%;width:100%;border-radius:20px;transition:width 1s linear"></div>
        </div>
    </div>
    <button class="btn btn-secondary btn-sm" onclick="pararTimer()">Pular ⏭</button>
</div>

<script>
    const totalEx = <?= count($exercicios_arr) ?>;
    const setsFeitos = {};
    let timerInterval = null;

    function marcarSet(exIdx, setNum, descanso) {
        const key = `${exIdx}-${setNum}`;
        const el  = document.getElementById(`set-${exIdx}-${setNum}`);

        if (el.classList.contains('done')) {
            el.classList.remove('done');
            delete setsFeitos[key];
        } else {
            el.classList.add('done');
            setsFeitos[key] = true;
            if (descanso > 0) iniciarTimer(descanso);
        }

        verificarExercicio(exIdx);
        atualizarProgresso();
    }

    function verificarExercicio(exIdx) {
        const card = document.getElementById(`excard-${exIdx}`);
        const series = card.querySelectorAll('.set-box').length;
        const feitas = [...card.querySelectorAll('.set-box.done')].length;

        if (feitas === series) {
            card.classList.add('done');
            document.getElementById(`done-${exIdx}`).style.display = 'block';
        } else {
            card.classList.remove('done');
            document.getElementById(`done-${exIdx}`).style.display = 'none';
        }
    }

    function atualizarProgresso() {
        let concluidos = 0;
        for (let i = 0; i < totalEx; i++) {
            const card = document.getElementById(`excard-${i}`);
            if (card && card.classList.contains('done')) concluidos++;
        }
        const pct = totalEx > 0 ? (concluidos / totalEx * 100) : 0;
        document.getElementById('progressoBar').style.width = pct + '%';
        document.getElementById('progressoTexto').textContent = `${concluidos} / ${totalEx} exercícios`;
    }

    let timerTotal = 0;
    let timerRestante = 0;

    function iniciarTimer(segundos) {
        pararTimer();
        timerTotal = segundos;
        timerRestante = segundos;

        const bar = document.getElementById('timerBar');
        const prog = document.getElementById('timerProg');
        bar.classList.add('show');
        atualizarDisplay();

        timerInterval = setInterval(() => {
            timerRestante--;
            const pct = (timerRestante / timerTotal * 100);
            prog.style.width = pct + '%';
            atualizarDisplay();
            if (timerRestante <= 0) pararTimer();
        }, 1000);
    }

    function atualizarDisplay() {
        const m = Math.floor(timerRestante / 60);
        const s = timerRestante % 60;
        document.getElementById('timerDisplay').textContent =
            `${m}:${String(s).padStart(2,'0')}`;
    }

    function pararTimer() {
        clearInterval(timerInterval);
        document.getElementById('timerBar').classList.remove('show');
        document.getElementById('timerProg').style.width = '100%';
    }

    function resetarTreino() {
        if (!confirm('Resetar todo o progresso deste treino?')) return;
        document.querySelectorAll('.set-box').forEach(el => el.classList.remove('done'));
        document.querySelectorAll('.exercise-card').forEach(el => el.classList.remove('done'));
        document.querySelectorAll('[id^="done-"]').forEach(el => el.style.display='none');
        Object.keys(setsFeitos).forEach(k => delete setsFeitos[k]);
        atualizarProgresso();
        pararTimer();
    }
</script>
</body>
</html>