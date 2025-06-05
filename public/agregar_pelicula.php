<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Recibir datos
$titulo = trim($_POST['titulo'] ?? '');
$clasificacion = trim($_POST['clasificacion'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if (!$titulo || !$clasificacion || !$descripcion) {
    echo json_encode(['error' => 'Todos los campos son obligatorios']);
    exit;
}

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Error al subir la imagen']);
    exit;
}

// Validar tipo de imagen permitida
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($_FILES['imagen']['type'], $allowedTypes)) {
    echo json_encode(['error' => 'Solo se permiten imágenes JPG, PNG o GIF']);
    exit;
}

// Directorio donde se guardarán las imágenes
$uploadDir = __DIR__ . '/../img/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Nombre único para la imagen
$imageName = time() . '_' . basename($_FILES['imagen']['name']);
$uploadFile = $uploadDir . $imageName;

if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadFile)) {
    echo json_encode(['error' => 'No se pudo mover el archivo subido']);
    exit;
}

// Guardar en la base de datos
$mysqli = new mysqli("localhost", "root", "sql123", "cine");
$mysqli->set_charset("utf8");

if ($mysqli->connect_errno) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO peliculas (titulo, clasificacion, descripcion, imagen) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $titulo, $clasificacion, $descripcion, $imageName);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Película agregada correctamente']);
} else {
    // Eliminar imagen si falla la inserción en BD
    unlink($uploadFile);
    echo json_encode(['error' => 'Error al agregar película a la base de datos']);
}

$stmt->close();
$mysqli->close();
?>
