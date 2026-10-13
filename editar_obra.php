<?php
session_start();
require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'] ?? ($_SESSION['id'] ?? 0);
if ($usuario_id <= 0) {
    header("Location: login.php");
    exit;
}

$id_obra = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id_obra']) ? intval($_POST['id_obra']) : 0);

$stmt = $conexion->prepare("SELECT * FROM obras WHERE (id = ? OR Id = ?) AND id_usuario = ?");
$stmt->bind_param("iii", $id_obra, $id_obra, $usuario_id);
$stmt->execute();
$obra = $stmt->get_result()->fetch_assoc();

if (!$obra) {
    echo "<script>alert('No tienes permiso para editar esta obra.'); window.location.href='explorar.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $categoria = trim($_POST['categoria']);
    $descripcion = trim($_POST['descripcion']);
    $estado = isset($_POST['guardar_borrador']) ? 'borrador' : 'publicado';

    $archivo_ruta = $obra['archivo'];
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = time() . '_' . basename($_FILES['archivo']['name']);
        $destino = 'uploads/' . $nombre_archivo;
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) {
            $archivo_ruta = $destino;
        }
    }

    $audio_ruta = $obra['archivo_audio'] ?? '';
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $nombre_audio = time() . '_audio_' . basename($_FILES['audio']['name']);
        $destino_audio = 'uploads/' . $nombre_audio;
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        if (move_uploaded_file($_FILES['audio']['tmp_name'], $destino_audio)) {
            $audio_ruta = $destino_audio;
        }
    }

    $stmt_upd = $conexion->prepare("UPDATE obras SET titulo = ?, categoria = ?, descripcion = ?, archivo = ?, archivo_audio = ?, estado = ? WHERE (id = ? OR Id = ?) AND id_usuario = ?");
    $stmt_upd->bind_param("ssssssiii", $titulo, $categoria, $descripcion, $archivo_ruta, $audio_ruta, $estado, $id_obra, $id_obra, $usuario_id);

    if ($stmt_upd->execute()) {
        header("Location: ver_obra.php?id=" . $id_obra);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Obra - Lumiol</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

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

<body>
    <main class="container">
        <form action="editar_obra.php" method="POST" enctype="multipart/form-data" class="editor-container">
            <input type="hidden" name="id_obra" value="<?php echo $obra['id'] ?? $obra['Id']; ?>">

            <div class="editor-header">
                <select name="categoria" class="cat-select" required>
                    <option value="Literatura" <?php echo ($obra['categoria'] === 'Literatura') ? 'selected' : ''; ?>>
                        Literatura</option>
                    <option value="Artes Visuales" <?php echo ($obra['categoria'] === 'Artes Visuales') ? 'selected' : ''; ?>>Artes Visuales</option>
                    <option value="Fotografía" <?php echo ($obra['categoria'] === 'Fotografía') ? 'selected' : ''; ?>>
                        Fotografía</option>
                    <option value="Música" <?php echo ($obra['categoria'] === 'Música') ? 'selected' : ''; ?>>Música
                    </option>
                    <option value="Teatro" <?php echo ($obra['categoria'] === 'Teatro') ? 'selected' : ''; ?>>Teatro
                    </option>
                </select>

            </div>

            <input type="text" name="titulo" class="title-input"
                value="<?php echo htmlspecialchars($obra['titulo']); ?>" placeholder="Título*" required>

            <div class="img-preview-container">
                <img id="img-preview"
                    src="<?php echo htmlspecialchars(!empty($obra['archivo']) ? $obra['archivo'] : 'IMG/lumiollogo.png'); ?>"
                    alt="Previsualización">
                <label for="input-file" class="change-img-btn"><i class="fas fa-camera"></i> Cambiar Imagen</label>
                <input type="file" id="input-file" name="archivo" accept="image/*" style="display:none;"
                    onchange="previewImage(event)">
            </div>

            <textarea name="descripcion" class="text-editor"
                placeholder="Texto o descripción (opcional)"><?php echo htmlspecialchars($obra['descripcion']); ?></textarea>

            <div id="audio-preview-wrapper" style="<?php echo empty($obra['archivo_audio']) ? 'display: none;' : ''; ?> margin: 15px 0;">
                <audio id="audio-preview" controls style="width: 100%; border-radius: 30px;">
                    <source src="<?php echo htmlspecialchars($obra['archivo_audio'] ?? ''); ?>">
                    Tu navegador no soporta el reproductor de audio.
                </audio>
            </div>

            <input type="file" id="input-audio" name="audio" accept="audio/*" style="display:none;" onchange="previewAudio(event)">

            <div class="editor-toolbar">
                <div class="toolbar-tools">
                    <button type="button" class="tool-btn" onclick="document.getElementById('input-file').click()"
                        title="Insertar Imagen">
                        <i class="fas fa-image"></i>
                    </button>
                    <button type="button" class="tool-btn" onclick="document.getElementById('input-audio').click()"
                        title="Insertar Audio">
                        <i class="fas fa-music"></i>
                    </button>
                    <button type="button" class="tool-btn" onmousedown="insertFormat('link', event)"
                        title="Insertar Enlace">
                        <i class="fas fa-link"></i>
                    </button>
                    <button type="button" class="tool-btn" onmousedown="insertFormat('bold', event)" title="Negrita">
                        <i class="fas fa-bold"></i>
                    </button>
                    <button type="button" class="tool-btn" onmousedown="insertFormat('italic', event)" title="Itálica">
                        <i class="fas fa-italic"></i>
                    </button>
                    <button type="button" class="tool-btn" onmousedown="insertFormat('list', event)"
                        title="Lista con viñetas">
                        <i class="fas fa-list-ul"></i>
                    </button>
                </div>

                <div class="action-buttons">
                    <button type="submit" name="guardar_borrador" class="btn-draft">Guardar borrador</button>
                    <button type="submit" class="btn-save">Guardar cambios</button>
                </div>
            </div>
        </form>
    </main>

    <script>
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    const output = document.getElementById('img-preview');
                    output.src = reader.result;
                }
                if (event.target.files[0]) {
                    reader.readAsDataURL(event.target.files[0]);
                }
            }

        function previewAudio(event) {
            const file = event.target.files[0];
            const previewWrapper = document.getElementById('audio-preview-wrapper');
            const audioPlayer = document.getElementById('audio-preview');

            if (file) {
                audioPlayer.src = URL.createObjectURL(file);
                audioPlayer.load();
                previewWrapper.style.display = 'block';
            }
        }
    </script>

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