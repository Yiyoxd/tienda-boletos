<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinetix - Reserva</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css?v=1.1"> <!-- versión para evitar caché -->
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

    <main class="contenedor-reserva">
        <div class="asientos">
            <div class="colores_boleto">
            <span><span class="cuadro color-seleccionado"></span> Seleccionado</span>
            <span><span class="cuadro color-disponible"></span> Disponible</span>
            <span><span class="cuadro color-ocupado"></span> Ocupado</span>  
            </div>
            <div class="pantalla">Pantalla Sala 1</div>
            <?php
                $filas = range('A', 'J');
                for ($i = 0; $i < count($filas); $i++) {
                    echo "<div class='fila' data-fila='{$filas[$i]}'>";
                    for ($j = 1; $j <= 20; $j++) {
                        $clase = ($j % 5 == 0) ? 'ocupado' : 'disponible';
                        echo "<button class='$clase' data-fila='{$filas[$i]}' data-numero='$j'>$j</button>";
                    }
                    echo "</div>";
                }
            ?>
        </div>

        <div class="resumen">
            <img src="img/emma.jpg" alt="Emma">
            <h3>Emma.</h3>
            <p><strong>Horario:</strong> 18:00</p>
            <p><strong>Sala:</strong> 1</p>
            <p><strong>Precio:</strong> $55.00 x <span id="cantidad">0</span></p>
            <p class="total">Total: $<span id="total">0.00</span></p>
            <button>Reservar</button>
        </div>
    </main>

    <footer class="footer">
        <p>Todos los derechos reservados. Cinetix 2025</p>
    </footer>

    <script>
        const precio = 55;
        const cantidad = document.getElementById("cantidad");
        const total = document.getElementById("total");

        document.querySelectorAll(".asientos button.disponible, .asientos button.seleccionado").forEach(btn => {
            btn.addEventListener("click", () => {
                if (btn.classList.contains("ocupado")) return;

                btn.classList.toggle("seleccionado");
                btn.classList.toggle("disponible");

                const seleccionados = document.querySelectorAll(".asientos button.seleccionado").length;
                cantidad.textContent = seleccionados;
                total.textContent = (seleccionados * precio).toFixed(2);
            });
        });
    </script>
</body>
</html>
