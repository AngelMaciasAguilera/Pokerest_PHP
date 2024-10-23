<?php
$resultado = 0;
$url = 'create.php?op=insertproduct&result=' . $resultado;

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if(!isset($_SESSION['user'])) {
    header('Location:.');
    exit;
}

/*Identificacion de errores segun el valor que mi $resultado contenga:
  > 0 -> Todo ha salido correcto 
  -1 -> nombre duplicado o ya existente en la base de datos.
  -2 -> los datos no son validos para realizar su insercion
  -3 -> no se han mandado todos lo datos necesarios para realizar la insercion
  -4 -> error de conexion.   
*/

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
    $url = 'create.php?op=insertproduct&result=' . $resultado;
    header('Location: ' . $url);
    exit;
}
 



$name = '';
$weight = 0.0;
$height = 0.0;
$type = '';
$evolutions = 0;

if(isset($_POST['name']) && isset($_POST['weight']) && isset($_POST['height']) && isset($_POST['p_type']) && isset($_POST['n_evolutions'])) {
    $name = $_POST['name'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];
    $type = $_POST['p_type'];
    $evolutions = $_POST['n_evolutions'];
    $ok = true;
    $name = trim($name);

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

    if($ok) {
        $sql = 'insert into pokemons (nombre, peso,altura,tipo,evoluciones) values (:nombre, :peso, :altura, :tipo, :evoluciones)';
        $sentence = $connection->prepare($sql);
        $parameters = ['nombre' => $name, 'peso' => $weight, 'altura' => $height, 'tipo' => $type, 'evoluciones' => $evolutions];
        foreach($parameters as $nombreParametro => $valorParametro) {
            $sentence->bindValue($nombreParametro, $valorParametro);
        }

        try {
            $sentence->execute();
            $resultado = $connection->lastInsertId();
            $url = 'create.php?op=insertproduct&result=' . $resultado;
        } catch(PDOException $e) {
            $resultado = -1;
            $url = 'create.php?op=insertproduct&result=' . $resultado;
            save_data_iferror($name,$weight,$height,$type,$evolutions);
            header('Location: ' . $url);
            exit;
        }
    }else{
        $resultado = -2;
        $url = 'create.php?op=insertproduct&result=' . $resultado;
        save_data_iferror($name,$weight,$height,$type,$evolutions);
    }
}else{
    $resultado = -3;
    $url = 'create.php?op=insertproduct&result=' . $resultado;
    save_data_iferror($name,$weight,$height,$type,$evolutions);
}


header('Location: ' . $url);

/*Esta funcion la voy a utilizar cuando el programa contenga un error para guardar los datos en mi $_SESSION sin tener que repetir lineas de codigo*/ 
function save_data_iferror($name,$weight,$height,$type,$evolutions){
    $_SESSION['old']['name'] = $name;
    $_SESSION['old']['weight'] = $weight;
    $_SESSION['old']['height'] = $height;
    $_SESSION['old']['type'] = $type;
    $_SESSION['old']['evolutions'] = $evolutions;
}