<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = trim($_POST['nombre']);
    $id_usuario = $_SESSION['usuario_id'];

    if (!preg_match('/^[a-zA-Z0-9_áéíóúÁÉÍÓÚñÑ-]+$/u', $nuevo_nombre)) {
        echo "<script>
                alert('El nombre de usuario solo puede contener letras, números y guiones bajos (_).');
                window.history.back();
              </script>";
        exit();
    }

    $stmt = $conexion->prepare("UPDATE usuarios SET usuario = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_nombre, $id_usuario);
    $stmt->execute();
    $_SESSION['usuario_nombre'] = $nuevo_nombre;


    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombre_foto = $_FILES['foto']['name'];
        $extension = pathinfo($nombre_foto, PATHINFO_EXTENSION);

        $nuevo_nombre_foto = "perfil_" . $id_usuario . "." . $extension;
        $ruta_destino = "uploads/" . $nuevo_nombre_foto;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
            $stmt_foto = $conexion->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
            $stmt_foto->bind_param("si", $ruta_destino, $id_usuario);
            $stmt_foto->execute();

            $_SESSION['usuario_foto'] = $ruta_destino;
        }
    }

    header("Location: actividad.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumiol - Editar Perfil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

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
            <?php if (isset($_SESSION['usuario_nombre'])): ?>
                <a href="actividad.php" class="btn-auth">Hola,
                    <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-auth">INICIAR SESIÓN</a>
            <?php endif; ?>
        </div>
        <nav class="nav-bar">
            <ul class="nav-links">
                <li><a href="index.php">INICIO</a></li>
                <li><a href="popular.php">POPULAR</a></li>
                <li><a href="actividad.php" class="active">ACTIVIDAD</a></li>
                <li><a href="explorar.php">EXPLORAR</a></li>
                <li><a href="legado.php">LEGADO</a></li>
                <li><a href="agenda.php">AGENDA CULTURAL</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <div class="edit-profile-container">
            <h2 class="edit-profile-title">Editar Perfil</h2>

            <form action="editar_perfil.php" method="POST" enctype="multipart/form-data" class="edit-profile-form">
                <div class="form-group">
                    <label for="nombre">Nombre de usuario:</label>
                    <input type="text" id="nombre" name="nombre"
                        value="<?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>" 
                        pattern="[a-zA-Z0-9_]+"
                        title="Solo se permiten letras, números y guiones bajos (_)." required>
                </div>

                <div class="form-group">
                    <label for="foto">Foto de perfil:</label>
                    <input type="file" id="foto" name="foto" accept="image/*">
                </div>

                <div class="form-actions">
                    <a href="actividad.php" class="btn-cancel">Cancelar</a>
                    <button type="submit" class="btn-save">Guardar Cambios</button>
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