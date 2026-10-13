<?php
session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function obtenerDatosObra($conexion, $id_obra)
{
    $stmt_obra = $conexion->prepare("
        SELECT o.*, u.id AS id_autor, u.usuario AS autor_nombre 
        FROM obras o 
        LEFT JOIN usuarios u ON o.id_usuario = u.id 
        WHERE o.id = ?
    ");
    $stmt_obra->bind_param("i", $id_obra);
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


    $stmt_destacado = $conexion->prepare("
        SELECT c.contenido, c.calificacion, u.usuario, u.foto 
        FROM comentarios c 
        LEFT JOIN usuarios u ON c.id_usuario = u.id 
        WHERE c.id_obra = ? 
        ORDER BY c.calificacion DESC, c.fecha DESC 
        LIMIT 1
    ");
    $stmt_destacado->bind_param("i", $id_obra);
    $stmt_destacado->execute();
    $comentario_destacado = $stmt_destacado->get_result()->fetch_assoc();


    $stmt_lista = $conexion->prepare("
        SELECT c.contenido, c.calificacion, c.fecha, u.usuario, u.foto 
        FROM comentarios c 
        LEFT JOIN usuarios u ON c.id_usuario = u.id 
        WHERE c.id_obra = ? 
        ORDER BY c.fecha DESC
    ");
    $stmt_lista->bind_param("i", $id_obra);
    $stmt_lista->execute();
    $res_lista = $stmt_lista->get_result();

    $lista_comentarios = [];
    while ($row = $res_lista->fetch_assoc()) {
        $lista_comentarios[] = $row;
    }

    return [
        'promedio' => $promedio,
        'estrellas' => $html_estrellas,
        'destacado' => $comentario_destacado,
        'lista' => $lista_comentarios, 
        'id_autor' => $datos_obra['id_autor'] ?? 1,
        'autor_nombre' => $datos_obra['autor_nombre'] ?? 'tomzoliv'
    ];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumiol - Popular</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<div id="modalComentarios" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Todos los Comentarios</h3>
            <span class="btn-cerrar" onclick="cerrarModalComentarios()">&times;</span>
        </div>
        <div id="modalBodyComentarios" class="modal-body">
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .btn-cerrar {
        font-size: 24px;
        cursor: pointer;
        font-weight: bold;
    }
</style>

<script>
    function abrirModalComentarios(idObra) {

        var contenido = document.getElementById('comentarios-obra-' + idObra).innerHTML;
        document.getElementById('modalBodyComentarios').innerHTML = contenido;

        var inputObra = document.querySelector('#modalComentarios input[name="id_obra"]');
        if (inputObra) {
            inputObra.value = idObra;
        }
        document.getElementById('modalComentarios').style.display = 'flex';
    }

    function cerrarModalComentarios() {
        document.getElementById('modalComentarios').style.display = 'none';
    }

    window.onclick = function (event) {
        var modal = document.getElementById('modalComentarios');
        if (event.target == modal) {
            cerrarModalComentarios();
        }
    }
</script>

<body>

    <header id="Inicio">
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
                    <li><a href="popular.php" class="active">POPULAR</a></li>
                    <li><a href="actividad.php">ACTIVIDAD</a></li>
                    <li><a href="explorar.php">EXPLORAR</a></li>
                    <li><a href="legado.php">LEGADO</a></li>
                </ul>
            </nav>
        </header>
    </header>

    <main class="container main-content">
        <section>
            <h2 class="section-title">Literatura</h2>
            <div class="popular-grid">

                <?php require_once 'conexion.php'; ?>
                <?php $datos23 = obtenerDatosObra($conexion, 23); ?>

                <article class="p-card">
                    <a href="ver_obra.php?id=23" style="text-decoration:none; color:inherit;">
                        <div class="p-card-header">
                            <div class="author-placeholder">
                                <img src="IMG/lenguaje.jpeg" alt="Ilustración">
                            </div>
                            <div class="p-header-text">
                                <h3>¿Sin lenguaje no hay pensamiento?</h3>
                                <p class="p-subtitle">En este ensayo se busca responder a una pregunta vacía.</p>
                            </div>
                        </div>
                        <div class="p-card-body">
                            <p>Ensayo sobre el existencialismo moderno y el pensamiento individual...</p>
                        </div>
                    </a>

                    <div class="p-card-rating">
                        <span>CALIFICACIÓN ·
                            <?php echo ($datos23['promedio'] > 0) ? $datos23['promedio'] : 'S/C'; ?>/5</span>
                        <div class="p-stars">
                            <?php echo $datos23['estrellas']; ?>
                        </div>
                    </div>

                    <div class="p-card-footer" onclick="abrirModalComentarios(1)"
                        style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <div class="p-avatar"
                            style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #334155; color: #fff; flex-shrink: 0;">
                            <?php if (!empty($datos23['destacado']['foto'])): ?>
                                <img src="<?php echo htmlspecialchars($datos23['destacado']['foto']); ?>" alt="Perfil"
                                    style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            <?php else: ?>
                                <span><?php echo $datos23['destacado'] ? strtoupper(substr($datos23['destacado']['usuario'], 0, 1)) : 'U'; ?></span>
                            <?php endif; ?>
                        </div>
                        <p style="margin: 0;">
                            <strong>Comentario destacado:</strong>
                            <?php echo $datos23['destacado'] ? htmlspecialchars($datos23['destacado']['contenido']) : 'Sé el primero en comentar esta obra.'; ?>
                        </p>
                    </div>

                    <div id="comentarios-obra-1" style="display: none;">
                        <?php if (!empty($datos23['lista'])): ?>
                            <?php foreach ($datos23['lista'] as $c): ?>
                                <div class="item-comentario"
                                    style="display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-start;">
                                    <div class="p-avatar"
                                        style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden; background: #334155; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <?php if (!empty($c['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($c['foto']); ?>" alt="Perfil"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($c['usuario'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($c['usuario']); ?></strong>
                                        <span style="color: #f59e0b;">(<?php echo $c['calificacion']; ?>/5 ⭐)</span>
                                        <p style="margin: 4px 0;"><?php echo htmlspecialchars($c['contenido']); ?></p>
                                        <small style="color: #64748b;"><?php echo $c['fecha']; ?></small>
                                    </div>
                                </div>
                                <hr style="border: 0.5px solid #eee; margin: 8px 0;">
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aún no hay comentarios en esta obra.</p>
                        <?php endif; ?>
                    </div>
                </article>


                <?php $datos9 = obtenerDatosObra($conexion, 9); ?>

                <article class="p-card">
                    <a href="ver_obra.php?id=9" style="text-decoration:none; color:inherit;">
                        <div class="p-card-header">
                            <div class="author-placeholder">
                                <img src="IMG/libros.jpeg" alt="Selección de libros">
                            </div>
                            <div class="p-header-text">
                                <h3>Latinoamérica y sus libros para el pueblo</h3>
                                <p class="p-subtitle">Un análisis literario para reflexionar sobre cómo la gente hace al
                                    libro.</p>
                            </div>
                        </div>
                        <div class="p-card-body">
                            <p>Dos caminos que llevan al mismo destino: una lectura crítica de la relación entre los
                                libros y sus pueblos en América Latina.</p>
                        </div>
                    </a>

                    <div class="p-card-rating">
                        <span>CALIFICACIÓN ·
                            <?php echo ($datos9['promedio'] > 0) ? $datos9['promedio'] : 'S/C'; ?>/5</span>
                        <div class="p-stars">
                            <?php echo $datos9['estrellas']; ?>
                        </div>
                    </div>

                    <div class="p-card-footer" onclick="abrirModalComentarios(9)"
                        style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <div class="p-avatar"
                            style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #334155; color: #fff; flex-shrink: 0;">
                            <?php if (!empty($datos9['destacado']['foto'])): ?>
                                <img src="<?php echo htmlspecialchars($datos9['destacado']['foto']); ?>" alt="Perfil"
                                    style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            <?php else: ?>
                                <span><?php echo $datos9['destacado'] ? strtoupper(substr($datos9['destacado']['usuario'], 0, 1)) : 'U'; ?></span>
                            <?php endif; ?>
                        </div>
                        <p style="margin: 0;">
                            <strong>Comentario destacado:</strong>
                            <?php echo $datos9['destacado'] ? htmlspecialchars($datos9['destacado']['contenido']) : 'Sé el primero en comentar esta obra.'; ?>
                        </p>
                    </div>

                    <div id="comentarios-obra-9" style="display: none;">
                        <?php if (!empty($datos9['lista'])): ?>
                            <?php foreach ($datos9['lista'] as $c): ?>
                                <div class="item-comentario"
                                    style="display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-start;">
                                    <div class="p-avatar"
                                        style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden; background: #334155; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <?php if (!empty($c['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($c['foto']); ?>" alt="Perfil"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($c['usuario'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($c['usuario']); ?></strong>
                                        <span style="color: #f59e0b;">(<?php echo $c['calificacion']; ?>/5 ⭐)</span>
                                        <p style="margin: 4px 0;"><?php echo htmlspecialchars($c['contenido']); ?></p>
                                        <small style="color: #64748b;"><?php echo $c['fecha']; ?></small>
                                    </div>
                                </div>
                                <hr style="border: 0.5px solid #eee; margin: 8px 0;">
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aún no hay comentarios en esta obra.</p>
                        <?php endif; ?>
                    </div>
                </article>
        </section>

        <section>
            <h2 class="section-title">Artes Visuales</h2>
            <div class="popular-grid">
                <?php $datos3 = obtenerDatosObra($conexion, 3); ?>

                <article class="p-card">
                    <a href="ver_obra.php?id=3" style="text-decoration:none; color:inherit;">
                        <div class="p-card-header">
                            <div class="author-placeholder">
                                <img src="IMG/boothill.webp"
                                    alt="Ilustración de Boothill, personaje del famoso juego Honkai: Star Rail.">
                            </div>
                            <div class="p-header-text">
                                <h3>Ilustración de Boothill!</h3>
                                <p class="p-subtitle">Hice este dibujo para demostrar mi amor hacía el personaje:
                                    Boothill y compatir su historia.</p>
                            </div>
                        </div>
                        <div class="p-card-body">
                            <p>La compleja historia de un personaje de videojuego...</p>
                        </div>
                    </a>

                    <div class="p-card-rating">
                        <span>CALIFICACIÓN ·
                            <?php echo ($datos3['promedio'] > 0) ? $datos3['promedio'] : 'S/C'; ?>/5</span>
                        <div class="p-stars">
                            <?php echo $datos3['estrellas']; ?>
                        </div>
                    </div>

                    <div class="p-card-footer" onclick="abrirModalComentarios(3)"
                        style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <div class="p-avatar"
                            style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #334155; color: #fff; flex-shrink: 0;">
                            <?php if (!empty($datos3['destacado']['foto'])): ?>
                                <img src="<?php echo htmlspecialchars($datos3['destacado']['foto']); ?>" alt="Perfil"
                                    style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            <?php else: ?>
                                <span><?php echo $datos3['destacado'] ? strtoupper(substr($datos3['destacado']['usuario'], 0, 1)) : 'U'; ?></span>
                            <?php endif; ?>
                        </div>
                        <p style="margin: 0;">
                            <strong>Comentario destacado:</strong>
                            <?php echo $datos3['destacado'] ? htmlspecialchars($datos3['destacado']['contenido']) : 'Sé el primero en comentar esta obra.'; ?>
                        </p>
                    </div>

                    <div id="comentarios-obra-3" style="display: none;">
                        <?php if (!empty($datos3['lista'])): ?>
                            <?php foreach ($datos3['lista'] as $c): ?>
                                <div class="item-comentario"
                                    style="display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-start;">
                                    <div class="p-avatar"
                                        style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden; background: #334155; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <?php if (!empty($c['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($c['foto']); ?>" alt="Perfil"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($c['usuario'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($c['usuario']); ?></strong>
                                        <span style="color: #f59e0b;">(<?php echo $c['calificacion']; ?>/5 ⭐)</span>
                                        <p style="margin: 4px 0;"><?php echo htmlspecialchars($c['contenido']); ?></p>
                                        <small style="color: #64748b;"><?php echo $c['fecha']; ?></small>
                                    </div>
                                </div>
                                <hr style="border: 0.5px solid #eee; margin: 8px 0;">
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aún no hay comentarios en esta obra.</p>
                        <?php endif; ?>
                    </div>
                </article>


                <?php $datos4 = obtenerDatosObra($conexion, 4); ?>

                <article class="p-card">
                    <a href="ver_obra.php?id=4" style="text-decoration:none; color:inherit;">
                        <div class="p-card-header">
                            <div class="author-placeholder">
                                <img src="IMG/self.webp" alt="Autorretrato de Oliver">
                            </div>
                            <div class="p-header-text">
                                <h3>Autorretrato</h3>
                                <p class="p-subtitle">Mi Autorretrato.</p>
                            </div>
                        </div>
                        <div class="p-card-body">
                            <p>Inspirado en el autorretrato de Akira Toriyama, un mangaka reconocido.</p>
                        </div>
                    </a>

                    <div class="p-card-rating">
                        <span>CALIFICACIÓN ·
                            <?php echo ($datos4['promedio'] > 0) ? $datos4['promedio'] : 'S/C'; ?>/5</span>
                        <div class="p-stars">
                            <?php echo $datos4['estrellas']; ?>
                        </div>
                    </div>

                    <div class="p-card-footer" onclick="abrirModalComentarios(4)"
                        style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <div class="p-avatar"
                            style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #334155; color: #fff; flex-shrink: 0;">
                            <?php if (!empty($datos4['destacado']['foto'])): ?>
                                <img src="<?php echo htmlspecialchars($datos4['destacado']['foto']); ?>" alt="Perfil"
                                    style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            <?php else: ?>
                                <span><?php echo $datos4['destacado'] ? strtoupper(substr($datos4['destacado']['usuario'], 0, 1)) : 'U'; ?></span>
                            <?php endif; ?>
                        </div>
                        <p style="margin: 0;">
                            <strong>Comentario destacado:</strong>
                            <?php echo $datos4['destacado'] ? htmlspecialchars($datos4['destacado']['contenido']) : 'Sé el primero en comentar esta obra.'; ?>
                        </p>
                    </div>

                    <div id="comentarios-obra-4" style="display: none;">
                        <?php if (!empty($datos4['lista'])): ?>
                            <?php foreach ($datos4['lista'] as $c): ?>
                                <div class="item-comentario"
                                    style="display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-start;">
                                    <div class="p-avatar"
                                        style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden; background: #334155; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <?php if (!empty($c['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($c['foto']); ?>" alt="Perfil"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($c['usuario'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($c['usuario']); ?></strong>
                                        <span style="color: #f59e0b;">(<?php echo $c['calificacion']; ?>/5 ⭐)</span>
                                        <p style="margin: 4px 0;"><?php echo htmlspecialchars($c['contenido']); ?></p>
                                        <small style="color: #64748b;"><?php echo $c['fecha']; ?></small>
                                    </div>
                                </div>
                                <hr style="border: 0.5px solid #eee; margin: 8px 0;">
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aún no hay comentarios en esta obra.</p>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </section>

        <section>
            <h2 class="section-title">Fotografía</h2>
            <div class="popular-grid">
                <?php $datos12 = obtenerDatosObra($conexion, 12); ?>

                <article class="p-card">
                    <a href="ver_obra.php?id=12" style="text-decoration:none; color:inherit;">
                        <div class="p-card-header">
                            <div class="author-placeholder">
                                <img src="IMG/meliton.jpg" alt="Fotografía">
                            </div>
                            <div class="p-header-text">
                                <h3> La Mirada sobre la Ciudad</h3>
                                <p class="p-subtitle">La fotografía en Medellín no es un simple registro estético; es la memoria viva de sus transformaciones, sus contrastes y sus rostros. </p>
                            </div>
                        </div>
                        <div class="p-card-body">
                            <p>...</p>
                        </div>
                    </a>

                    <div class="p-card-rating">
                        <span>CALIFICACIÓN ·
                            <?php echo ($datos12['promedio'] > 0) ? $datos12['promedio'] : 'S/C'; ?>/5</span>
                        <div class="p-stars">
                            <?php echo $datos12['estrellas']; ?>
                        </div>
                    </div>

                    <div class="p-card-footer" onclick="abrirModalComentarios(12)"
                        style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <div class="p-avatar"
                            style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #334155; color: #fff; flex-shrink: 0;">
                            <?php if (!empty($datos12['destacado']['foto'])): ?>
                                <img src="<?php echo htmlspecialchars($datos12['destacado']['foto']); ?>" alt="Perfil"
                                    style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            <?php else: ?>
                                <span><?php echo $datos12['destacado'] ? strtoupper(substr($datos12['destacado']['usuario'], 0, 1)) : 'U'; ?></span>
                            <?php endif; ?>
                        </div>
                        <p style="margin: 0;">
                            <strong>Comentario destacado:</strong>
                            <?php echo $datos12['destacado'] ? htmlspecialchars($datos12['destacado']['contenido']) : 'Sé el primero en comentar esta obra.'; ?>
                        </p>
                    </div>

                    <div id="comentarios-obra-12" style="display: none;">
                        <?php if (!empty($datos12['lista'])): ?>
                            <?php foreach ($datos12['lista'] as $c): ?>
                                <div class="item-comentario"
                                    style="display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-start;">
                                    <div class="p-avatar"
                                        style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden; background: #334155; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <?php if (!empty($c['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($c['foto']); ?>" alt="Perfil"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($c['usuario'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($c['usuario']); ?></strong>
                                        <span style="color: #f59e0b;">(<?php echo $c['calificacion']; ?>/5 ⭐)</span>
                                        <p style="margin: 4px 0;"><?php echo htmlspecialchars($c['contenido']); ?></p>
                                        <small style="color: #64748b;"><?php echo $c['fecha']; ?></small>
                                    </div>
                                </div>
                                <hr style="border: 0.5px solid #eee; margin: 8px 0;">
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aún no hay comentarios en esta obra.</p>
                        <?php endif; ?>
                    </div>
                </article>


                <?php $datos13 = obtenerDatosObra($conexion, 13); ?>

                <article class="p-card">
                    <a href="ver_obra.php?id=13" style="text-decoration:none; color:inherit;">
                        <div class="p-card-header">
                            <div class="author-placeholder">
                                <img src="IMG/maria.webp" alt="Selección de libros">
                            </div>
                            <div class="p-header-text">
                                <h3> Retrato de María Cano Márquez en su juventud</h3>
                                <p class="p-subtitle">Cuando Benjamín de la Calle toma este retrato, María Cano (nacida en 1887) tenía entre 23 y 28 años. Todavía no era la figura masiva de la política colombiana conocida como «La Flor del Trabajo» (rol que asumiría hacia 1925 en las huelgas petroleras y bananeras).</p>
                            </div>
                        </div>
                        <div class="p-card-body">
                            <p>...</p>
                        </div>
                    </a>

                    <div class="p-card-rating">
                        <span>CALIFICACIÓN ·
                            <?php echo ($datos13['promedio'] > 0) ? $datos13['promedio'] : 'S/C'; ?>/5</span>
                        <div class="p-stars">
                            <?php echo $datos13['estrellas']; ?>
                        </div>
                    </div>

                    <div class="p-card-footer" onclick="abrirModalComentarios(13)"
                        style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <div class="p-avatar"
                            style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #334155; color: #fff; flex-shrink: 0;">
                            <?php if (!empty($datos13['destacado']['foto'])): ?>
                                <img src="<?php echo htmlspecialchars($datos13['destacado']['foto']); ?>" alt="Perfil"
                                    style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            <?php else: ?>
                                <span><?php echo $datos13['destacado'] ? strtoupper(substr($datos13['destacado']['usuario'], 0, 1)) : 'U'; ?></span>
                            <?php endif; ?>
                        </div>
                        <p style="margin: 0;">
                            <strong>Comentario destacado:</strong>
                            <?php echo $datos13['destacado'] ? htmlspecialchars($datos13['destacado']['contenido']) : 'Sé el primero en comentar esta obra.'; ?>
                        </p>
                    </div>

                    <div id="comentarios-obra-13" style="display: none;">
                        <?php if (!empty($datos13['lista'])): ?>
                            <?php foreach ($datos13['lista'] as $c): ?>
                                <div class="item-comentario"
                                    style="display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-start;">
                                    <div class="p-avatar"
                                        style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden; background: #334155; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <?php if (!empty($c['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($c['foto']); ?>" alt="Perfil"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($c['usuario'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($c['usuario']); ?></strong>
                                        <span style="color: #f59e0b;">(<?php echo $c['calificacion']; ?>/5 ⭐)</span>
                                        <p style="margin: 4px 0;"><?php echo htmlspecialchars($c['contenido']); ?></p>
                                        <small style="color: #64748b;"><?php echo $c['fecha']; ?></small>
                                    </div>
                                </div>
                                <hr style="border: 0.5px solid #eee; margin: 8px 0;">
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aún no hay comentarios en esta obra.</p>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </section>

        <a href="crear_obra.php" class="btn-floating-create" title="Crear nueva obra">
        <i class="fas fa-pen"></i>
        </a>

    </main>

    <!--To Top-->>
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