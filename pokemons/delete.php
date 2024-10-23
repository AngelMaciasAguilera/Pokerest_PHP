<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if(!isset($_SESSION['user'])) {
    header('Location:.');
    exit;
}
$user = $_SESSION['user'];

try {
    $connection = new PDO(
        'mysql:host=localhost;dbname=pokemon_php_database',
        'dbroot',
        'dbroot',
        array(
            PDO::ATTR_PERSISTENT => true,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8'
        )
    );
} catch (PDOException $e) {
    //header('Location:..');
    var_dump($connection);
    //ex
}
if(isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $url = '.?op=deleteproduct&result=' . $resultado;
    header('Location: ' . $url);
    exit;
}

$sql = 'delete from pokemons where id = :id';
$sentence = $connection->prepare($sql);
$parameters = ['id' => $id];
foreach($parameters as $nombreParametro => $valorParametro) {
    $sentence->bindValue($nombreParametro, $valorParametro);
}
try {
    $sentence->execute();
    $resultado = $sentence->rowCount();
} catch(PDOException $e) {
    $resultado = -1;
}
$connection = null;
$url = '.?op=deleteproduct&result=' . $resultado;
header('Location: ' . $url);