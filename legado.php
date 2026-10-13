<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lumiol - Legado</title>
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
        <li><a href="actividad.php">ACTIVIDAD</a></li>
        <li><a href="explorar.php">EXPLORAR</a></li>
        <li><a href="legado.php" class="active">LEGADO</a></li>
      </ul>
    </nav>
  </header>

  <main class="container main-content legado-layout">

    <aside class="legado-sidebar">
      <h2 class="sidebar-main-title">RUTA CULTURAL DEL MES</h2>
      <h4 class="comuna-title">COMUNA 10 · EL CENTRO</h4>

      <div class="sidebar-divider"></div>

      <div class="ruta-lugar">
        <h5><i class="far fa-dot-circle"></i> TEATRO LIDO</h5>
        <p class="lugar-desc">Música clásica y lucha por la identidad. Escuche su "Concierto en La Alpujarra".</p>
        <audio src="audio/Intermezzo.mp3" controls class="lugar-link"></audio>
      </div>

      <div class="sidebar-divider"></div>

      <div class="ruta-lugar">
        <h5><i class="far fa-dot-circle"></i> CAFÉ PASAJE</h5>
        <p class="lugar-desc">Pensamiento crítico y bohemia. Fragmento: "Café Pasaje de las culturas: música y dormitorio".</p>
        <a href="ver_obra.php?id=24" style="text-decoration:none; color: #b84a39;" class="lugar-link">Leer fragmento &rarr;</a>
      </div>
    </aside>

    <section class="map-center-container">
      <div class="map-graphic-box">

        <div class="polygon-map" id="map-grid">
          
          <div class="comuna-cell" data-comuna="1" data-nombre="UVA de la Esperanza" data-img="IMG/uva.jpeg"
            data-desc="Nació sobre los antiguos tanques de agua e infraestructura de servicios públicos de EPM para transformarse en un parque urbano abierto e incluyente. En este espacio convergen la música, el teatro y la animación a la lectura. Cuenta con amplias terrazas, chorros de agua interactivos, aulas para talleres comunitarios, exposiciones itinerantes de artistas locales y proyecciones de cine al aire libre. Es un símbolo de resiliencia y participación comunitaria para el sector."
            data-direccion="Calle 96 # 34 - 100, Barrio San Pablo"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Calle+96+%23+34+-+100+Barrio+San+Pablo+Medellin">
            <span>Comuna 1</span>
          </div>

          <div class="comuna-cell" data-comuna="2" data-nombre="La Casa Amarilla" data-img="IMG/comunados.jpeg"
            data-desc="Un espacio independiente de teatro, literatura y artes de calle que por más de tres décadas ha funcionado como faro cultural en el nororiente de la ciudad. Allí se llevan a cabo temporadas teatrales, talleres de actuación, títeres, música y laboratorios de dramaturgia comunitaria. La estructura de la casa es un referente artístico donde la juventud de Santa Cruz se forma en la creación escénica y la gestión social."
            data-direccion="Calle 99 # 50C - 38, Barrio Santa Cruz / La Rosa"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Calle+99+%23+50C+-+38+Medellin">
            <span>Comuna 2</span>
          </div>

          <div class="comuna-cell" data-comuna="3" data-nombre="Casa de la Cultura Manrique" data-img="IMG/manrique.jfif"
            data-desc="Una de las sedes culturales más icónicas de la zona norte, vinculada a la memoria del tango y las expresiones urbanas. Este equipamiento público dispone de laboratorios de producción de sonido, estudios de grabación, salas de ensayo para danza y escenarios donde se realizan muestras artísticas, conciertos locales, clases de literatura y exposiciones de pintura de jóvenes emergentes."
            data-direccion="Carrera 45 # 72 - 62, Barrio El Pomar"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Carrera+45+%23+72+-+62+Medellin">
            <span>Comuna 3</span>
          </div>

          <div class="comuna-cell" data-comuna="4" data-nombre="Jardín Botánico" data-img="IMG/botanico.jpeg" 
            data-desc="Más allá de ser un pulmón verde y santuario ecológico de la ciudad, es un pilar cultural y arquitectónico emblemático. En sus áreas abiertas y en el afamado Orquideorama se desarrollan eventos masivos de la ciudad como la Fiesta del Libro y la Cultura, recitales de poesía, ferias artesanales, exposiciones fotográficas y conciertos al aire libre integrados con la biodiversidad local."
            data-direccion="Calle 73 # 51D - 14, Aranjuez"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Calle+73+%23+51D+-+14+Medellin">
            <span>Comuna 4</span>
          </div>

          <div class="comuna-cell" data-comuna="5" data-nombre="Parque Juanes de la Paz" data-img="IMG/pjuanez.jfif" 
            data-desc="El Parque Juanes de la Paz es un espacio público ubicado en el barrio Castilla, en el noroccidente de Medellín. Fue construido como un espacio de recreación, encuentro comunitario y práctica deportiva. Cuenta con zonas verdes, canchas deportivas, cicloruta, espacios para actividades físicas y áreas destinadas a la recreación.
            El parque tiene además un significado especial para la historia reciente de Castilla. Su creación estuvo relacionada con iniciativas impulsadas por el cantante Juanes y su Fundación Mi Sangre, con especial atención a personas afectadas por las minas antipersona y a comunidades con menores recursos."
            data-direccion="Carrera 65G # 14 - 115, Barrio Guayabal"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Parque+Biblioteca+Guayabal+Medellin">
            <span>Comuna 5</span>
          </div>

          <div class="comuna-cell" data-comuna="6" data-nombre="Centro Cultural Pedregal" data-img="IMG/cultural.jpeg"
            data-desc="Es el principal dinamizador artístico de la Comuna 6. Cuenta con un teatro al aire libre para presentaciones masivas de danza, música folclórica y obras de teatro. Dentro de sus instalaciones se ofrecen talleres gratuitos de pintura, escultura, fotografía, comparsas y artes aplicadas, consolidándose como un espacio vital para las expresiones comunitarias y la vida del barrio."
            data-direccion="Calle 104 # 74 - 120, Barrio Pedregal"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Casa+de+la+Cultura+Pedregal+Medellin">
            <span>Comuna 6</span>
          </div>

          <div class="comuna-cell" data-comuna="7" data-nombre="Casa de la Literatura San Germán" data-img="IMG/casalit.jpeg" 
            data-desc="Una casona tradicional rehabilitada e integrada a la Red de Bibliotecas que promueve exclusivamente la creación y apreciación literaria. Posee salas de lectura rodeadas de jardines, clubes de escritura creativa, salones para talleres de narrativa, poesía e ilustración, además de una agenda de lanzamientos de libros e intercambios culturales entre autores y la comunidad."
            data-direccion="Calle 63 # 75 - 86, Sector San Germán"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Casa+de+la+Literatura+San+German+Medellin">
            <span>Comuna 7</span>
          </div>

          <div class="comuna-cell" data-comuna="8" data-nombre="Parque Biblioteca León de Greiff" data-img="IMG/parqueleon.jpeg"
            data-desc=" Diseñado como un mirador hacia la ladera centro-oriental, rinde tributo al poeta León de Greiff. Ofrece salas de consulta bibliográfica, talleres de escritura, auditorios para obras teatrales e interpretación musical, y terrazas donde periódicamente se organizan tertulias literarias, proyecciones cinematográficas y exposiciones históricas sobre el desarrollo del sector."
            data-direccion="Calle 59A # 36 - 30, Barrio La Ladera, Villa Hermosa"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Parque+Biblioteca+Leon+de+Greiff+La+Ladera+Medellin">
            <span>Comuna 8</span>
          </div>

          <div class="comuna-cell" data-comuna="9" data-nombre="Casa de la Cultura Ávila" data-img="IMG/libreria.jfif"
            data-desc="Epicentro de encuentro para colectivos artísticos de la zona centro-oriental. Alberga salones para la práctica de danza folclórica y urbana, salas de artes plásticas donde se dictan cursos de grabado y pintura, y una agenda con muestras de artes vivas, comparsas de carnaval, festivales infantiles y recitales de música tradicional."
            data-direccion="Carrera 29 # 38F - 58, Barrio Loreto / Ávila"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Carrera+29+%23+38F+-+58+Medellin">
            <span>Comuna 9</span>
          </div>

          <div class="comuna-cell" data-comuna="10" data-nombre="Teatro Pablo Tobón Uribe" data-img="IMG/teatrop.jfif"
            data-desc="Corazón del circuito dramático y musical del centro. El Pablo Tobón es reconocido por su imponente sala y la plazoleta 'De las Artes', donde se organizan conciertos al aire libre, mercados culturales y festivales. Cerca de allí, el Ateneo acoge la dramaturgia independiente, el cine de autor y foros sobre el legado literario antioqueño."
            data-direccion="Carrera 40 # 51 - 24 (Avenida La Playa)"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Carrera+40+%23+51+-+24+Medellin">
            <span>Comuna 10</span>
          </div>
        
          <div class="comuna-cell" data-comuna="11" data-nombre="Teatro Popular de Medellín" data-img="IMG/teatropopular.jfif" 
            data-desc=" Una institución dramática histórica e independiente de la ciudad. Con décadas de trayectoria, el TPM ofrece temporadas estables de obras teatrales nacionales e internacionales, talleres de formación actoral para jóvenes, montajes dramatúrgicos basados en la literatura colombiana y debates sobre historia del arte y gestión artística."
            data-direccion="Calle 48 # 41 13, Bomboná No.1"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Calle+48+%23+41+-+13+Medellin">
            <span>Comuna 11</span>
          </div>

          <div class="comuna-cell" data-comuna="12" data-nombre="Casa de la Cultura Los Alcázares" data-img="IMG/alcazares.jfif" 
            data-desc="Es el corazón de la creación artística en la Comuna 12. Un espacio dedicado a mantener vivo el legado cultural a través de exposiciones de artes plásticas, talleres de pintura, música, teatro y grupos comunitarios que promueven la identidad y el talento local del sector."
            data-direccion="Calle 48DD # 88A - 11, Barrio Los Alcázares"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Calle+48DD+%23+88A+-+11+Medellin">
            <span>Comuna 12</span>
          </div>

          <div class="comuna-cell" data-comuna="13" data-nombre="Graffitour Comuna 13" data-img="IMG/graffitour.jfif" 
            data-desc=" Un fenómeno mundial de transformación urbana a través de las expresiones del arte callejero, la música hip-hop y la resiliencia. Comprende un extenso recorrido de murales realizados por grafiteros locales e internacionales que narran la historia de la comunidad, acompañado de puestas en escena espontáneas de breakdance, poesía urbana y galerías comunitarias de fotografía e historia oral."
            data-direccion="Barrio 20 de Julio / Sector Escaleras Eléctricas"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Escaleras+Electricas+Comuna+13+Medellin">
            <span>Comuna 13</span>
          </div>

          <div class="comuna-cell" data-comuna="14" data-nombre="Museo de Arte Moderno (MAMM)" data-img="IMG/mamm.jfif" 
            data-desc=" Ubicado en los antiguos talleres de Talleres Robledo, es el epicentro de las artes plásticas contemporáneas de la región. Cuenta con amplias salas de exposiciones, un cine/auditorio para muestras independientes, la biblioteca de artes visuales, tienda de diseño y una explanada abierta que acoge conciertos, festivales gastronómicos y proyecciones de cine de entrada libre."
            data-direccion="Carrera 44 # 19A - 100, Ciudad del Río"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Carrera+44+%23+19A+-+100+Medellin">
            <span>Comuna 14</span>
          </div>

          <div class="comuna-cell" data-comuna="15" data-nombre="Parque Biblioteca Guayabal" data-img="IMG/guayabal.jfif" 
            data-desc=" Un nodo enfocado en la tecnología aplicada al arte, laboratorios de sonido digital, animación y narrativas multimediales. Organiza periódicamente talleres de narrativa gráfica, exposiciones fotográficas de carácter urbano, clubes de lectura temáticos y muestras de cine independiente orientadas a estudiantes y familias del sector sur."
            data-direccion="Carrera 65G # 14 - 115, Barrio Guayabal"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Carrera+65G+%23+14+-+115+Medellin">
            <span>Comuna 15</span>
          </div>

          <div class="comuna-cell" data-comuna="16" data-nombre="Parque Biblioteca Belén" data-img="IMG/belen.jfif"
            data-desc="Diseñado por el arquitecto Hiroshi Naito e integrado por plazas de agua y patios arbolados. Ofrece una infraestructura robusta para el arte: salas con pianos para práctica musical, salas de literatura infantil y juvenil, galerías para exposiciones pictóricas temporales, talleres de escritura y un auditorio con programación continua de cine y obras teatrales."
            data-direccion="Carrera 76 # 18A - 19, Belén"
            data-mapa="https://www.google.com/maps/search/?api=1&query=Carrera+76+%23+18A+-+19+Medellin">
            <span>Comuna 16</span>
          </div>

        </div> 


        <div class="comuna-detail-view" id="comuna-detail" style="display: none;">
          <button type="button" class="btn-close-detail" id="btn-back-map">&larr; Volver al mapa</button>
          <div class="detail-text-wrapper">
            <h3 id="detail-title"></h3>
            <p id="detail-desc"></p>
            <a id="detail-map-link" href="#" target="_blank" rel="noopener noreferrer" class="map-link">
              <i class="fas fa-map-marker-alt"></i> <span id="detail-address"></span>
            </a>
          </div>
        </div>

      </div>
      <br>
      <h4><i class="fa-solid fa-location-dot"></i> MAPA COMUNAS · MEDELLÍN</h4>
    </section>

    <article class="maestro-card" id="dynamic-drawer">
      <div class="card-tabs-nav">
        <button class="card-tab-btn active" data-tab="tab-legado">Legado</button>
      </div>

      <div id="tab-legado" class="card-tab-content active">
        <span class="maestro-tag">MAESTRO DE LEGADO</span>
        <h3 id="tab-legado-title">León de Greiff</h3>
        <div class="maestro-portrait">
          <img id="tab-legado-img" src="IMG/leonxdxdxd.jfif" alt="Fotografía de Artista">
        </div>
        <div class="quote-box">
          <span class="quote-label">FRAGMENTO DESTACADO</span>
          <blockquote id="tab-legado-quote">&ldquo;El tiempo he perdido y he perdido el viaje...&rdquo;</blockquote>
        </div>
      </div>
      <a href="ver_obra.php?id=28" class="btn-full-view">MIRAR PUBLICACIÓN COMPLETA &rarr;</a>
    </article>

    <a href="crear_obra.php" class="btn-floating-create" title="Crear nueva obra">
      <i class="fas fa-pen"></i>
    </a>

  </main>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const cells = document.querySelectorAll('.comuna-cell');
    const gridMap = document.getElementById('map-grid');
    const detailView = document.getElementById('comuna-detail');
    const btnBack = document.getElementById('btn-back-map');

    const detailTitle = document.getElementById('detail-title');
    const detailDesc = document.getElementById('detail-desc');
    const detailAddress = document.getElementById('detail-address');
    const detailMapLink = document.getElementById('detail-map-link');

    cells.forEach(cell => {
      cell.addEventListener('click', (e) => {
        e.preventDefault();

        const nombre = cell.getAttribute('data-nombre');
        const img = cell.getAttribute('data-img');
        const desc = cell.getAttribute('data-desc');
        const direccion = cell.getAttribute('data-direccion');
        const urlMapa = cell.getAttribute('data-mapa');

        if (img && desc) {
          detailView.style.backgroundImage = `url('${img}')`;
          detailTitle.textContent = nombre;
          detailDesc.textContent = desc;

          if (direccion && urlMapa) {
            detailAddress.textContent = direccion;
            detailMapLink.href = urlMapa;
            detailMapLink.style.display = 'inline-block';
          } else {
            detailMapLink.style.display = 'none';
          }

          gridMap.style.setProperty('display', 'none', 'important');
          detailView.style.setProperty('display', 'flex', 'important');
        }
      });
    });

    if (btnBack) {
      btnBack.addEventListener('click', () => {
        detailView.style.setProperty('display', 'none', 'important');
        gridMap.style.setProperty('display', 'grid', 'important');
      });
    }
  });
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

</body>

</html>