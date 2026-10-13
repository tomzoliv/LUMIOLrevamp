<?php
session_start();
require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'] ?? ($_SESSION['id'] ?? 0);
$id_obra = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($usuario_id > 0 && $id_obra > 0) {
    $stmt = $conexion->prepare("DELETE FROM obras WHERE (id = ? OR Id = ?) AND id_usuario = ?");
    $stmt->bind_param("iii", $id_obra, $id_obra, $usuario_id);
    $stmt->execute();
}

header("Location: explorar.php");
exit;