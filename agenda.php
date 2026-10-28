<?php
session_start();
require_once 'conexion.php';

$sql_eventos = "SELECT * FROM eventos_culturales ORDER BY fecha_hora ASC";
$res_eventos = $conexion->query($sql_eventos);

$sql_actividades = "SELECT * FROM actividades_culturales ORDER BY id DESC";
$res_actividades = $conexion->query($sql_actividades);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Cultural - LUMIOL</title>
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
                <a href="actividad.php" class="btn-auth">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></a>
            <?php else: ?>
                <a href="login.php" class="btn-auth">INICIAR SESIÓN</a>
            <?php endif; ?>
        </div>
        <nav class="nav-bar">
            <ul class="nav-links">
                <li><a href="index.php">INICIO</a></li>
                <li><a href="popular.php">POPULAR</a></li>
                <li><a href="actividad.php">ACTIVIDAD</a></li>
                <li><a href="explorar.php">EXPLORAR</a></li>
                <li><a href="legado.php">LEGADO</a></li>
                <li><a href="agenda.php" class="active">AGENDA CULTURAL</a></li>
            </ul>
        </nav>
    </header>

    <main class="container main-content">
        <section style="margin-bottom: 50px;">
            <h2 class="section-title">EVENTOS CULTURALES</h2>
            <div class="agenda-grid">
                <?php if ($res_eventos && $res_eventos->num_rows > 0): ?>
                    <?php while ($evt = $res_eventos->fetch_assoc()): ?>
                        <?php 
                            $img_evt = (!empty($evt['imagen']) && file_exists($evt['imagen'])) ? $evt['imagen'] : 'IMG/lumiollogo.png';
                            $fecha_fmt = date("d/m/Y - h:i A", strtotime($evt['fecha_hora']));
                        ?>
                        <article class="agenda-card">
                            <div class="agenda-img-container">
                                <img src="<?php echo htmlspecialchars($img_evt); ?>" alt="<?php echo htmlspecialchars($evt['nombre']); ?>" class="agenda-img">
                            </div>
                            <div class="agenda-content">
                                <h3 class="agenda-title"><?php echo htmlspecialchars($evt['nombre']); ?></h3>
                                <p class="agenda-meta"><i class="far fa-calendar-alt"></i> <?php echo $fecha_fmt; ?></p>
                                <p class="agenda-meta"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($evt['lugar']); ?></p>
                                <p class="agenda-desc"><?php echo htmlspecialchars($evt['descripcion']); ?></p>
                            </div>
                            <div class="agenda-footer">
                                <a href="<?php echo htmlspecialchars($evt['enlace_info']); ?>" target="_blank" class="btn-info-event">
                                    Más información <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </section>

        <section style="margin-bottom: 50px;">
            <h2 class="section-title">TALLERES Y ESPACIOS FORMATIVOS</h2>
            <div class="agenda-grid">
                <?php if ($res_actividades && $res_actividades->num_rows > 0): ?>
                    <?php while ($act = $res_actividades->fetch_assoc()): ?>
                        <?php 
                            $img_act = (!empty($act['imagen']) && file_exists($act['imagen'])) ? $act['imagen'] : 'IMG/lumiollogo.png';
                        ?>
                        <article class="agenda-card">
                            <div class="agenda-img-container">
                                <img src="<?php echo htmlspecialchars($img_act); ?>" alt="<?php echo htmlspecialchars($act['nombre']); ?>" class="agenda-img">
                            </div>
                            <div class="agenda-content">
                                <h3 class="agenda-title"><?php echo htmlspecialchars($act['nombre']); ?></h3>
                                <p class="agenda-meta"><i class="far fa-clock"></i> <?php echo htmlspecialchars($act['horario']); ?></p>
                                <p class="agenda-meta"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($act['lugar']); ?></p>
                                <p class="agenda-desc"><?php echo htmlspecialchars($act['descripcion']); ?></p>
                            </div>
                            <div class="agenda-footer">
                                <a href="<?php echo htmlspecialchars($act['enlace_info']); ?>" target="_blank" class="btn-info-event">
                                    Ver talleres / Inscripciones <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </section>
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