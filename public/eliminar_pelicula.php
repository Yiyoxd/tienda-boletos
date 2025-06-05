<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID no recibido']);
    exit();
}

$mysqli = new mysqli("localhost", "root", "sql123", "cine");
if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'error' => 'Error DB']);
    exit();
}

$stmt = $mysqli->prepare("DELETE FROM peliculas WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Película eliminada correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al eliminar película']);
}

$stmt->close();
$mysqli->close();
?>
