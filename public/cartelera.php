<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "cine");
$conexion->set_charset("utf8");

$peliculas = $conexion->query("SELECT * FROM peliculas WHERE estado = 'activa'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinetix</title>
    <link rel="preload" href="css/normalize.css" as="style">
    <link rel="stylesheet" href="css/normalize.css">
    <link href="https://fonts.googleapis.com/css2?family=Krub:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="preload" href="css/style.css" as="style">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">
            <div class="nav-izquierda">
                <div class="logo"><a href="index.html">C I N E T I X</a></div>
                <a href="index.html">Inicio</a>
                <a href="index.html#quienes">Quiénes somos</a>
                <a href="#">Catálogo</a>
            </div>
            <div class="nav-derecha">
                <a href="index.html#contacto">Contacto</a>
                <a href="#">Login</a>
                <a href="#" class="boton-registro">Registro</a>
            </div>
        </nav>
    </div>

    <div class="encabezado-cartelera">
        <h1 class="titulo-cartelera">🍿Cartelera🍿</h1>
        <p class="descripcion-cartelera">Consulta los horarios disponibles y reserva tu lugar</p>
    </div>

    <main class="contenedor sombra">
        <section class="estrenos">
            <?php while($peli = $peliculas->fetch_assoc()): ?>
                <div class="pelicula-flex">
                    <div class="pelicula-imagen">
                        <img src="img/<?= htmlspecialchars($peli['imagen']) ?>" alt="<?= htmlspecialchars($peli['titulo']) ?>">
                    </div>
                    <div class="pelicula-info">
                    <h3><?= htmlspecialchars($peli['titulo']) ?></h3>
                    <p>Clasificación: <?= htmlspecialchars($peli['clasificacion']) ?></p>
                    <p>Duración: <?= htmlspecialchars($peli['duracion']) ?> minutos</p>
                    <p><?= htmlspecialchars($peli['descripcion']) ?></p>
                    <div class="horarios">
                        <button>12:00</button>
                        <button>15:00</button>
                        <button>18:00</button>
                        <button>21:00</button>
                    </div>
                </div>

                </div>
            <?php endwhile; ?>
        </section>
    </main>

    <footer class="footer">
        <p>Todos los derechos reservados. Cinetix 2025</p>
    </footer>
</body>
</html>
