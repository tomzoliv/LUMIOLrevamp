<?php
session_start();

if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';

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
                ? '<i class="fas fa-star" style="color: #b83b3b;"></i>'
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

$id_usuario = $_SESSION['usuario_id'] ?? null;
$tab_activa = $_GET['tab'] ?? 'general';

$borradores = [];
$mis_publicaciones = [];
$mis_me_gusta = [];

if ($id_usuario) {

    $stmt = $conexion->prepare("SELECT * FROM obras WHERE id_usuario = ? AND estado = 'borrador' ORDER BY fecha DESC");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $borradores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt_pub = $conexion->prepare("SELECT * FROM obras WHERE id_usuario = ? AND estado = 'publicado' ORDER BY fecha DESC");
    $stmt_pub->bind_param("i", $id_usuario);
    $stmt_pub->execute();
    $mis_publicaciones = $stmt_pub->get_result()->fetch_all(MYSQLI_ASSOC);

    $query_likes = "
    SELECT o.*, c.calificacion AS mi_calificacion
    FROM obras o 
    INNER JOIN comentarios c ON (o.Id = c.id_obra OR o.id = c.id_obra)
    WHERE c.id_usuario = ? AND c.calificacion = 5 AND o.estado = 'publicado'
    ORDER BY c.id DESC";
    $stmt_likes = $conexion->prepare($query_likes);
    $stmt_likes->bind_param("i", $id_usuario);
    $stmt_likes->execute();
    $mis_me_gusta = $stmt_likes->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumiol - Actividad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/actividad.css">
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
                </ul>
            </nav>
        </header>
    </div>

    <main class="container main-content activity-layout">
        <aside class="profile-sidebar">
            <div class="profile-avatar">
                <?php if (!empty($_SESSION['usuario_foto']) && file_exists($_SESSION['usuario_foto'])): ?>
                    <img src="<?php echo $_SESSION['usuario_foto']; ?>" alt="Foto de perfil" class="avatar-img">
                <?php else: ?>
                    <?php echo strtoupper(substr($_SESSION['usuario_nombre'], 0, 1)); ?>
                <?php endif; ?>
            </div>
            <h3 class="profile-name">
                <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                <a href="editar_perfil.php" title="Editar perfil" class="edit-profile-icon">
                    <i class="fas fa-pen"></i>
                </a>
            </h3>
            <ul class="sidebar-menu">
                <li class="dashed-top">
                    <a href="actividad.php?tab=borradores"
                        class="tab-link <?php echo ($tab_activa === 'borradores') ? 'active-tab' : ''; ?>">
                        <i class="fas fa-paint-brush"></i> Borradores (<?php echo count($borradores); ?>)
                    </a>
                </li>
                <li class="dashed-top">
                    <a href="logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </li>
                <li class="dashed-top">
                    <a href="eliminar_cuenta.php" class="btn-delete-account">
                        <i class="fas fa-trash-alt"></i> Eliminar cuenta
                    </a>
                </li>
            </ul>
        </aside>

        <div class="activity-feed-full">

            <?php if ($tab_activa === 'borradores'): ?>
                <div class="section-block">
                    <div class="borradores-header">
                        <h3 class="column-title no-border">Mis borradores <i class="fas fa-paint-brush"></i></h3>
                        <a href="actividad.php" class="back-link">← Volver a mi actividad</a>
                    </div>

                    <div class="cards-grid">
                        <?php if (!empty($borradores)): ?>
                            <?php foreach ($borradores as $borrador): ?>
                                <article class="card card-borrador">
                                    <div class="card-header">
                                        <span class="card-tag"><?php echo htmlspecialchars($borrador['categoria']); ?></span>
                                        <div class="card-media">
                                            <?php if (!empty($borrador['archivo']) && file_exists($borrador['archivo'])): ?>
                                                <img src="<?php echo htmlspecialchars($borrador['archivo']); ?>"
                                                    alt="<?php echo htmlspecialchars($borrador['titulo']); ?>">
                                            <?php else: ?>
                                                <div class="sin-imagen">
                                                    Sin imagen disponible
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h4 class="card-title"><?php echo htmlspecialchars($borrador['titulo']); ?></h4>
                                        <p class="card-date">
                                            Guardado: <?php echo date("d/m/Y", strtotime($borrador['fecha'])); ?>
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="crear_obra.php?id=<?php echo $borrador['Id']; ?>"
                                            class="card-btn edit-borrador-btn">Editar borrador</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="empty-message">No tienes borradores guardados actualmente.</p>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>

                <div class="section-block">
                    <h3 class="column-title">Mis publicaciones <i class="fa-regular fa-file"></i></h3>

                    <div class="cards-grid">
                        <?php if (!empty($mis_publicaciones)): ?>
                            <?php foreach ($mis_publicaciones as $obra): ?>
                                <?php
                                $id_actual = $obra['Id'] ?? $obra['id'];
                                $datos = obtenerDatosObra($conexion, $id_actual);
                                ?>
                                <article class="work-card">
                                    <a href="ver_obra.php?id=<?php echo $id_actual; ?>"
                                        style="text-decoration:none; color:inherit;">
                                        <div class="work-image-placeholder">
                                            <img src="<?php echo htmlspecialchars(!empty($obra['archivo']) ? $obra['archivo'] : 'IMG/lumiollogo.png'); ?>"
                                                alt="Sin imagen disponible">
                                        </div>
                                        <div class="work-info">
                                            <span
                                                class="work-tag"><?php echo strtoupper(htmlspecialchars($obra['categoria'] ?? 'GENERAL')); ?></span>
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
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="empty-message">No has publicado ninguna obra aún.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="section-block">
                    <h3 class="column-title">Me gusta <i class="fa-regular fa-heart"></i></h3>

                    <div class="cards-grid">
                        <?php if (!empty($mis_me_gusta)): ?>
                            <?php foreach ($mis_me_gusta as $obra_like): ?>
                                <?php
                                $id_like = $obra_like['Id'] ?? $obra_like['id'];
                                $datos_like = obtenerDatosObra($conexion, $id_like);
                                ?>
                                <article class="work-card">
                                    <a href="ver_obra.php?id=<?php echo $id_like; ?>" style="text-decoration:none; color:inherit;">
                                        <div class="work-image-placeholder">
                                            <img src="<?php echo htmlspecialchars(!empty($obra_like['archivo']) ? $obra_like['archivo'] : 'IMG/lumiollogo.png'); ?>"
                                                alt="Sin imagen disponible">
                                        </div>
                                        <div class="work-info">
                                            <span
                                                class="work-tag"><?php echo strtoupper(htmlspecialchars($obra_like['categoria'] ?? 'GENERAL')); ?></span>
                                            <h3><?php echo htmlspecialchars($obra_like['titulo']); ?></h3>
                                            <p class="work-author">
                                                <?php echo htmlspecialchars($obra_like['tipo_contenido'] ?? $obra_like['descripcion'] ?? 'Publicación'); ?>
                                            </p>
                                        </div>
                                        <div class="p-card-rating">
                                            <span>CALIFICACIÓN ·
                                                <?php echo ($datos_like['promedio'] > 0) ? $datos_like['promedio'] : 'S/C'; ?>/5</span>
                                            <div class="p-stars">
                                                <?php echo $datos_like['estrellas']; ?>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="empty-message">No has calificado obras con 5 estrellas.</p>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endif; ?>

        </div>

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