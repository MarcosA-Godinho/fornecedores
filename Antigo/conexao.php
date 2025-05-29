<?php
// ✅ CONFIGURAÇÕES DO BANCO DE DADOS
$host = "localhost";
$user = "root";
$pass = "";
$db = "sistema_faturas";

// ✅ CRIA CONEXÃO
$con = new mysqli($host, $user, $pass, $db);

// ✅ VERIFICA CONEXÃO
if ($con->connect_error) {
    die("Falha na conexão: " . $con->connect_error);
}

// ✅ DEFINE UTF-8
$con->set_charset("utf8");
?>
