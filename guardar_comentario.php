<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $id_obra = intval($_POST['id_obra']);
    $id_usuario = $_SESSION['usuario_id'];
    $calificacion = intval($_POST['calificacion']);
    $contenido = trim($_POST['contenido']);

    if ($calificacion >= 1 && $calificacion <= 5 && !empty($contenido)) {
        $stmt = $conexion->prepare("INSERT INTO comentarios (id_obra, id_usuario, contenido, calificacion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $id_obra, $id_usuario, $contenido, $calificacion);
        $stmt->execute();
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>