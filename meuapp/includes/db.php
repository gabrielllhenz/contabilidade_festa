<?php

$host = "localhost"; 
$dbname = "festa_db"; 
$username = "root"; 
$password = "";
$port = 3307;


$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    // Se a conexão falhar, este código vai mostrar o erro exato na tela
    die("A conexão com o banco de dados falhou: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>