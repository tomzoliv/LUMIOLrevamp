<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumiol - login</title>
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

    <main class="auth-layout">
        <div class="auth-card">
            <h2 class="auth-titulo">INICIAR SESIÓN</h2>

            <div class="intro">
                <p>Únete a la comunidad de LUMIOL y expande tu horizonte. Accede a análisis literarios exclusivos,
                    comparte tus perspertivas y califica obras que transforman.</p>
            </div>

            <form class="auth-formulario" action="procesar_login.php" method="POST">
                <div class="formulario">
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                </div>

                <div class="formulario">
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">INICIAR SESIÓN</button>
            </form>

            <div class="auth-footer">
                <p class="auth-text-sub">¿OLVIDASTE TU CONTRASEÑA?</p>
                <a href="recuperar.php" class="auth-link-font-serif">RECUPÉRALA AQUÍ</a>

                <div class="auth-div"></div>

                <p class="auth-text">¿NUEVO EN LUMIOL</p>
                <a href="registro.php" class="auth-link-m">REGÍSTRATE</a>
                <br>
                <a href="registro.php" class="auth-link-font-serif">CREAR CUENTA</a>
            </div>
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