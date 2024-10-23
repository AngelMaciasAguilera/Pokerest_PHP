<?php

ini_set('display_errors', 1);
error_reporting(E_ALL); //Estas lineas son para ver los errores que pueda producir el codigo

session_start();

$user = null;
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user']; //Compruebo el usuario
}

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
    //exit;
}
/*Obtengo los registros con los pokemons que hay en mi base de datos*/

$sql = 'select * from pokemons order by id';
$error = '';
try {
    $sentence = $connection->prepare($sql);
    $sentence->execute();
} catch (PDOException $e) {
    //echo '<pre>' . var_export($e, true) . '</pre>';
    header('Location:..');
    exit;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pokerest</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href="..">Pokerest</a>
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
            <div class="container">
                <h4 class="display-4">Pokemons</h4>
            </div>
        </div>
        <div class="container">
            <?php
            if (isset($_GET['op']) && isset($_GET['result'])) {
                if ($_GET['result'] > 0) {
            ?>
                    <div class="alert alert-primary" role="alert">
                        result: <?= $_GET['op'] . ' ' . $_GET['result'] ?>
                    </div>
                <?php
                } else {
                ?>
                    <div class="alert alert-danger" role="alert">
                        result: <?= $_GET['op'] . ' ' . $_GET['result'] ?>
                    </div>
            <?php
                }
            }
            ?>
            <div class="row">
                <h3>All pokemons</h3>
            </div>
            <table class="table table-striped table-hover" id="tablaProducto">
                <thead>
                    <tr>
                        <?php
                        /*Obtengo las columnas de la tabla de pokemons con esta sentencia sql, ademas me devuelve otros datos con 
                        informacion sobre dichas columnas pero cogeremos solo la columna que queremos con los campos de la tabla 
                        de esa manera si añadimos un campo en la base de datos no tendremos que ponerlo a mano sino que se mostrara 
                        automaticamente*/
                        $sql_table_header = "SHOW COLUMNS FROM pokemons";
                        $header_sentence = $connection->prepare($sql_table_header);
                        $header_sentence->execute();
                        $header_array = [];

                        /*Recorro el array que me devuelve, la sentencia y obtengo los valores que necesito de la columna field que es 
                        la que contiene el nombre de todas las columnas de mi tabla, ademas lo guardo en mi array auxiliar $header_array
                        para que despues la obtencion de datos sea automatica y no tengamos que ponerlo todo manualmente.*/
                        while ($header_name = $header_sentence->fetch()) {
                            echo '<th>' . $header_name['Field'] . '</th>';
                            array_push($header_array, $header_name['Field']);
                        }


                        ?>
                        <?php
                        if (isset($_SESSION['user'])) {
                        ?>
                            <th>delete</th>
                            <th>edit</th>
                        <?php
                        }
                        ?>
                        <th>view</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    /*Recorro todos los registros que me devuelve la base de datos con los pokemons que contiene*/
                    while ($fila = $sentence->fetch()) {
                    ?>
                        <tr>
                            <?php
                            /*Hago uso de mi array auxiliar con las cabeceras de mi tabla y en cada vuelta del foreach saco 
                            el valor especifico y lo imprimo sobre la tabla.*/
                            foreach ($header_array as $columna) {
                                echo '<td>' . $fila[$columna] . '</td>';
                            }
                            ?>

                            <?php
                            if (isset($_SESSION['user'])) {
                            ?>
                                <td><a href="./delete.php?id=<?= $fila['id']?>" class="borrar">delete</a></td>
                                <td><a href="./edit.php?id=<?= $fila['id']?>" class="edit">edit</a></td>

                            <?php
                            }
                            ?>
                            <td><a href="./view.php?id=<?= $fila['id']?>">view</a></td>
                        </tr>

                    <?php
                    }
                    ?>

                </tbody>
            </table>
            <div class="row">
                <?php
                if (isset($_SESSION['user'])) {
                ?>
                    <a href="create.php" class="btn btn-success">add pokemon</a>
                <?php
                }
                ?>
            </div>
            <hr>
        </div>
    </main>
    <footer class="container">
        <p>&copy;Angel 2024</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="./assets/js/prevent_delete.js"></script>
</body>

</html>