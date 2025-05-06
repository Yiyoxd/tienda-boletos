<?php
// Conexión a la base de datos (si después quieres cargar estrenos dinámicos, ya está listo)
$conexion = new mysqli("localhost", "root", "", "cine");
$conexion->set_charset("utf8");

// Ejemplo: podrías hacer esto después
// $estrenos = $conexion->query("SELECT * FROM peliculas WHERE estreno = 1");
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

    <header>
        <h1 class="titulo">Cinetix <span>Tu cine favorito</span></h1>
    </header>

    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">
          <div class="nav-izquierda">
            <div class="logo">
                <a href="index.php">C I N E T I X</a></div>
            <a href="index.php">Inicio</a>
            <a href="#quienes">Quiénes somos</a>
            <a href="cartelera.php">Catálogo</a>
          </div>
          <div class="nav-derecha">
            <a href="#contacto">Contacto</a>
            <a href="login.html">Login</a>
            <a href="registro.html" class="boton-registro">Registro</a>
          </div>
        </nav>
    </div>

    <section class="hero">
        <div class="contenido-hero contenedor-carousel">
            <button class="atras">❮</button>
            <img class="imagen-hero" src="img/banner/todoEn.jpg" alt="Frase">
            <div class="texto-hero">
                <h2 class="frase">"En otra vida, me habría gustado lavar la ropa y hacer los impuestos contigo."</h2>
                <p class="autor">- Everything Everywhere All at Once (2022)</p>
                <a class="boton" href="cartelera.php">Consultar Cartelera</a>
            </div>
            <button class="adelante">❯</button>
        </div>
    </section>

    <main class="contenedor sombra">
        <section class="estrenos">
            <h1>Próximos Estrenos</h1>
            <div class="peliculas-grid">
                <div class="pelicula">
                    <img src="img/estreno1.jpg" alt="Memorias de un caracol">
                    <h3>Memorias de un caracol</h3>
                    <p>Estreno: 15 de mayo</p>
                </div>
                <div class="pelicula">
                    <img src="img/estreno2.jpg" alt="Mickey 17">
                    <h3>Mickey 17</h3>
                    <p>Estreno: 22 de mayo</p>
                </div>
            </div>
        </section>

        <section id="quienes" class="quienes-somos">
            <div class="contenido-quienes">
                <div class="texto-quienes">
                    <h2>¿Quiénes somos?</h2>
                    <p>
                        El cine nació a finales del siglo XIX como una forma de capturar y proyectar movimiento. Desde las primeras funciones de los hermanos Lumière en 1895, ha evolucionado hasta convertirse en una de las formas de arte más influyentes en el mundo.
                    </p>
                    <p>
                        Cinetix es el primer cine fundado en Francisco I. Madero, Coahuila, con la misión de acercar la magia del séptimo arte a nuestra comunidad. Nos enorgullece ser un espacio que rinde homenaje a la historia del cine, al mismo tiempo que impulsa nuevas experiencias para todos nuestros visitantes. En Cinetix, traemos lo mejor del cine... a ti.
                    </p>
                </div>
                <div class="imagen-quienes">
                    <img src="img/image.png" alt="Historia del cine">
                </div>
            </div>
        </section>
    </main>

    <section id="contacto" class="contacto-footer">
        <div class="contenedor-contacto">
            <div class="info-contacto">
                <h2 class="titulo-contacto">Contáctanos</h2>
                <p class="telefono-contacto">📞 55 21 22 60 60</p>
            </div>

            <form class="formulario-contacto">
                <div class="campo-footer">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" placeholder="Tu nombre" />
                </div>

                <div class="campo-footer">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" placeholder="tucorreo@ejemplo.com" />
                </div>

                <div class="campo-footer campo-textarea">
                    <label for="mensaje">¿Tienes una queja o sugerencia?</label>
                    <textarea id="mensaje" rows="4" placeholder="Escribe tu mensaje aquí..."></textarea>
                </div>

                <button type="submit" class="boton-footer">Enviar</button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <p>Todos los derechos reservados. Cinetix 2025</p>
    </footer>
    <script src="js/carrusel.js"></script>
</body>
</html>
