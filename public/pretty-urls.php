<?php
// Redirige todas las rutas a index.php para que tu router/front-controller las maneje
$_SERVER["PATH_INFO"] = $_SERVER["REQUEST_URI"] ?? '/';
require __DIR__ . '/index.php';
