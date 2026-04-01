<?php

 //CONEXÃO COM O BANCO DE DADOS

$servidor = "localhost"; // server padrão
$usuario  = "root"; // user padrão dos banco de dados
$senha    = "";
$dbname   = "academia_zenet"; // quando for clonar o projeto coloque o nome igual a este no seu banco de dados

$conexao = mysqli_connect($servidor, $usuario, $senha, $dbname);

if (!$conexao) { // faz uma validação caso a conexão não ocorra, mas é só seguir os passos que estou dizendo a VOCÊ, meu amor!
    die("Erro na conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");