<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';


if (!function_exists('obtenerDatosObra')) {
    function obtenerDatosObra($conexion, $id_obra)
    {
        $stmt_obra = $conexion->prepare("
            SELECT o.*, u.id AS id_autor, u.usuario AS autor_nombre 
            FROM obras o 
            LEFT JOIN usuarios u ON o.id_usuario = u.id 
            WHERE o.id = ? OR o.Id = ?
        ");
        $stmt_obra->bind_param("ii", $id_obra, $id_obra);
        $stmt_obra->execute();
        $datos_obra = $stmt_obra->get_result()->fetch_assoc();

        $stmt = $conexion->prepare("SELECT AVG(calificacion) as promedio, COUNT(*) as total FROM comentarios WHERE id_obra = ?");
        $stmt->bind_param("i", $id_obra);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        $promedio = $res['promedio'] ? round($res['promedio'], 1) : 0;
        $estrellas_llenas = floor($promedio);

        $html_estrellas = '';
        for ($i = 1; $i <= 5; $i++) {
            $html_estrellas .= ($i <= $estrellas_llenas)
                ? '<i class="fas fa-star"></i>'
                : '<i class="far fa-star" style="color: #cbd5e1;"></i>';
        }

        return [
            'promedio' => $promedio,
            'estrellas' => $html_estrellas,
            'id_autor' => $datos_obra['id_autor'] ?? 1,
            'autor_nombre' => $datos_obra['autor_nombre'] ?? 'Usuario'
        ];
    }
}

$cat_filtrada = isset($_GET['cat']) ? trim($_GET['cat']) : '';

if (!empty($cat_filtrada)) {
    $cat_esc = $conexion->real_escape_string($cat_filtrada);
    $sql_obras = "SELECT * FROM obras WHERE (categoria LIKE '%$cat_esc%' OR titulo LIKE '%$cat_esc%') AND estado = 'publicado' ORDER BY Id DESC";
    $titulo_seccion = "Obras de " . htmlspecialchars($cat_filtrada);
} else {
    $sql_obras = "SELECT * FROM obras WHERE estado = 'publicado' ORDER BY Id DESC";
    $titulo_seccion = "Nuevas Obras";
}

$res_obras = $conexion->query($sql_obras);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumiol - Explorar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<div class="header" id="Inicio">
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
                    <input type="text" name="cat" placeholder="Buscar categorías..."
                        value="<?php echo htmlspecialchars($cat_filtrada); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <?php if (isset($_SESSION['usuario_nombre'])): ?>
                <a href="actividad.php" class="btn-auth">Hola,
                    <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></a>
            <?php else: ?>
                <a href="login.php" class="btn-auth">INICIAR SESIÓN</a>
            <?php endif; ?>
        </div>
        <nav class="nav-bar">
            <ul class="nav-links">
                <li><a href="index.php">INICIO</a></li>
                <li><a href="popular.php">POPULAR</a></li>
                <li><a href="actividad.php">ACTIVIDAD</a></li>
                <li><a href="explorar.php" class="active">EXPLORAR</a></li>
                <li><a href="legado.php">LEGADO</a></li>
            </ul>
        </nav>
    </header>
</div>

    <main class="container main-content">
        <section style="margin-bottom: 50px;">
            <h2 class="section-title">Categorías Destacadas</h2>
            <div class="categories-grid">

                <a href="explorar.php?cat=Literatura" class="cat-box-link">
                    <div class="cat-box <?php echo ($cat_filtrada === 'Literatura') ? 'active-cat' : ''; ?>">
                        <div class="badge"></div>
                        <span class="cat-icon"><i class="fa-solid fa-book"></i></span>
                        <h4>Literatura</h4>
                        <span class="cat-count">Explorar</span>
                    </div>
                </a>

                <a href="explorar.php?cat=Artes Visuales" class="cat-box-link">
                    <div class="cat-box <?php echo ($cat_filtrada === 'Artes Visuales') ? 'active-cat' : ''; ?>">
                        <div class="badge"></div>
                        <span class="cat-icon"><i class="fa-solid fa-palette"></i></span>
                        <h4>Artes visuales</h4>
                        <span class="cat-count">Explorar</span>
                    </div>
                </a>

                <a href="explorar.php?cat=Fotografía" class="cat-box-link">
                    <div class="cat-box <?php echo ($cat_filtrada === 'Fotografía') ? 'active-cat' : ''; ?>">
                        <div class="badge"></div>
                        <span class="cat-icon"><i class="fa-solid fa-camera"></i></span>
                        <h4>Fotografía</h4>
                        <span class="cat-count">Explorar</span>
                    </div>
                </a>

                <a href="explorar.php?cat=Música" class="cat-box-link">
                    <div class="cat-box <?php echo ($cat_filtrada === 'Música') ? 'active-cat' : ''; ?>">
                        <div class="badge"></div>
                        <span class="cat-icon"><i class="fa-solid fa-music"></i></span>
                        <h4>Música</h4>
                        <span class="cat-count">Explorar</span>
                    </div>
                </a>

                <a href="explorar.php?cat=Teatro" class="cat-box-link">
                    <div class="cat-box <?php echo ($cat_filtrada === 'Teatro') ? 'active-cat' : ''; ?>">
                        <div class="badge"></div>
                        <span class="cat-icon"><i class="fa-solid fa-masks-theater"></i></span>
                        <h4>Teatro</h4>
                        <span class="cat-count">Explorar</span>
                    </div>
                </a>

            </div>
        </section>

        <section>
            <div class="filter-header">
                <h2 class="section-title" style="margin: 0;"><?php echo $titulo_seccion; ?></h2>
                <?php if (!empty($cat_filtrada)): ?>
                    <a href="explorar.php" class="btn-reset-filter"><i class="fas fa-times"></i> Quitar Filtro</a>
                <?php endif; ?>
            </div>

            <div class="works-grid">
                <?php if ($res_obras && $res_obras->num_rows > 0): ?>
                    <?php while ($obra = $res_obras->fetch_assoc()): ?>
                        <?php
                        $id_actual = $obra['Id'] ?? $obra['id'];
                        $datos = obtenerDatosObra($conexion, $id_actual);
                        ?>

                        <article class="work-card">
                            <a href="ver_obra.php?id=<?php echo $id_actual; ?>" style="text-decoration:none; color:inherit;">
                                <div class="work-image-placeholder">
                                    <img src="<?php echo htmlspecialchars(!empty($obra['archivo']) ? $obra['archivo'] : 'IMG/lumiollogo.png'); ?>"
                                        alt="Sin imagen disponible">
                                </div>
                                <div class="work-info">
                                    <span class="work-tag"><?php echo strtoupper(htmlspecialchars($obra['categoria'] ?? 'GENERAL')); ?></span>
                                    <h3><?php echo htmlspecialchars($obra['titulo']); ?></h3>
                                    <p class="work-author">
                                        <?php echo htmlspecialchars($obra['tipo_contenido'] ?? $obra['descripcion'] ?? 'Publicación'); ?>
                                    </p>
                                </div>
                                <div class="p-card-rating">
                                    <span>CALIFICACIÓN ·
                                        <?php echo ($datos['promedio'] > 0) ? $datos['promedio'] : 'S/C'; ?>/5</span>
                                    <div class="p-stars">
                                        <?php echo $datos['estrellas']; ?>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                <?php endif; ?>

            </div>
        </section>

        <a href="crear_obra.php" class="btn-floating-create" title="Crear nueva obra">
            <i class="fas fa-pen"></i>
        </a>

    </main>

    <!--To Top-->
    <a href="#Inicio">
        <div class="to-top"><</div>
    </a>
    <script src="script.js"></script>

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