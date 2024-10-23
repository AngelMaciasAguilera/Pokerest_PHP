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
    header('Location:..');
    exit;
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $url = '.?op=editproduct&result=noid';
    header('Location: ' . $url);
    exit;
}

$sql = 'select * from pokemons where id = :id';
$sentence = $connection->prepare($sql);
$parameters = ['id' => $id];
foreach($parameters as $nombreParametro => $valorParametro) {
    $sentence->bindValue($nombreParametro, $valorParametro);
}

try {
    $sentence->execute();
    $row = $sentence->fetch();
} catch(PDOException $e) {
    header('Location:.');
    exit;
}

if($row == null) {
    header('Location: .');
    exit;
}

/*Creamos las variables donde guardaremos el valor que inserte el usuario a la hora de crear un pokemon,
esto permite que si ocurre un error tengamos los valores que habia puesto el usuario y que no los tenga
que escribir de nuevo*/

$name = '';
$weight = 0.000;
$height = 0.000;
$type = '';
$evolutions = 0;

/*Comprobamos si ese valor ya esta declarado en el $_SESSION que es el array donde guardaremos dichos valores,
si ya esta declarado guardamos ese valor en la variable correspondiente y lo quitamos del array para ganar rendimiento*/

if(isset($_SESSION['old']['name'])){
    $name = $_SESSION['old']['name'];
    unset($_SESSION['old']['name']);
}

if(isset($_SESSION['old']['weight'])){
    $weight = $_SESSION['old']['weight'];
    unset($_SESSION['old']['weight']);
}

if(isset($_SESSION['old']['height'])){
    $height = $_SESSION['old']['height'];
    unset($_SESSION['old']['height']);
}

if(isset($_SESSION['old']['type'])){
    $type = $_SESSION['old']['type'];
    unset($_SESSION['old']['type']);
}

if(isset($_SESSION['old']['evolutions'])){
    $evolutions = $_SESSION['old']['evolutions'];
    unset($_SESSION['old']['evolutions']);
}

$id = $row['id'];
if($name == '') {
    $name = $row['nombre'];
}
if($weight == 0) {
    $weight = $row['peso'];
}
if($height == 0) {
    $height = $row['altura'];
}
if($type == '') {
    $type = $row['tipo'];
}
if($evolutions == 0) {
    $evolutions = $row['evoluciones'];
}
?>
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>pokerest</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <a class="navbar-brand" href="..">pokerest</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="..">home</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="./">pokemons</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main role="main">
            <div class="jumbotron">
            </div>
            <div class="container">
            <?php
            if (isset($_GET['op']) && isset($_GET['result'])) {
                if ($_GET['result'] >= 0) {
            ?>
                    <div class="alert alert-primary" role="alert">
                        El pokemon se ha actualizado correctamente, su id es <?= $_GET['result'] ?>
                    </div>
                <?php
                } else {
                ?>
                    <div class="alert alert-danger" role="alert">
                        <?php
                                $error_message = '';
                                $result = $_GET['result'];
                                /*Compruebo cada caso posible para imprimir el mensaje de error y por si acaso pongo un valor
                                por defecto.*/
                                switch ($result) {
                                    case -1:
                                        $error_message = 'El nombre que intenta asignarle al pokemon ya existe en la base de datos, pruebe otro';
                                        break;
                                    case -2:
                                        $error_message = 'Los datos enviados no son validos';
                                        break;
                                    case -3:
                                        $error_message = 'no se han mandado los datos necesarios para realizar la actualizacion';
                                        break;
                                    case -4:
                                        $error_message = 'Ha ocurrido un error de conexion con la base de datos';
                                        break;
                                    default:
                                        $error_message = 'Ha ocurrido un error inesperado';
                                }
                            
                        ?>

                        <?= $error_message ?>

                    </div>
            <?php
                    }
                }
            ?>
                <div>
                    <form action="update.php" method="post">
                        <div class="form-group">
                            <label for="name">pokemon name</label>
                            <input value="<?= $name ?>" required type="text" class="form-control" id="name" name="name" placeholder="product name">
                        </div>
                        <div class="form-group">
                            <label for="weight">pokemon weight</label>
                            <input value="<?= $weight ?>" required type="number" step="0.001" class="form-control" id="weight" name="weight" placeholder="pokemon weight">
                        </div>
                        <div class="form-group">
                            <label for="height">pokemon height</label>
                            <input value="<?= $height ?>" required type="number" step="0.001" class="form-control" id="height" name="height" placeholder="pokemon height">
                        </div>
                        <select name="p_type" id="p_type">
                            <?php
                                /*Realizamos una consulta sql que me obtenga todos los tipos de enum existentes*/
                                $sql = "SHOW COLUMNS FROM pokemons LIKE 'tipo'";
                                $statement = $connection->prepare($sql);
                                $statement->execute();
                                $result = $statement->fetch();
                                 //Una vez obtenido guardo el array especifico que necesito con los valores en mi variable $enumValues
                                $enumValues = $result['Type'];

                                //Ahora reemplazo los caracteres que no me interesen del array que me devuelve la base de datos
                                $enumValues = str_replace("enum('", "", $enumValues);
                                $enumValues = str_replace("')", "", $enumValues);

                                //Separo cada valor del array con ,
                                $enumValuesArray = explode("','", $enumValues);

                                /*Recorro el array con los valores que necesito y compruebo si es igual al que el usuario hubiera
                                elegido durante la creacion del pokemon y si es asi le pongo la propiedad selected para que lo seleccione
                                por defecto*/
                                foreach ($enumValuesArray as $poke_type) {
                                    $isTypeSelected = '';
                                    if ($poke_type === $type) {
                                        $isTypeSelected = 'selected';
                                    }
                            
                            ?>
                            
                            <option value="<?= $poke_type ?>" <?= $isTypeSelected ?>> <?= $poke_type ?> </option>
                            
                            <?php
                                }
                            ?>
                        </select>

                        <div class="form-group">
                            <label for="evolutions">pokemon evolutions number</label>
                            <input value="<?= $evolutions ?>" required type="number" class="form-control" id="n_evolutions" name="n_evolutions" placeholder="number of evolutions of the pokemon">
                        </div> 

                        <input type="hidden" name="id" value="<?= $id ?>" />
                        <button type="submit" class="btn btn-primary">edit</button>
                    </form>
                </div>
                <hr>
            </div>
        </main>
        <footer class="container">
            <p>&copy; Angel 2024</p>
        </footer>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </body>
</html>