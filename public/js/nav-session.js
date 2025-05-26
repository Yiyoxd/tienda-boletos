(async () => {
    try {
        const res = await fetch("api_usuarios.php?accion=info", {
            credentials: "include"
        });
        const j = await res.json();

        if (!j.success || !j.data || !j.data.nombre) return;

        const primerNombre = j.data.nombre.split(" ")[0];

        const nav = document.querySelector(".nav-derecha");
        if (!nav) return;

        nav.innerHTML = `
      <span class="saludo" style="color: white; font-weight: bold;">
        Hola, ${primerNombre}
      </span>
      <a href="#" id="btnSalir">Salir</a>
    `;

        document.getElementById("btnSalir").addEventListener("click", async (e) => {
            e.preventDefault();
            await fetch("api_usuarios.php?accion=logout", {
                credentials: "include"
            });
            location.reload();
        });

    } catch (err) {
        console.error("Error al detectar sesión:", err);
    }
})();