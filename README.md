# 🎬 Proyecto Web: Sistema de Venta de Boletos para Cine

Este proyecto consiste en el desarrollo de un sistema web tipo cine, que permite a los usuarios consultar la cartelera, seleccionar funciones y asientos, reservar boletos y realizar pagos de manera sencilla e interactiva.

## 🎯 Objetivo del proyecto

El objetivo es simular el funcionamiento real de un sistema de venta de boletos en línea para un cine. El sistema debe permitir al usuario:
- Ver las películas disponibles en cartelera.
- Consultar funciones por fecha y hora.
- Visualizar los asientos disponibles por función.
- Seleccionar y reservar asientos.
- Realizar el pago del boleto.
- Confirmar la compra y obtener detalles del boleto.
- El usuario pueda tener una cuenta donde vea sus compras (registro y login)

## 🧰 Tecnologías utilizadas
- HTML5 y CSS3 – Estructura y diseño de las páginas.
- JavaScript – Funcionalidad en el navegador (selección de asientos, AJAX).
- PHP – Lógica del lado del servidor.
- MySQL – Base de datos relacional.

## 📁 Estructura del proyecto

El sistema está organizado siguiendo una arquitectura limpia, dividiendo las responsabilidades entre archivos visibles para el usuario (frontend), lógica del servidor (backend) y estructura de datos (base de datos).

<pre> ```plaintext cine-web/ │ ├── public/ # Archivos accesibles desde el navegador │ ├── index.php # Página de inicio │ ├── cartelera.php # Lista de funciones disponibles │ ├── seleccion.php # Página para elegir asientos │ ├── pago.php # Formulario para realizar el pago │ ├── confirmacion.php # Confirmación del boleto reservado │ ├── login.php # Inicio de sesión │ ├── logout.php # Cierre de sesión │ ├── registro.php # Registro de usuario │ ├── perfil.php # Perfil del usuario (historial) │ ├── procesar_pago.php # Archivo que procesa la compra │ ├── procesar_reserva.php # Maneja los asientos seleccionados │ ├── api_asientos.php # Devuelve los asientos disponibles (JSON) │ ├── api_funciones.php # Devuelve funciones disponibles (JSON) │ ├── api_peliculas.php # Devuelve info de películas (JSON) │ ├── css/ # Archivos de estilos │ ├── js/ # Scripts JS (asientos, funciones, etc.) │ └── img/ # Imágenes (carteles, logos, etc.) │ ├── app/ # Lógica del sistema │ ├── controllers/ # Controladores que gestionan peticiones y respuestas │ ├── models/ # Clases que representan entidades del sistema │ └── views/ # Fragmentos HTML reutilizables (cabecera, pie, etc.) │ ├── config/ │ └── db.php # Archivo de configuración de la base de datos │ ├── sql/ │ └── cine_schema.sql # Script SQL para crear las tablas del sistema │ ├── lib/ │ └── functions.php # Funciones auxiliares reutilizables │ └── README.md # Documento explicativo del proyecto ``` </pre>
## 👥 Roles y trabajo en equipo

El equipo de desarrollo se divide en tres roles principales para trabajar de forma paralela:

1. Frontend – Desarrollo de vistas HTML, CSS y JS.
2. Backend – Programación de la lógica en PHP y comunicación con la base de datos.
3. Base de Datos – Diseño y creación de la estructura de datos en MySQL.

Cada integrante trabaja en su propia rama usando Git (frontend/nombre, backend/nombre, db/nombre), y se integra todo en una rama común (dev) para pruebas. Solo las versiones funcionales se suben a main.

## 📦 Instrucciones para ejecutar el proyecto

1. Clona el repositorio en tu máquina:
   git clone https://github.com/Yiyoxd/tienda-boletos

2. Crea una base de datos llamada `cine` en tu servidor local y ejecuta el script sql/cine_schema.sql para crear las tablas.

3. Configura la conexión en config/db.php con tus datos de usuario y contraseña de MySQL.

4. Abre public/index.php desde un entorno local como XAMPP, WAMP o Laragon.

## 📌 Estado actual del proyecto

- ✅ Estructura base creada
- ✅ Script de base de datos listo
- ✅ División de vistas públicas
- 🔄 Lógica de compra y reservas en proceso
- 🔄 Validaciones de formularios
- 🔄 Estilización final
