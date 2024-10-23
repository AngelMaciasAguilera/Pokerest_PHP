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

if(isset($_POST['id'])) {
    $id = $_POST['id'];
} else {
    $url = '.?op=updateproduct&result=noid';
    header('Location: ' . $url);
    exit;
}


if(isset($_POST['name'])) {
    $name = trim($_POST['name']);
} else {
    header('Location: .');
    exit;
}

if(isset($_POST['weight'])) {
    $weight = $_POST['weight'];
} else {
    header('Location: .');
    exit;
}

if(isset($_POST['height'])) {
    $height = $_POST['height'];
} else {
    header('Location: .');
    exit;
}

if(isset($_POST['p_type'])) {
    $type = $_POST['p_type'];
} else {
    header('Location: .');
    exit;
}

if(isset($_POST['n_evolutions'])) {
    $evolutions = $_POST['n_evolutions'];
} else {
    header('Location: .');
    exit;
}


$ok = true;

if(strlen($name) < 2 || strlen($name) > 150) {
    $ok = false;
}

if(!(is_numeric($weight) && $weight >= 0 && $weight <= 999999999.9)) {
    $ok = false;
}

if(!(is_numeric($height) && $height >= 0 && $height <= 999999999.9)) {
    $ok = false;
}

if(!(is_numeric($evolutions) && $evolutions >= 0 && $evolutions <= 18)){
    $ok = false;
}

$resultado = 0;

if($ok) {
    $sql = 'update pokemons set nombre = :nombre, peso = :peso, altura = :altura, tipo = :tipo, evoluciones = :evoluciones where id = :id';
    $sentence = $connection->prepare($sql);
    $parameters = ['nombre' => $name, 'peso' => $weight, 'altura' => $height, 'tipo' => $type, 'evoluciones' => $evolutions, 'id' => $id];
    foreach($parameters as $nombreParametro => $valorParametro) {
        $sentence->bindValue($nombreParametro, $valorParametro);
    }
    try {
        $sentence->execute();
        $resultado = $sentence->rowCount();
        $url = '.?op=editproduct&result=' . $resultado;
    } catch(PDOException $e) {
    }
}

if($resultado == 0) {
    $_SESSION['old']['name'] = $name;
    $_SESSION['old']['weight'] = $weight;
    $_SESSION['old']['height'] = $height;
    $_SESSION['old']['type'] = $type;
    $_SESSION['old']['evolutions'] = $evolutions;
    $url = 'edit.php?op=editproduct&result=' . $resultado . '&id=' . $id;
}
header('Location: ' . $url);