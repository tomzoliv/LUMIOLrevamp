<?php
session_start();
require_once 'conexion.php';

function formatear_texto($texto)
{
    $texto = htmlspecialchars($texto);
    $texto = preg_replace('/\[(.*?)\]\((https?:\/\/[^\s\)]+)\)/i', '<a href="$2" target="_blank" rel="noopener noreferrer" style="color: #a83232; text-decoration: underline;">$1</a>', $texto);
    $texto = preg_replace('/(?<!href="|">)(https?:\/\/[^\s<]+)/i', '<a href="$1" target="_blank" rel="noopener noreferrer" style="color: #a83232; text-decoration: underline;">$1</a>', $texto);
    $texto = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $texto);
    $texto = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $texto);

    return nl2br($texto);
}

$id_obra = isset($_GET['id']) ? intval($_GET['id']) : 0;
$usuario_actual_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : (isset($_SESSION['id']) ? $_SESSION['id'] : 0);

if ($id_obra <= 0) {
    header("Location: explorar.php");
    exit;
}


$stmt = $conexion->prepare("
    SELECT o.*, u.usuario AS nombre_autor 
    FROM obras o 
    LEFT JOIN usuarios u ON o.id_usuario = u.id 
    WHERE o.id = ? OR o.Id = ?
");
$stmt->bind_param("ii", $id_obra, $id_obra);
$stmt->execute();
$obra = $stmt->get_result()->fetch_assoc();

if (!$obra) {
    header("Location: explorar.php");
    exit;
}

$id_autor_obra = $obra['id_usuario'] ?? 0;


$stmt_comentarios = $conexion->prepare("
    SELECT 
        c.*, 
        u.usuario AS nombre_comentador, 
        u.foto 
    FROM comentarios c 
    LEFT JOIN usuarios u ON c.id_usuario = u.id 
    WHERE c.id_obra = ? 
    ORDER BY c.fecha DESC
");
$stmt_comentarios->bind_param("i", $id_obra);
$stmt_comentarios->execute();
$comentarios = $stmt_comentarios->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumiol - <?php echo htmlspecialchars($obra['titulo']); ?></title>
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
                    <li><a href="explorar.php" class="active">EXPLORAR</a></li>
                    <li><a href="legado.php">LEGADO</a></li>
                </ul>
            </nav>
        </header>
    </div>

    <main class="container">
        <div class="obra-container">
            <a href="explorar.php" class="btn-back">&larr; Volver a Explorar</a>

            <?php if (!empty($obra['archivo'])): ?>
                <img src="<?php echo htmlspecialchars($obra['archivo']); ?>"
                    alt="<?php echo htmlspecialchars($obra['titulo']); ?>" class="obra-banner">
            <?php endif; ?>

            <div class="header-obra-info">
                <div>
                    <span class="obra-category"><?php echo htmlspecialchars($obra['categoria']); ?></span>
                    <h1 class="obra-title"><?php echo htmlspecialchars($obra['titulo']); ?></h1>
                </div>

                <?php if ($usuario_actual_id > 0 && $usuario_actual_id == $id_autor_obra): ?>
                    <div class="options-dropdown">
                        <button class="dots-btn" onclick="toggleDropdown('dropdown-obra')"><i class="fas fa-ellipsis-v"></i></button>
                        <div id="dropdown-obra" class="dropdown-menu">
                            <a href="editar_obra.php?id=<?php echo $obra['id'] ?? $obra['Id']; ?>"><i class="fas fa-pen" style="color:#a83232;"></i> Editar</a>
                            <a href="eliminar_obra.php?id=<?php echo $obra['id'] ?? $obra['Id']; ?>" class="opt-eliminar" onclick="return confirm('¿Seguro que deseas eliminar esta obra?');"><i class="fas fa-trash-alt"></i> Eliminar</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <p class="obra-author">
                Publicado por: <?php echo htmlspecialchars($obra['nombre_autor'] ?? 'Comunidad Lumiol'); ?>
            </p>

            <div class="obra-content">
                <?php echo formatear_texto($obra['descripcion'] ?? ''); ?>
            </div>

            <?php if (!empty($obra['archivo_audio'])): ?>
                <div class="audio-player-container" style="margin: 20px 0;">
                    <audio controls style="width: 100%;">
                        <source src="<?php echo htmlspecialchars($obra['archivo_audio']); ?>">
                        Tu navegador no soporta el elemento de audio.
                    </audio>
                </div>
            <?php endif; ?>

            <section class="comentarios-section">
                <h3>Comentarios</h3>

                <?php if ($comentarios && $comentarios->num_rows > 0): ?>
                    <?php while ($com = $comentarios->fetch_assoc()): ?>
                        <div class="comentario-item">
                            <div style="display: flex; gap: 12px; align-items: flex-start; width: 100%;">

                                <div class="comentario-avatar">
                                    <?php if (!empty($com['foto']) && file_exists($com['foto'])): ?>
                                        <img src="<?php echo htmlspecialchars($com['foto']); ?>"
                                            alt="<?php echo htmlspecialchars($com['nombre_comentador']); ?>"
                                            style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #1e293b; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                            <?php echo strtoupper(substr($com['nombre_comentador'] ?? 'U', 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div style="flex-grow: 1;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span class="comentario-user"><?php echo htmlspecialchars($com['nombre_comentador'] ?? 'Usuario'); ?></span>

                                        <?php if (!empty($com['calificacion'])): ?>
                                            <span style="color: #f59e0b; font-weight: bold; font-size: 14px;">
                                                (<?php echo $com['calificacion']; ?>/5 ⭐)
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <p class="comentario-texto"><?php echo htmlspecialchars($com['contenido']); ?></p>
                                    <span class="comentario-fecha"><?php echo $com['fecha']; ?></span>
                                </div>
                            </div>

                            <?php if ($usuario_actual_id > 0 && $usuario_actual_id == $com['id_usuario']): ?>
                                <div class="options-dropdown">
                                    <button class="dots-btn" onclick="toggleDropdown('dropdown-com-<?php echo $com['id']; ?>')">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="dropdown-com-<?php echo $com['id']; ?>" class="dropdown-menu">
                                        <a href="javascript:void(0)" onclick="editarComentario(<?php echo $com['id']; ?>, '<?php echo addslashes($com['contenido']); ?>')">
                                            <i class="fas fa-pen" style="color:#a83232;"></i> Editar
                                        </a>
                                        <a href="eliminar_comentario.php?id=<?php echo $com['id']; ?>&obra_id=<?php echo $id_obra; ?>" class="opt-eliminar" onclick="return confirm('¿Eliminar comentario?');">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color:#777;">No hay comentarios aún.</p>
                <?php endif; ?>
            </section>
        </div>

        <?php if (isset($_SESSION['usuario_id'])): ?>
            <form action="guardar_comentario.php" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="id_obra" value="<?php echo $obra['id'] ?? $obra['Id']; ?>">

                <label for="calificacion"><strong>Califica esta obra:</strong></label>
                <select name="calificacion" id="calificacion" required>
                    <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                    <option value="4">⭐⭐⭐⭐ (4/5)</option>
                    <option value="3">⭐⭐⭐ (3/5)</option>
                    <option value="2">⭐⭐ (2/5)</option>
                    <option value="1">⭐ (1/5)</option>
                </select>

                <br><br>

                <textarea name="contenido" placeholder="Escribe tu comentario..." required style="width: 100%; height: 80px;"></textarea>

                <br><br>

                <button type="submit" style="padding: 8px 16px; background-color: #1e293b; color: white; border: none; cursor: pointer;">
                    Publicar Comentario
                </button>
                <br><br>
            </form>
        <?php else: ?>
            <p><a href="login.php">Inicia sesión</a> para dejar un comentario y calificar.</p>
        <?php endif; ?>

    </main>

    <script>
        function toggleDropdown(id) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== id) menu.classList.remove('show');
            });
            document.getElementById(id).classList.toggle('show');
        }

        window.onclick = function(event) {
            if (!event.target.matches('.dots-btn') && !event.target.matches('.dots-btn *')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        }

        function editarComentario(idComentario, textoActual) {
            let nuevoTexto = prompt("Edita tu comentario:", textoActual);
            if (nuevoTexto !== null && nuevoTexto.trim() !== "") {
                let form = document.createElement("form");
                form.method = "POST";
                form.action = "editar_comentario.php";

                let inputId = document.createElement("input");
                inputId.type = "hidden";
                inputId.name = "id_comentario";
                inputId.value = idComentario;

                let inputObraId = document.createElement("input");
                inputObraId.type = "hidden";
                inputObraId.name = "id_obra";
                inputObraId.value = "<?php echo $id_obra; ?>";

                let inputTexto = document.createElement("input");
                inputTexto.type = "hidden";
                inputTexto.name = "comentario";
                inputTexto.value = nuevoTexto;

                form.appendChild(inputId);
                form.appendChild(inputObraId);
                form.appendChild(inputTexto);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

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