<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar">
    <a class="navbar-brand" href="dashboard.php">ZE<span>NET</span></a>
    <ul class="navbar-nav">
        <li><a href="/GymProjectPHP/src/app/Views/aluno/dashboard.php" <?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'class="active"' : '' ?>>Início</a></li>
        <li><a href="/GymProjectPHP/src/app/Views/aluno/meus_treinos.php" <?= (basename($_SERVER['PHP_SELF']) === 'meus_treinos.php') ? 'class="active"' : '' ?>>Meus Treinos</a></li>
        <li><span class="badge-tipo" style="background:var(--info)">Aluno</span></li>
        <li><a href="/GymProjectPHP/src/public/logout.php">Sair</a></li>
    </ul>
</nav>