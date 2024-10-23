<?php
/*Primero compruebo que me llega algun id de un pokemon al que tenga que actualizar sino no continuo*/
$id = -1;
if (isset($_POST['id'])) {
    $id = $_POST['id'];
} else {
    $url = '.?op=updateproduct&result=noid';
    header('Location: ' . $url);
    exit;

    echo $_POST['id'];
}

$resultado = 0;
$url = 'edit.php?op=editproduct&result=' . $resultado . '&id=' . $id;
ini_set('display_errors', 1);
error_reporting(E_ALL);

/*Identificacion de errores segun el valor que mi $resultado contenga:
  > 0 -> Todo ha salido correcto 
  -1 -> nombre duplicado o ya existente en la base de datos.
  -2 -> los datos no son validos para realizar su insercion
  -3 -> no se han mandado todos lo datos necesarios para realizar la insercion
  -4 -> error de conexion.   
*/


session_start();
if (!isset($_SESSION['user'])) {
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
    $resultado =  -4;
    $url = 'edit.php?op=editproduct&result=' . $resultado . '&id=' . $id;
    header('Location: ' . $url);
    exit;
}




$name = '';
$weight = 0.0;
$height = 0.0;
$type = '';
$evolutions = 0;

if (isset($_POST['name']) && isset($_POST['weight']) && isset($_POST['height']) && isset($_POST['p_type']) && isset($_POST['n_evolutions'])) {
    $name = $_POST['name'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];
    $type = $_POST['p_type'];
    $evolutions = $_POST['n_evolutions'];
    $ok = true;
    $name = trim($name);

    if (strlen($name) < 2 || strlen($name) > 150) {
        $ok = false;
    }
    if (!(is_numeric($weight) && $weight >= 0 && $weight <= 999999999.9)) {
        $ok = false;
    }
    if (!(is_numeric($height) && $height >= 0 && $height <= 999999999.9)) {
        $ok = false;
    }

    if (!(is_numeric($evolutions) && $evolutions >= 0 && $evolutions <= 18)) {
        $ok = false;
    }

    if ($ok) {
        $sql = 'update pokemons set nombre = :nombre, peso = :peso, altura = :altura, tipo = :tipo, evoluciones = :evoluciones where id = :id';
        $sentence = $connection->prepare($sql);
        $parameters = ['nombre' => $name, 'peso' => $weight, 'altura' => $height, 'tipo' => $type, 'evoluciones' => $evolutions, 'id' => $id];
        foreach ($parameters as $nombreParametro => $valorParametro) {
            $sentence->bindValue($nombreParametro, $valorParametro);
        }
        try {
            $sentence->execute();
            $resultado = $sentence->rowCount();
            $url = 'edit.php?op=editproduct&result=' . $resultado . '&id=' . $id;
        } catch (PDOException $e) {
            $resultado = -1;
            $url = 'edit.php?op=editproduct&result=' . $resultado . '&id=' . $id;
            save_data_iferror($name,$weight,$height,$type,$evolutions);
            header('Location: ' . $url);
            exit;
        }
    } else {
        $resultado = -2;
        $url = 'edit.php?op=editproduct&result=' . $resultado . '&id=' . $id;
        save_data_iferror($name, $weight, $height, $type, $evolutions);
    }
} else {
    $resultado = -3;
    $url = 'edit.php?op=editproduct&result=' . $resultado . '&id=' . $id;
    save_data_iferror($name, $weight, $height, $type, $evolutions);
}


header('Location: ' . $url);

/*Esta funcion la voy a utilizar cuando el programa contenga un error para guardar los datos en mi $_SESSION sin tener que repetir lineas de codigo*/
function save_data_iferror($name, $weight, $height, $type, $evolutions)
{
    $_SESSION['old']['name'] = $name;
    $_SESSION['old']['weight'] = $weight;
    $_SESSION['old']['height'] = $height;
    $_SESSION['old']['type'] = $type;
    $_SESSION['old']['evolutions'] = $evolutions;
}
