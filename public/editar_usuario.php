<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

if (!isset($_POST['id'], $_POST['nombre'], $_POST['email'])) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos']);
    exit();
}

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$email = $_POST['email'];

$mysqli = new mysqli("localhost", "root", "sql123", "cine");
if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'error' => 'Error DB']);
    exit();
}

$stmt = $mysqli->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
$stmt->bind_param("ssi", $nombre, $email, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Usuario actualizado correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar usuario']);
}

$stmt->close();
$mysqli->close();
?>
