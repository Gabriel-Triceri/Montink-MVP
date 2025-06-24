<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'classes/core/conexao.php';
require_once 'classes/core/Router.php';

$conexao = new Conexao();
$db = $conexao->conectar();

$url = $_GET['url'] ?? '';

Router::route($url, $db);
