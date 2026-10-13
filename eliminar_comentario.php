<?php
session_start();
require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'] ?? ($_SESSION['id'] ?? 0);
$id_comentario = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_obra = isset($_GET['obra_id']) ? intval($_GET['obra_id']) : 0;

if ($usuario_id > 0 && $id_comentario > 0) {
    $stmt = $conexion->prepare("DELETE FROM comentarios WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_comentario, $usuario_id);
    $stmt->execute();
}

header("Location: ver_obra.php?id=" . $id_obra);
exit;