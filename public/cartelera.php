<?php
$conexion = new mysqli("localhost", "root", "", "cine");
$conexion->set_charset("utf8");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cinetix</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link href="https://fonts.googleapis.com/css2?family=Krub:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="nav-bg">
    <nav class="navegacion-principal contenedor">
        <div class="nav-izquierda">
            <div class="logo"><a href="index.php">C I N E T I X</a></div>
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
    <section class="estrenos" id="cartelera"></section>
</main>

<footer class="footer">
    <p>Todos los derechos reservados. Cinetix 2025</p>
</footer>

<script>
    async function cargarCartelera() {
        const contenedor = document.getElementById("cartelera");

        try {
            const pelisRes = await fetch("api_peliculas.php?accion=listar");
            const pelisData = await pelisRes.json();

            if (!pelisData.success || !pelisData.data) {
                contenedor.innerHTML = "<p style='color:red;'>No se pudo cargar la cartelera.</p>";
                return;
            }

            for (const peli of pelisData.data) {
                const funcionesRes = await fetch("api_funciones.php?accion=por_pelicula", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ pelicula_id: peli.id })
                });
                const funcionesData = await funcionesRes.json();
                const funciones = funcionesData.data ?? [];

                let horariosHTML = "<div class='horarios'>";
                funciones.forEach((f, idx) => {
                    const horaSinSegundos = f.hora.slice(0, 5);
                    horariosHTML += `<button onclick="window.location.href='procesar_reserva.php?funcion_id=${f.id}'">${horaSinSegundos}</button>`;
                    if ((idx + 1) % 4 === 0) horariosHTML += "</div><div class='horarios'>";
                });
                horariosHTML += "</div>";

                const imagen = peli.imagen ? `img/${peli.imagen}` : "img/image.png";

                const peliHTML = `
                  <div class="pelicula-flex">
                    <div class="pelicula-imagen">
                      <img src="${imagen}" alt="${peli.titulo}">
                    </div>
                    <div class="pelicula-info">
                      <h3>${peli.titulo}</h3>
                      <p>Clasificación: ${peli.clasificacion}</p>
                      <p>${peli.descripcion}</p>
                      ${horariosHTML}
                    </div>
                  </div>
                `;

                contenedor.innerHTML += peliHTML;
            }
        } catch (err) {
            console.error(err);
            contenedor.innerHTML = "<p style='color:red;'>Error al cargar la cartelera.</p>";
        }
    }

    cargarCartelera();
</script>
<script src="js/nav-session.js" defer></script>
</body>
</html>
