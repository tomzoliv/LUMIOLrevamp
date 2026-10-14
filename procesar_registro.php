<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';
    $correo = trim($_POST['email'] ?? '');

    if (!preg_match('/^[a-zA-Z0-9_áéíóúÁÉÍÓÚñÑ-]+$/u', $usuario)) {
        echo "<script>
                alert('El nombre de usuario solo puede contener letras, números y guiones bajos (_). Sin espacios ni símbolos.');
                window.history.back();
              </script>";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "<script>
                alert('Las contraseñas no coinciden.');
                window.history.back();
              </script>";
        exit();
    }

    $check_stmt = $conexion->prepare("SELECT Id FROM usuarios WHERE usuario = ? OR correo = ?");
    if (!$check_stmt) {
        die("Error en la consulta de verificación: " . $conexion->error);
    }

    $check_stmt->bind_param("ss", $usuario, $correo);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>
                alert('El nombre de usuario o el correo electrónico ya se encuentran registrados.');
                window.history.back();
              </script>";
        $check_stmt->close();
        exit();
    }
    $check_stmt->close();

    $password_encriptada = password_hash($password, PASSWORD_BCRYPT);
    
    $stmt = $conexion->prepare("INSERT INTO usuarios (usuario, correo, contraseña) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Error en la preparación del INSERT: " . $conexion->error);
    }

    $stmt->bind_param("sss", $usuario, $correo, $password_encriptada);

    if ($stmt->execute()) {
        echo "<script>
                alert('¡Registro exitoso!');
                window.location.href = 'login.php';
            </script>";
    } else {
        echo "Error al ejecutar el registro: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>