<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?msg=debes_iniciar_sesion');
    exit;
}

$mensaje = "";
$titulo_obra = "";
$categoria_obra = "";
$contenido_obra = "";
$archivo_obra = "";
$archivo_audio_obra = "";
$obra_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_usuario = $_SESSION['usuario_id'];

if ($obra_id > 0) {
    $stmt_edit = $conexion->prepare("SELECT * FROM obras WHERE (Id = ? OR id = ?) AND id_usuario = ?");
    $stmt_edit->bind_param("iii", $obra_id, $obra_id, $id_usuario);
    $stmt_edit->execute();
    $res = $stmt_edit->get_result();

    if ($obra_datos = $res->fetch_assoc()) {
        $titulo_obra = $obra_datos['titulo'] ?? '';
        $categoria_obra = $obra_datos['categoria'] ?? '';
        $contenido_obra = $obra_datos['descripcion'] ?? $obra_datos['contenido'] ?? '';
        $archivo_obra = $obra_datos['archivo'] ?? '';
        $archivo_audio_obra = $obra_datos['archivo_audio'] ?? '';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $categoria = trim($_POST['categoria']);
    $contenido = trim($_POST['contenido']);
    $estado = isset($_POST['guardar_borrador']) ? 'borrador' : 'publicado';
    $obra_id = intval($_POST['obra_id'] ?? 0);

    $ruta_archivo = $archivo_obra ?: "IMG/placeholder.jpeg";
    $ruta_audio = $archivo_audio_obra;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ruta_destino = 'uploads/' . time() . '_' . basename($_FILES['imagen']['name']);
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            $ruta_archivo = $ruta_destino;
        }
    }

    if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $ruta_destino_audio = 'uploads/audio_' . time() . '_' . basename($_FILES['audio']['name']);
        if (move_uploaded_file($_FILES['audio']['tmp_name'], $ruta_destino_audio)) {
            $ruta_audio = $ruta_destino_audio;
        }
    }

    if (!empty($titulo) && !empty($categoria)) {
        $tipo_contenido = "Publicación";

        if ($obra_id > 0) {

            $stmt = $conexion->prepare("UPDATE obras SET titulo = ?, categoria = ?, tipo_contenido = ?, descripcion = ?, archivo = ?, archivo_audio = ?, estado = ? WHERE (Id = ? OR id = ?) AND id_usuario = ?");
            $stmt->bind_param("sssssssiii", $titulo, $categoria, $tipo_contenido, $contenido, $ruta_archivo, $ruta_audio, $estado, $obra_id, $obra_id, $id_usuario);
        } else {

            $stmt = $conexion->prepare("INSERT INTO obras (id_usuario, titulo, categoria, tipo_contenido, descripcion, archivo, archivo_audio, estado, fecha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssssss", $id_usuario, $titulo, $categoria, $tipo_contenido, $contenido, $ruta_archivo, $ruta_audio, $estado);
        }

        if ($stmt->execute()) {
            if ($estado === 'borrador') {
                header('Location: actividad.php?tab=borradores');
            } else {
                header('Location: explorar.php');
            }
            exit;
        } else {
            $mensaje = "Error al guardar la obra: " . $conexion->error;
        }
    } else {
        $mensaje = "Por favor selecciona una categoría y un título.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($obra_id > 0) ? 'Editar Borrador' : 'Crear Obra'; ?> · LUMIOL</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

<body class="create-page-body">

    <main class="create-container">
        <form action="crear_obra.php<?php echo ($obra_id > 0) ? '?id=' . $obra_id : ''; ?>" method="POST"
            enctype="multipart/form-data" id="form-crear">

            <input type="hidden" name="obra_id" value="<?php echo $obra_id; ?>">


            <div class="create-header">
                <select name="categoria" class="select-category" required>
                    <option value="" disabled <?php echo empty($categoria_obra) ? 'selected' : ''; ?>>Seleccionar
                        categoría</option>
                    <option value="Literatura" <?php echo ($categoria_obra === 'Literatura') ? 'selected' : ''; ?>>
                        Literatura</option>
                    <option value="Artes visuales" <?php echo ($categoria_obra === 'Artes visuales') ? 'selected' : ''; ?>>Artes visuales</option>
                    <option value="Música" <?php echo ($categoria_obra === 'Música') ? 'selected' : ''; ?>>Música</option>
                    <option value="Fotografía" <?php echo ($categoria_obra === 'Fotografía') ? 'selected' : ''; ?>>
                        Fotografía</option>
                    <option value="Teatro" <?php echo ($categoria_obra === 'Teatro') ? 'selected' : ''; ?>>Teatro</option>
                </select>

                <a href="actividad.php?tab=borradores" class="link-drafts">Borradores</a>
            </div>

            <?php if (!empty($mensaje)): ?>
                <p class="error-msg"><?php echo $mensaje; ?></p>
            <?php endif; ?>


            <input type="text" name="titulo" class="input-title" placeholder="Título*"
                value="<?php echo htmlspecialchars($titulo_obra); ?>" required>


            <div class="image-preview-container" id="preview-wrapper"
                style="<?php echo empty($archivo_obra) ? 'display: none;' : ''; ?>">
                <img id="image-preview" src="<?php echo htmlspecialchars($archivo_obra); ?>"
                    alt="Vista previa de la obra">
            </div>


            <textarea name="contenido" id="editor-content" class="editor-textarea"
                placeholder="Texto o descripción (opcional)"><?php echo htmlspecialchars($contenido_obra); ?></textarea>

            <div id="audio-preview-wrapper" style="<?php echo empty($archivo_audio_obra) ? 'display: none;' : ''; ?> margin: 15px 0;">
                <audio id="audio-preview" src="<?php echo htmlspecialchars($archivo_audio_obra ?? ''); ?>" controls style="width: 100%; border-radius: 30px;">
                    Tu navegador no soporta el reproductor de audio.
                </audio>
            </div>

            <input type="file" name="imagen" id="file-input" style="display: none;" accept="image/*"
                onchange="previewSelectedImage(event)">

            <input type="file" name="audio" id="audio-input" style="display: none;" accept="audio/*" onchange="previewSelectedAudio(event)">


            <div class="editor-toolbar">
                <div class="toolbar-tools">
                    <button type="button" class="tool-btn" onclick="document.getElementById('file-input').click()"
                        title="Insertar Imagen"><i class="fas fa-image"></i></button>
                    <button type="button" class="tool-btn" onclick="insertFormat('link')" title="Insertar Enlace"><i
                            class="fas fa-link"></i></button>
                    <button type="button" class="tool-btn" onclick="insertFormat('bold')" title="Negrita"><i
                            class="fas fa-bold"></i></button>
                    <button type="button" class="tool-btn" onclick="insertFormat('italic')" title="Itálica"><i
                            class="fas fa-italic"></i></button>
                    <button type="button" class="tool-btn" onclick="insertFormat('list')" title="Lista con viñetas"><i
                            class="fas fa-list-ul"></i></button>
                    <button type="button" class="tool-btn" onclick="document.getElementById('audio-input').click()" title="Subir Audio">
                        <i class="fas fa-music"></i>
                    </button>
                </div>

                <div class="toolbar-actions">
                    <button type="submit" name="guardar_borrador" class="btn-save-draft">Guardar borrador</button>
                    <button type="submit" name="publicar" class="btn-publish">Publicar</button>
                </div>
            </div>

        </form>
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

    <script>
        function previewSelectedImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('image-preview');
                    const previewWrapper = document.getElementById('preview-wrapper');
                    previewImg.src = e.target.result;
                    previewWrapper.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }

        function insertFormat(type) {
            const textarea = document.getElementById('editor-content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);

            let replacement = '';

            switch (type) {
                case 'bold':
                    replacement = `**${selectedText || 'texto en negrita'}**`;
                    break;
                case 'italic':
                    replacement = `*${selectedText || 'texto en itálica'}*`;
                    break;
                case 'link':
                    replacement = `[${selectedText || 'texto enlace'}](https://)`;
                    break;
                case 'list':
                    replacement = `\n- ${selectedText || 'Elemento de lista'}`;
                    break;
            }

            textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
            textarea.focus();
        }

        function previewSelectedAudio(event) {
            const file = event.target.files[0];
            if (file) {
                const audioPlayer = document.getElementById('audio-preview');
                const previewWrapper = document.getElementById('audio-preview-wrapper');
                audioPlayer.src = URL.createObjectURL(file);
                previewWrapper.style.display = 'block';
            }
        }
    </script>

</body>

</html>