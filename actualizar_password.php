<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $nueva_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("SELECT email FROM recuperacion_tokens WHERE token = ? AND expiracion > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($registro = $resultado->fetch_assoc()) {
        $email = $registro['email'];

        $stmtUpdate = $conexion->prepare("UPDATE usuarios SET contraseña = ? WHERE correo = ?");
        $stmtUpdate->bind_param("ss", $nueva_pass, $email);
        $stmtUpdate->execute();

        $stmtDelete = $conexion->prepare("DELETE FROM recuperacion_tokens WHERE email = ?");
        $stmtDelete->bind_param("s", $email);
        $stmtDelete->execute();

        header("Location: login.php?msg=password_actualizada");
        exit;
    } else {
        echo "Token inválido o expirado.";
    }
}
?>