<?php
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
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Krub:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
</head>
<body>
    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">
            <div class="nav-izquierda">
                <div class="logo"><a href="index.html">C I N E T I X</a></div>
                <a href="index.php">Inicio</a>
                <a href="index.php#quienes">Quiénes somos</a>
                <a href="#">Catálogo</a>
            </div>
            <div class="nav-derecha">
                <a href="index.php#contacto">Contacto</a>
                <a href="login.html">Login</a>
                <a href="registro.html" class="boton-registro">Registro</a>
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
                        <div class="horarios" id="horarios-<?= $peli['id'] ?>">
                            <p>Cargando horarios...</p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </section>
    </main>

    <footer class="footer">
        <p>Todos los derechos reservados. Cinetix 2025</p>
    </footer>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".pelicula-flex").forEach(pelicula => {
            const peliculaId = pelicula.querySelector(".horarios").id.split("-")[1];
            const horariosDiv = document.getElementById("horarios-" + peliculaId);

            fetch("api_funciones.php?accion=por_pelicula", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ pelicula_id: parseInt(peliculaId) })
            })
            .then(res => res.json())
            .then(data => {
                horariosDiv.innerHTML = "";
                if (data.success && Array.isArray(data.data)) {
                    data.data.forEach(funcion => {
                        const btn = document.createElement("button");
                        btn.textContent = funcion.hora.slice(0, 5); // solo HH:MM
                        btn.onclick = () => {
                            window.location.href = 'procesar_reserva.php?funcion_id=${funcion.id}';
                        };
                        horariosDiv.appendChild(btn);
                    });
                } else {
                    horariosDiv.innerHTML = "<p>Sin horarios disponibles</p>";
                }
            })
            .catch(err => {
                console.error("Error al cargar funciones:", err);
                horariosDiv.innerHTML = "<p>Error al cargar horarios</p>";
            });
        });
    });
    </script>
</body>
</html>
