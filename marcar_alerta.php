<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("UPDATE alertas_stock SET atendida = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: alertas.php");
    exit();
}