<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Probar API Películas</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      padding: 2rem;
    }
    .formulario {
      background-color: white;
      border-radius: 8px;
      padding: 2rem;
      max-width: 500px;
      margin: 0 auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .formulario h2 {
      margin-top: 0;
      text-align: center;
    }
    .formulario input,
    .formulario textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 1rem;
    }
    .botones {
      display: flex;
      justify-content: space-between;
    }
    .botones button {
      width: 32%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      color: white;
      font-weight: bold;
      font-size: 1rem;
      cursor: pointer;
    }
    .registrar { background-color: #28a745; }
    .obtener { background-color: #007bff; }
    .listar { background-color: #6c757d; }
    .mensaje {
      margin-top: 1rem;
      padding: 10px;
      border-radius: 5px;
      display: none;
      font-size: 0.95rem;
    }
    .mensaje.success {
      background-color: #d4edda;
      color: #155724;
    }
    .mensaje.error {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body>

  <div class="formulario">
    <h2>Probar API de Películas</h2>
    <input type="text" id="titulo" placeholder="Título">
    <textarea id="descripcion" placeholder="Descripción"></textarea>
    <input type="number" id="duracion" placeholder="Duración (min)">
    <input type="text" id="clasificacion" placeholder="Clasificación (A, B, etc)">
    <input type="text" id="imagen" placeholder="Ruta de imagen (ej. img/xyz.jpg)">
    <input type="text" id="estado" placeholder="Estado (activa/inactiva)">
    <input type="number" id="buscarId" placeholder="ID para obtener">
    
    <div class="botones">
      <button class="registrar" onclick="registrar()">Registrar</button>
      <button class="obtener" onclick="obtener()">Obtener</button>
      <button class="listar" onclick="listar()">Listar</button>
    </div>

    <div id="mensaje" class="mensaje"></div>
  </div>

  <script>
    const mensajeDiv = document.getElementById("mensaje");

    function mostrarMensaje(texto, tipo = "success") {
      mensajeDiv.textContent = (typeof texto === "string") ? texto : JSON.stringify(texto, null, 2);
      mensajeDiv.className = "mensaje " + (tipo === "success" ? "success" : "error");
      mensajeDiv.style.display = "block";
    }

    async function registrar() {
      const payload = {
        titulo: document.getElementById("titulo").value,
        descripcion: document.getElementById("descripcion").value,
        duracion: parseInt(document.getElementById("duracion").value),
        clasificacion: document.getElementById("clasificacion").value,
        imagen: document.getElementById("imagen").value,
        estado: document.getElementById("estado").value || "activa"
      };

      try {
        const res = await fetch("api_peliculas.php?accion=registrar", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        mostrarMensaje(data.message, data.success ? "success" : "error");
      } catch (err) {
        mostrarMensaje("Error de red: " + err.message, "error");
      }
    }

    async function obtener() {
      const id = parseInt(document.getElementById("buscarId").value);
      if (!id) return mostrarMensaje("Ingresa un ID válido", "error");

      try {
        const res = await fetch("api_peliculas.php?accion=obtener", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id })
        });
        const data = await res.json();
        mostrarMensaje(data.data || data.message, data.success ? "success" : "error");
      } catch (err) {
        mostrarMensaje("Error de red: " + err.message, "error");
      }
    }

    async function listar() {
      try {
        const res = await fetch("api_peliculas.php?accion=listar");
        const data = await res.json();
        mostrarMensaje(data.data || data.message, data.success ? "success" : "error");
      } catch (err) {
        mostrarMensaje("Error de red: " + err.message, "error");
      }
    }
  </script>

</body>
</html>
