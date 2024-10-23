<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
$id = 0;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    var_dump($e);
}
$sql = 'select * from pokemons where id = :id';
$sentence = $connection->prepare($sql);
$parameters = ['id' => $id];
foreach ($parameters as $nombreParametro => $valorParametro) {
    $sentence->bindValue($nombreParametro, $valorParametro);
}
try {
    $sentence->execute();
} catch (PDOException $e) {
    var_dump($e);
}

if (!$fila = $sentence->fetch()) {
    var_dump($e);
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
        <a class="navbar-brand" href="..">Pokerest</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="..">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="./">Pokemons</a>
                </li>
            </ul>
        </div>
    </nav>
    <main role="main">
        <div class="jumbotron">
            <div class="container">
                <h4 class="display-4">Pokemon selected</h4>
            </div>
        </div>
        <div class="container">

            <?php
                $sql_table_header = "SHOW COLUMNS FROM pokemons";
                $header_sentence = $connection->prepare($sql_table_header);
                $header_sentence->execute();
                $header_array = [];
                while($pokedata = $header_sentence -> fetch()) {
                    array_push($header_array, $pokedata['Field']);
                } 
            ?>

            <div>
                    <?php 
                        foreach($header_array as  $poke_header){
                    ?>
                    <div class="form-group">
                        pokemon <?= $poke_header;?>:
                        <?= $fila[$poke_header] ?>
                    </div>
                <?php 
                        }
                ?>
            </div>
            <a href="index.php">back</a>
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