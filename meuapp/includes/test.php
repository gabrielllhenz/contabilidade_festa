<?php
$host = "localhost";
$dbname = "festa_db";
$username = "root";
$password = ""; // já que no phpMyAdmin vai sem senha

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    echo "✅ Conexão bem-sucedida!";
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
