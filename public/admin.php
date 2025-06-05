<?php
session_start();

// Verifica si el usuario es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.html');
    exit();
}

// Conexión a la base de datos
$mysqli = new mysqli("localhost", "root", "sql123", "cine");
$mysqli->set_charset("utf8");

if ($mysqli->connect_errno) {
    die("Error al conectar a la base de datos: " . $mysqli->connect_error);
}

// Obtener usuarios
$result_usuarios = $mysqli->query("SELECT id, nombre, email FROM usuarios");

// Obtener películas para eliminar (almacenamos en array para no perder puntero)
$peliculas_eliminar = [];
$result_peliculas = $mysqli->query("SELECT id, titulo, clasificacion, imagen FROM peliculas");
if ($result_peliculas && $result_peliculas->num_rows > 0) {
    while ($fila = $result_peliculas->fetch_assoc()) {
        $peliculas_eliminar[] = $fila;
    }
}

// Obtener películas actuales en otra consulta
$result_peliculas_actuales = $mysqli->query("SELECT id, titulo, clasificacion, imagen FROM peliculas");

// Obtener ventas
$result_ventas = $mysqli->query("
    SELECT 
        r.id,
        u.email AS usuario_email,
        p.titulo AS pelicula_titulo,
        r.total AS boletos,
        r.fecha_reserva AS fecha
    FROM reservas r
    INNER JOIN usuarios u ON r.usuario_id = u.id
    INNER JOIN funciones f ON r.funcion_id = f.id
    INNER JOIN peliculas p ON f.pelicula_id = p.id
    ORDER BY r.fecha_reserva DESC
");


?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Admin Cinetix</title>
  <link rel="stylesheet" href="css/normalize.css" />
  <link href="https://fonts.googleapis.com/css2?family=Krub:wght@400;700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/admin.css" />
</head>
<body>
  <div class="nav-bg">
    <nav class="navegacion-principal contenedor">
      <div class="nav-izquierda">
        <div class="logo"><a href="index.php">C I N E T I X</a></div>
        <a href="index.php">Inicio</a>
      </div>
      <div class="nav-derecha">
        <a href="logout.php">Cerrar sesión</a>
      </div>
    </nav>
  </div>

  <div class="encabezado-cartelera">
    <h1 class="titulo-cartelera">Panel de Administración</h1>
    <p class="descripcion-cartelera">Gestiona el sistema de Cinetix</p>
  </div>

  <main class="contenedor sombra">
    <div class="contenido-cuadro">
      <div class="tabs">
        <button class="tab active" data-tab="agregar" onclick="mostrarTab('agregar')">Agregar Película</button>
        <button class="tab" data-tab="eliminar" onclick="mostrarTab('eliminar')">Eliminar Película</button>
        <button class="tab" data-tab="lista" onclick="mostrarTab('lista')">Películas Actuales</button>
        <button class="tab" data-tab="usuarios" onclick="mostrarTab('usuarios')">Usuarios Registrados</button>
        <button class="tab" data-tab="ventas" onclick="mostrarTab('ventas')">Ventas</button>
      </div>

      <!-- Agregar peli-->
      <section id="agregar" class="tab-content active formulario-admin">
  <h2>Agregar nueva película</h2>
  <form id="formAgregarPelicula" enctype="multipart/form-data">
    <label for="titulo">Título:</label>
    <input type="text" id="titulo" name="titulo" required />

    <label for="clasificacion">Clasificación:</label>
    <select id="clasificacion" name="clasificacion" required>
      <option value="A">A</option>
      <option value="B">B</option>
      <option value="B15">B15</option>
      <option value="C">C</option>
    </select>

    <label for="descripcion">Descripción:</label>
    <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

    <label for="imagen">Selecciona imagen:</label>
    <input type="file" id="imagen" name="imagen" accept="image/*" required />

    <button type="submit">Agregar Película</button>
  </form>
</section>

      <!-- Eliminar peli-->
      <section id="eliminar" class="tab-content admin-lista">
        <h2>Eliminar Película</h2>
        <?php if (count($peliculas_eliminar) > 0): ?>
          <?php foreach ($peliculas_eliminar as $pelicula): ?>
            <div class="pelicula-admin">
              <img src="img/<?= htmlspecialchars($pelicula['imagen']) ?>" alt="<?= htmlspecialchars($pelicula['titulo']) ?>" />
              <div>
                <h3><?= htmlspecialchars($pelicula['titulo']) ?></h3>
                <p>Clasificación: <?= htmlspecialchars($pelicula['clasificacion']) ?></p>
              </div>
              <button onclick="eliminarPelicula(<?= $pelicula['id'] ?>)">Eliminar</button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No hay películas para mostrar.</p>
        <?php endif; ?>
      </section>

      <!-- peliculas actuales -->
      <section id="lista" class="tab-content admin-lista">
        <h2>Películas Actuales</h2>
        <?php if ($result_peliculas_actuales && $result_peliculas_actuales->num_rows > 0): ?>
          <?php while ($pelicula = $result_peliculas_actuales->fetch_assoc()): ?>
            <div class="pelicula-admin">
              <img src="img/<?= htmlspecialchars($pelicula['imagen']) ?>" alt="<?= htmlspecialchars($pelicula['titulo']) ?>" />
              <div>
                <h3><?= htmlspecialchars($pelicula['titulo']) ?></h3>
                <p>Clasificación: <?= htmlspecialchars($pelicula['clasificacion']) ?></p>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No hay películas para mostrar.</p>
        <?php endif; ?>
      </section>

      <!-- usuarios -->
      <section id="usuarios" class="tab-content admin-lista">
        <h2>Usuarios Registrados</h2>
        <table class="tabla-admin">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result_usuarios && $result_usuarios->num_rows > 0): ?>
              <?php while ($usuario = $result_usuarios->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($usuario['id']) ?></td>
                  <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                  <td><?= htmlspecialchars($usuario['email']) ?></td>
                  <td>
                    <button class="editar" onclick="editarUsuario(<?= $usuario['id'] ?>)">Editar</button>
                    <button class="eliminar" onclick="eliminarUsuario(<?= $usuario['id'] ?>)">Eliminar</button>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="4">No hay usuarios registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <!-- ventas -->
      <section id="ventas" class="tab-content admin-lista">
        <h2>Historial de Ventas</h2>
        <table class="tabla-admin">
          <thead>
            <tr>
              <th>ID Venta</th>
              <th>Usuario</th>
              <th>Película</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result_ventas && $result_ventas->num_rows > 0): ?>
              <?php while ($venta = $result_ventas->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($venta['id']) ?></td>
                    <td><?= htmlspecialchars($venta['usuario_email']) ?></td>
                    <td><?= htmlspecialchars($venta['pelicula_titulo']) ?></td>
                    <td><?= htmlspecialchars($venta['fecha']) ?></td>
                    <td><?= htmlspecialchars($venta['boletos']) ?></td>

                  <td><button class="btn-cancelar" onclick="cancelarVenta(<?= $venta['id'] ?>)">Cancelar</button></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6">No hay ventas registradas.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </div>
  </main>

  <script>
    function mostrarTab(tabId) {
      const tabs = document.querySelectorAll('.tab');
      const contents = document.querySelectorAll('.tab-content');

      tabs.forEach(tab => tab.classList.remove('active'));
      contents.forEach(content => content.classList.remove('active'));

      const tab = document.querySelector(`.tab[data-tab="${tabId}"]`);
      if (tab) tab.classList.add('active');

      const content = document.getElementById(tabId);
      if (content) content.classList.add('active');
    }

    // Agregar Película (captura submit del form)
    document.getElementById('formAgregarPelicula').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('agregar_pelicula.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    alert(data.mensaje || data.error);
    if (data.success) location.reload();
  })
  .catch(() => alert('Error al enviar la solicitud'));
});

    // Eliminar película
    function eliminarPelicula(id) {
      if(!confirm('¿Eliminar esta película?')) return;

      fetch('eliminar_pelicula.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}`
      })
      .then(res => res.json())
      .then(res => {
        alert(res.mensaje || res.error);
        if(res.success) location.reload();
      })
      .catch(() => alert('Error al enviar la solicitud'));
    }

    // Editar usuario (puedes abrir un prompt para editar datos)
    function editarUsuario(id) {
      const nombre = prompt('Nuevo nombre:');
      if(!nombre) return alert('Nombre es obligatorio');
      const email = prompt('Nuevo email:');
      if(!email) return alert('Email es obligatorio');

      fetch('editar_usuario.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}&nombre=${encodeURIComponent(nombre)}&email=${encodeURIComponent(email)}`
      })
      .then(res => res.json())
      .then(res => {
        alert(res.mensaje || res.error);
        if(res.success) location.reload();
      })
      .catch(() => alert('Error al enviar la solicitud'));
    }

    // Eliminar usuario
    function eliminarUsuario(id) {
      if(!confirm('¿Eliminar este usuario?')) return;

      fetch('eliminar_usuario.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}`
      })
      .then(res => res.json())
      .then(res => {
        alert(res.mensaje || res.error);
        if(res.success) location.reload();
      })
      .catch(() => alert('Error al enviar la solicitud'));
    }

    // Cancelar venta
    function cancelarVenta(id) {
      if(!confirm('¿Cancelar esta venta?')) return;

      fetch('cancelar_venta.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}`
      })
      .then(res => res.json())
      .then(res => {
        alert(res.mensaje || res.error);
        if(res.success) location.reload();
      })
      .catch(() => alert('Error al enviar la solicitud'));
    }
  </script>
</body>
</html>
