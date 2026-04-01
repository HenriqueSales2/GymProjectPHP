<?php
// ============================================
// FUNÇÕES DE AUTENTICAÇÃO
// ============================================

function requerLogin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

function requerProfessor() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
        header("Location: ../login.php");
        exit();
    }
}

function requerAluno() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'aluno') {
        header("Location: ../login.php");
        exit();
    }
}

function ehProfessor() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'professor';
}

function sanitize($conn, $valor) {
    return mysqli_real_escape_string($conn, trim($valor));
}