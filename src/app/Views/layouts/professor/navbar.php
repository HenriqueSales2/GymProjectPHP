<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar">
    <a class="navbar-brand" href="dashboard.php">ZE<span>NET</span></a>
    <ul class="navbar-nav">
        <li><a href="dashboard.php" <?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'class="active"' : '' ?>>Dashboard</a></li>
        <li><a href="alunos.php" <?= (basename($_SERVER['PHP_SELF']) === 'alunos.php') ? 'class="active"' : '' ?>>Alunos</a></li>
        <li><a href="treinos.php" <?= (basename($_SERVER['PHP_SELF']) === 'treinos.php') ? 'class="active"' : '' ?>>Treinos</a></li>
        <li><span class="badge-tipo">Professor</span></li>
        <li><a href="/GymProjectPHP/src/public/logout.php">Sair</a></li>
    </ul>
</nav>