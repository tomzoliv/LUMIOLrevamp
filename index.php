<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumiol - Inicio</title>
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

    <main class="container main-content inicio-layout">

        <aside class="ruta-sidebar">
            <a href="legado.php" style="text-decoration:none; color:inherit;"><h2 class="sidebar-main-title">RUTA CULTURAL DEL MES</h2></a>
            <h4 class="comuna-title">COMUNA 10 · EL CENTRO</h4>

            <div class="sidebar-divider"></div>

            <div class="ruta-lugar">
                <h5><i class="far fa-dot-circle"></i> TEATRO LIDO</h5>
                <p class="lugar-desc">Música clásica y lucha por la identidad. Escuche su "Concierto en La Alpujarra".
                </p>
                <audio src="audio/Intermezzo.mp3" controls></audio>
            </div>

            <div class="sidebar-divider"></div>

            <div class="ruta-lugar">
                <h5><i class="far fa-dot-circle"></i> CAFÉ PASAJE</h5>
                <p class="lugar-desc">Pensamiento crítico y bohemia. Fragmento: "Café Pasaje de las culturas: música y
                    dormitorio".</p>
                <a href="ver_obra.php?id=24" style="text-decoration:none; color: #b84a39;" class="lugar-link">Leer fragmento &rarr;</a>
            </div>
        </aside>

        <section class="protagonistas-section">
            <h2 class="section-title">PROTAGONISTAS DE LA RUTA</h2>

            <div class="carousel-container">
                <button class="carousel-arrow prev-btn"><i class="fas fa-chevron-left"></i></button>

                <div class="protagonistas-grid">

                    <article class="p-card">
                        <a href="ver_obra.php?id=1" style="text-decoration:none; color:inherit;">
                            <div class="p-portrait">
                                <img src="IMG/tomasxd.jpg" alt="Foto de Tomás Carrasquilla">
                            </div>
                            <div class="p-info">
                                <a href="explorar.php?cat=Literatura" style="text-decoration:none; color:inherit;" class="tag-link">
                                    <span class="work-tag">LITERATURA</span>
                                </a>
                                <h3>Tomás Carrasquilla</h3>
                                <p class="p-bio">Padre de la literatura antioqueña moderna. Su vida y obra están ligadas
                                    a Medellín...</p>
                            </div>
                        </a>
                    </article>

                    <article class="p-card">
                        <a href="ver_obra.php?id=2" style="text-decoration:none; color:inherit;">
                            <div class="p-portrait">
                                <img src="IMG/pintura-debora.webp" alt="Pintura de Debora">
                            </div>
                            <div class="p-info">
                                <span class="work-tag">ARTES VISUALES</span>
                                <h3>Débora Arango</h3>
                        </a>
                        <p class="p-bio">Las pinturas políticas de Débora Arango. En Casablanca, su casa museo en
                            Envigado, pintó sus obras más rebeldes.</p>
                </div>
                </article>

                <article class="p-card">
                    <a href="ver_obra.php?id=8" style="text-decoration:none; color:inherit;">
                        <div class="p-portrait">
                            <img src="IMG/teresa-dibujo.jpg" alt="Ilustración de Teresita">
                        </div>
                        <div class="p-info">
                            <span class="work-tag">MÚSICA</span>
                            <h3>Teresita Gómez</h3>
                    </a>
                    <p class="p-bio">Una pianista cuyo arte demuestra resiliencia: la disciplina de su música y el
                        toque suave de sus teclas, compositora del pueblo.</p>
            </div>
            </article>

            <article class="p-card">
                <a href="ver_obra.php?id=6" style="text-decoration:none; color:inherit;">
                    <div class="p-portrait">
                        <img src="IMG/iatmul.jpg" alt="Hombre con piel de cocodrilo, Pueblo Iatmul">
                    </div>
                    <div class="p-info">
                        <span class="work-tag">LITERATURA</span>
                        <h3>Pueblo Iatmul</h3>
                </a>
                <p class="p-bio">Devorar para nacer: el cuerpo como territorio de transformación</p>
                </div>
            </article>

            </div>

            <button class="carousel-arrow next-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
        </section>

        <a href="crear_obra.php" class="btn-floating-create" title="Crear nueva obra">
        <i class="fas fa-pen"></i>
        </a>

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

    <script src="script.js"></script>

</body>

</html>