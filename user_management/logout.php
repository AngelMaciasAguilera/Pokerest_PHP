<?php
session_start();
unset($_SESSION['user']);
$url = '..?op=logout&result=ok';
header('Location: ' . $url);