<?php
session_start();

if (!isset($_SESSION['usuario_nombre']) || !isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';

$id_usuario = $_SESSION['usuario_id'];
$error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_ingresada = $_POST['password'] ?? '';

    if (empty($password_ingresada)) {
        $error = "Por favor, ingresa tu contraseña para confirmar.";
    } else {

        $stmt_user = $conexion->prepare("SELECT contraseña FROM usuarios WHERE id = ?");
        $stmt_user->bind_param("i", $id_usuario);
        $stmt_user->execute();
        $res_user = $stmt_user->get_result()->fetch_assoc();

        $hash_bd = $res_user['contraseña'] ?? '';


        $password_valida = password_verify($password_ingresada, $hash_bd) || ($password_ingresada === $hash_bd);

        if ($password_valida) {
            $conexion->begin_transaction();

            try {

                $stmt_comentarios = $conexion->prepare("
                    DELETE c FROM comentarios c
                    LEFT JOIN obras o ON c.id_obra = o.id OR c.id_obra = o.Id
                    WHERE c.id_usuario = ? OR o.id_usuario = ?
                ");
                $stmt_comentarios->bind_param("ii", $id_usuario, $id_usuario);
                $stmt_comentarios->execute();

                $stmt_obras = $conexion->prepare("DELETE FROM obras WHERE id_usuario = ?");
                $stmt_obras->bind_param("i", $id_usuario);
                $stmt_obras->execute();

                $stmt_usuario = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt_usuario->bind_param("i", $id_usuario);
                $stmt_usuario->execute();

                $conexion->commit();

                session_unset();
                session_destroy();

                header("Location: login.php?msg=cuenta_eliminada");
                exit();

            } catch (Exception $e) {
                $conexion->rollback();
                $error = "Error al eliminar la cuenta. Por favor intenta más tarde.";
            }
        } else {
            $error = "La contraseña ingresada es incorrecta.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Cuenta · LUMIOL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="activity-page-body">

    <header class="main-header">
        <div class="container header-top">
            <a href="index.php" class="logo-container">
                <div class="logo-icon">
                    <img src="IMG/lumiollogo.png" alt="Logo LUMIOL" class="logo-img">
                </div>
                <h1 class="logo-text">LUMIOL</h1>
            </a>
            <div class="search-container">
                <form action="explorar.php" method="GET">
                    <input type="text" name="cat" placeholder="Buscar categorías...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <a href="actividad.php" class="btn-auth">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></a>
        </div>
        <nav class="nav-bar">
            <ul class="nav-links">
                <li><a href="index.php">INICIO</a></li>
                <li><a href="popular.php">POPULAR</a></li>
                <li><a href="actividad.php">ACTIVIDAD</a></li>
                <li><a href="explorar.php">EXPLORAR</a></li>
                <li><a href="legado.php">LEGADO</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-layout">
        <div class="auth-card delete-account-card">
            <h2 class="auth-titulo delete-title">
                <i class="fas fa-exclamation-triangle"></i> Eliminar Cuenta
            </h2>

            <p class="delete-warning-text">
                Esta acción es <strong>definitiva e irreversible</strong>. Se borrarán permanentemente todas tus publicaciones, borradores y datos asociados en Lumiol.
            </p>

            <?php if (!empty($error)): ?>
                <p class="error-msg-box">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>

            <form action="eliminar_cuenta.php" method="POST" class="auth-formulario">
                <div class="form-group">
                    <label for="password" class="delete-label">
                        Ingresa tu contraseña para confirmar:
                    </label>
                    <input type="password" name="password" id="password" placeholder="Contraseña" required autofocus>
                </div>

                <button type="submit" class="btn-submit btn-delete-submit" onclick="return confirm('⚠️\n\n¿Realmente deseas borrar definitivamente tu cuenta de Lumiol?');">
                    Confirmar y eliminar mi cuenta
                </button>

                <div class="delete-cancel-container">
                    <a href="actividad.php" class="delete-cancel-link">
                        ← Cancelar y volver a mi actividad
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container footer-container">
            <div class="footer-left">
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=lumiolsupport@gmail.com" target="_blank">
                    <i class="far fa-envelope"></i> <span class="email-txt">lumiolsupport@gmail.com</span>
                </a>
            </div>
            <div class="footer-center">
                <a href="https://www.instagram.com/lumiol" target="_blank"><i class="fab fa-instagram"></i> @lumiol</a>
                <a href="nosotros.php"><i class="fa-regular fa-address-book"></i> Nosotros</a>
            </div>
            <div class="footer-right">&copy; 2026 LUMIOL. Todos los derechos reservados.</div>
        </div>
    </footer>

</body>
</html>