<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Probar API Login / Registro</title>
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
      max-width: 400px;
      margin: 0 auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .formulario h2 {
      margin-top: 0;
      text-align: center;
    }
    .formulario input {
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
      width: 48%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      color: white;
      font-weight: bold;
      font-size: 1rem;
      cursor: pointer;
    }
    .botones button.login {
      background-color: #0066cc;
    }
    .botones button.registrar {
      background-color: #28a745;
    }
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
    <h2>Probar API Login / Registro</h2>
    <input type="text" id="nombre" placeholder="Nombre (solo para registrar)">
    <input type="email" id="correo" placeholder="Correo electrónico">
    <input type="password" id="password" placeholder="Contraseña">
    <div class="botones">
      <button class="login" onclick="login()">Login</button>
      <button class="registrar" onclick="registrar()">Registrar</button>
    </div>
    <div id="mensaje" class="mensaje"></div>
  </div>

  <script>
    const mensajeDiv = document.getElementById("mensaje");

    function mostrarMensaje(texto, tipo) {
      mensajeDiv.textContent = texto;
      mensajeDiv.className = "mensaje " + (tipo === "success" ? "success" : "error");
      mensajeDiv.style.display = "block";
    }

    async function login() {
        mensajeDiv.style.display = 'none';
        const correo = document.getElementById("correo").value.trim();
        const password = document.getElementById("password").value;
        
        try {
            const res = await fetch("http://localhost/tienda-boletos/public/api_usuarios.php?accion=login", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ nombre: "", correo, password })
            });
            
            const text = await res.text();
            console.log("RAW RESPONSE:", JSON.stringify(text));  // <— imprime con comillas y escapes
            const data = JSON.parse(text);                       // parse explícito
            mostrarMensaje(data.message, data.success ? "success" : "error");
        } catch (err) {
            mostrarMensaje("Error de red: " + err.message, "error");
        }
    }

    async function registrar() {
      mensajeDiv.style.display = 'none';
      const nombre = document.getElementById("nombre").value.trim();
      const correo = document.getElementById("correo").value.trim();
      const password = document.getElementById("password").value;

      try {
        const res = await fetch("http://localhost/tienda-boletos/public/api_usuarios.php?accion=registrar", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ nombre, correo, password })
        });
        const data = await res.json();
        mostrarMensaje(data.message, data.success ? "success" : "error");
      } catch (err) {
        mostrarMensaje("Error de red: " + err.message, "error");
      }
    }
  </script>

</body>
</html>
