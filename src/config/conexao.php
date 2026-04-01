<?php
// ============================================
// CONEXÃO COM O BANCO - academia_zenet
// ============================================
$servidor = "localhost";
$usuario  = "root";
$senha    = "";
$dbname   = "academia_zenet";

$conexao = mysqli_connect($servidor, $usuario, $senha, $dbname);

if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");