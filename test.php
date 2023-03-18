<?php
$server     = 'localhost\SQLEXPRESS';
$instance   = '\\SQLEXPRESS';
$user       = 'Profit';
$pass       = 'profit';
$database =  'JBP_A';

$dsn = "sqlsrv:server={$server};database={$database}";
$dbh = new PDO($dsn, $user, $pass);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt   = $dbh->query("SELECT co_cli, cli_des, login, Password, co_ven, inactivo, campo1 FROM clientes");
$row    = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($row);
