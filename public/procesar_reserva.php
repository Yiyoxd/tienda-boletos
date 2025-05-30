<?php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);
$sala_funcion_id = isset($_GET['funcion_id']) ? intval($_GET['funcion_id']) : 0;
if ($sala_funcion_id <= 0) { die('Sala-Función no especificada'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Cinetix – Reserva</title>

    <!-- Estilos existentes -->
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css?v=1.2">
    <link href="https://fonts.googleapis.com/css2?family=Krub:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

    <!-- PayPal SDK (sandbox) -->
    <script src="https://www.paypal.com/sdk/js?client-id=ATKK44gRIaDJV-r-QDh6PP-9ItUxNIxY67UbUDCDjzA3egP7UuNqxrqMghjEPUCql5EhS6zH_6h6xLwP&currency=MXN"></script>
</head>
<body>

<!-- NAV -->
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

<!-- 🟢 Banner de mensaje -->
<div id="mensaje-pago" style="display:none;"></div>

<main class="contenedor-reserva">
    <!-- —— ASIENTOS —— -->
    <div class="asientos">
        <div class="colores_boleto">
            <span><span class="cuadro color-seleccionado"></span> Seleccionado</span>
            <span><span class="cuadro color-disponible"></span> Disponible</span>
            <span><span class="cuadro color-ocupado"></span> Ocupado</span>
        </div>
        <div class="pantalla" id="pantalla-sala">Pantalla Sala</div>
    </div>

    <!-- —— RESUMEN —— -->
    <div class="resumen" id="resumen">
        <img id="img-pelicula" src="" alt="Imagen" style="display:none">
        <h3 id="titulo-pelicula">...</h3>
        <p><strong>Fecha:</strong> <span id="fecha-funcion"></span></p>
        <p><strong>Horario:</strong> <span id="hora-funcion"></span></p>
        <p><strong>Sala:</strong> <span id="sala-id"></span></p>
        <p><strong>Precio:</strong> $<span id="precio-unitario"></span> x <span id="cantidad">0</span></p>
        <p class="total">Total: $<span id="total">0.00</span></p>

        <!-- PayPal -->
        <div id="paypal-button-container" style="margin-top:1rem;"></div>
    </div>
</main>

<footer class="footer"><p>Todos los derechos reservados. Cinetix 2025</p></footer>

<script>
    const salaFuncionId = <?= $sala_funcion_id ?>;
    let precio = 0;
    let pelicula = '';
    let descripcionOrden = '';

    /* Cargar datos */
    (async () => {
        const sf = await (await fetch('api_sala_funcion.php?accion=obtener', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body:JSON.stringify({id:salaFuncionId})
        })).json();
        if (!sf.success) return alert('No se pudo cargar sala-función');
        document.getElementById('pantalla-sala').textContent = 'Pantalla Sala ' + sf.data.sala_id;
        document.getElementById('sala-id').textContent = sf.data.sala_id;

        const f = await (await fetch('api_funciones.php?accion=obtener', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body:JSON.stringify({id:sf.data.funcion_id})
        })).json();
        if (!f.success) return alert('No se pudo cargar función');
        precio = parseFloat(f.data.precio);
        document.getElementById('fecha-funcion').textContent = f.data.fecha;
        document.getElementById('hora-funcion').textContent = f.data.hora.substring(0,5);
        document.getElementById('precio-unitario').textContent = precio.toFixed(2);

        const p = await (await fetch('api_peliculas.php?accion=obtener', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body:JSON.stringify({id:f.data.pelicula_id})
        })).json();
        if (!p.success) return alert('No se pudo cargar película');
        pelicula = p.data;
        descripcionOrden = pelicula.titulo;
        document.getElementById('titulo-pelicula').textContent = pelicula.titulo;
        document.getElementById('img-pelicula').src = 'img/' + pelicula.imagen;
        document.getElementById('img-pelicula').style.display = 'block';
    })();

    /* Cargar asientos */
    (async () => {
        const j = await (await fetch('api_asientos.php?accion=listar', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body:JSON.stringify({sala_funcion_id:salaFuncionId})
        })).json();
        if (!j.success) return alert(j.message || 'No se pudieron cargar asientos');

        const mapa = {};
        j.data.forEach(a => {
            if (!mapa[a.fila]) mapa[a.fila] = {};
            mapa[a.fila][a.numero] = a.estado;
        });

        const cont = document.querySelector('.asientos');
        for (const f of 'ABCDEFGHIJ') {
            const fila = document.createElement('div');
            fila.className = 'fila';

            // 🔸 Agrega esto:
            const etiqueta = document.createElement('span');
            etiqueta.className = 'fila-label';
            etiqueta.textContent = f;
            fila.appendChild(etiqueta);

            // resto del código original sigue exactamente igual
            for (let n = 1; n <= 20; n++) {
                const est = (mapa[f]?.[n]) || 'libre';
                const btn = document.createElement('button');
                btn.textContent = n;
                btn.dataset.fila = f;
                btn.dataset.numero = n;
                btn.className = est === 'reservado' ? 'ocupado' : 'disponible';
                fila.appendChild(btn);
            }
            cont.appendChild(fila);
        }
        agregarEventos();
    })();

    function agregarEventos() {
        const cant = document.getElementById('cantidad');
        const tot = document.getElementById('total');
        document.querySelectorAll('.asientos button').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.classList.contains('ocupado')) return;
                btn.classList.toggle('seleccionado');
                btn.classList.toggle('disponible');
                const n = document.querySelectorAll('.asientos .seleccionado').length;
                cant.textContent = n;
                tot.textContent = (n * precio).toFixed(2);
            });
        });
    }

    function asientosSeleccionados() {
        return Array.from(document.querySelectorAll('.asientos .seleccionado'))
            .map(b => ({ fila: b.dataset.fila, numero: +b.dataset.numero }));
    }

    paypal.Buttons({
        style: { color: 'gold', shape: 'rect', label: 'pay' },
        createOrder: async () => {
            const sel = asientosSeleccionados();
            if (!sel.length) {
                alert('Selecciona al menos un asiento');
                throw new Error();
            }
            const total = parseFloat(document.getElementById('total').textContent);
            const r = await fetch('create_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ total, descripcion: descripcionOrden })
            });
            const j = await r.json();
            return j.id;
        },
        onApprove: async (data) => {
            const cap = await fetch(`capture_order.php?orderID=${data.orderID}`);
            const j = await cap.json();
            if (j.status !== 'COMPLETED') {
                alert('Pago no capturado');
                return;
            }
            const reserva = await fetch('api_reserva.php?accion=crear', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({
                    sala_funcion_id: salaFuncionId,
                    asientos: asientosSeleccionados()
                })
            });
            const rjson = await reserva.json();
            if (rjson.success) {
                localStorage.setItem('mensaje_pago', '¡Reserva y pago completados!');
                location.href = `procesar_reserva.php?funcion_id=${salaFuncionId}`;
            } else {
                alert('Pago realizado, pero la reserva falló: ' + (rjson.message || ''));
            }
        },
        onCancel: function () {
            alert('Pago cancelado por el usuario');
        }
    }).render('#paypal-button-container');

    /* Mostrar mensaje visual si existe */
    document.addEventListener('DOMContentLoaded', () => {
        const mensaje = localStorage.getItem('mensaje_pago');
        if (mensaje) {
            const div = document.getElementById('mensaje-pago');
            div.textContent = mensaje;
            div.style.display = 'block';
            div.style.backgroundColor = '#d4edda';
            div.style.color = '#155724';
            div.style.padding = '10px';
            div.style.borderRadius = '6px';
            div.style.margin = '1rem auto';
            div.style.textAlign = 'center';
            div.style.maxWidth = '500px';
            div.style.fontWeight = 'bold';
            div.style.boxShadow = '0 0 10px rgba(0,0,0,0.1)';
            setTimeout(() => {
                div.style.display = 'none';
                localStorage.removeItem('mensaje_pago');
            }, 5000);
        }
    });
</script>

<script src="js/nav-session.js" defer></script>
</body>
</html>
