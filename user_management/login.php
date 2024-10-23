<?php
session_start();
$_SESSION['user'] = $_GET['user'];
$url = '..?op=login&result=ok';
header('Location: ' . $url);