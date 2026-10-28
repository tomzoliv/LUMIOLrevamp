<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumiol - Nosotros</title>
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
                <li><a href="index.php" class="active">INICIO</a></li>
                <li><a href="popular.php">POPULAR</a></li>
                <li><a href="actividad.php">ACTIVIDAD</a></li>
                <li><a href="explorar.php">EXPLORAR</a></li>
                <li><a href="legado.php">LEGADO</a></li>
                <li><a href="agenda.php">AGENDA CULTURAL</a></li>
            </ul>
        </nav>
    </header>

    <main class="nosotros">

        <header class="nosotros-header">
            <h1 class="titulo">NOSOTROS: LUMIOL</h1>
            <div class="nosotros-texto">
                <p>Somos un refugio para el arte sin filtros. Un espacio donde la creación contemporánea se encuentra
                    con la memoria histórica. LUMIOL nace como una alternativa emergente y un espacio de resistencia
                    frente a lo establecido.</p>
                <p>Creemos en el poder de la identidad local. En Colombia hay algunas falencias como la falta de
                    visibilidad o reconocimiento y esto nos lleva a querer mejorar el sistema para volver a la luz el
                    arte y las habilidades de cada usuario.</p>
            </div>
        </header>

        <section class="pilares-section">
            <div class="pilar-card">
                <div class="icono-dorado"><i class="fa-solid fa-palette"></i></div>
                <h3>SOBRE LUMIOL</h3>
                <p>Es una plataforma para subir y compartir tus obras (ilustaciones, fotografía, ensayos, música) sin
                    barreras ni censura institucional.</p>
            </div>
            <div class="pilar-card">
                <div class="icono-dorado"><i class="fa-solid fa-book-open"></i></div>
                <h3>NUESTRO PROPÓSITO</h3>
                <p>Conectamos tu arte con las raíces de los grandes maestros y la historia cultural de Medellín y
                    Antioquia.</p>
            </div>
            <div class="pilar-card">
                <div class="icono-dorado"><i class="fa-solid fa-comment-dots"></i></div>
                <h3>NUESTRA COMUNIDAD</h3>
                <p>Unida por debate profundo, donde califican, reseñan y aportan feedback constructivo a tus obras.</p>
            </div>
        </section>

        <section class="equipo-section">
            <h2 class="equipo-subtitulo">EL EQUIPO</h2>

            <div class="equipo-grid">
                <div class="miembro-card">
                    <img src="IMG/olober.jpeg" alt="Oliver Foto" class="foto">
                    <div class="miembro-info">
                        <h4>Oliver Blanco</h4>
                        <p>Desarrollador jefe</p>
                    </div>
                </div>
                <div class="miembro-card">
                    <img src="IMG/Miri.jpg" alt="Miranda Foto" class="foto">
                    <div class="miembro-info">
                        <h4>Miranda Echavarria</h4>
                        <p>Desarrolladora UI/UX</p>
                    </div>
                </div>
                <div class="miembro-card">
                    <img src="IMG/Luci.png" alt="Luciana Foto" class="foto">
                    <div class="miembro-info">
                        <h4>Luciana Murillo</h4>
                        <p>Manager</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="unete">
            <a href="registro.php" class="btn-unete">ÚNETE A LUMIOL</a>
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