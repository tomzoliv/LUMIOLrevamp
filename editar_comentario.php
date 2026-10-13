<?php
session_start();
require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'] ?? ($_SESSION['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_comentario = intval($_POST['id_comentario']);
    $id_obra = intval($_POST['id_obra']);
    $texto_nuevo = trim($_POST['comentario']);

    if ($usuario_id > 0 && $id_comentario > 0 && !empty($texto_nuevo)) {
        $stmt = $conexion->prepare("UPDATE comentarios SET contenido = ? WHERE id = ? AND id_usuario = ?");
        $stmt->bind_param("sii", $texto_nuevo, $id_comentario, $usuario_id);
        $stmt->execute();
    }
    
    header("Location: ver_obra.php?id=" . $id_obra);
    exit;
}