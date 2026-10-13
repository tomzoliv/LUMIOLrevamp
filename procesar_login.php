<?php
session_start();
require 'conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {

        $stmt = $conexion->prepare("SELECT id, usuario, contraseña, foto FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $datos = $resultado->fetch_assoc();

            if (password_verify($password, $datos['contraseña']) || $password === $datos['contrasena']) {
                
                $_SESSION['usuario_id'] = $datos['id'];
                $_SESSION['usuario_nombre'] = $datos['usuario'];
                $_SESSION['usuario_foto'] = $datos['foto'];

                
                header("Location: actividad.php");
                exit();
            } else {
                echo "<script>alert('Contraseña incorrecta.'); window.location.href='login.php';</script>";
            }
        } else {
            echo "<script>alert('El usuario no existe.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Por favor llena todos los campos.'); window.location.href='login.php';</script>";
    }
} else {
    header("Location: login.php");
    exit();
}
?>