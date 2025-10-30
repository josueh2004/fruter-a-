<?php

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header('Location: login.php');
    exit();
}

require_once 'conexion.php';


$stmt = $pdo->query("
    SELECT u.*, 
           CASE WHEN a.usuario_id IS NOT NULL THEN 'Administrador' ELSE 'Cliente' END as tipo_usuario
    FROM usuarios u 
    LEFT JOIN administradores a ON u.id = a.usuario_id 
    ORDER BY u.fecha_registro DESC
");
$usuarios = $stmt->fetchAll();


$clientes_normales = 0;
$administradores = 0;
foreach($usuarios as $usuario) {
    if($usuario['tipo_usuario'] === 'Administrador') {
        $administradores++;
    } else {
        $clientes_normales++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        .admin-nav {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .password-field {
            font-family: monospace;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .stats-card {
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h2><i class="bi bi-shield-fill-check"></i> Panel de Administrador</h2>
                    <p class="mb-0">Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></p>
                </div>
                <div class="col-auto">
                    <a href="cerrar_sesion.php" class="btn btn-light">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
      
        <div class="admin-nav">
            <div class="row">
                <div class="col">
                    <a href="admin_perfil.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-person-badge"></i> Mi Perfil
                    </a>
                    <a href="admin_clientes_simple.php" class="btn btn-primary me-2">
                        <i class="bi bi-people-fill"></i> Clientes
                    </a>
                </div>
            </div>
        </div>

      
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stats-card text-bg-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-4"></i>
                        <h3><?= count($usuarios) ?></h3>
                        <p class="mb-0">Total de Usuarios</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-bg-success">
                    <div class="card-body text-center">
                        <i class="bi bi-person display-4"></i>
                        <h3><?= $clientes_normales ?></h3>
                        <p class="mb-0">Clientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-bg-danger">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-check display-4"></i>
                        <h3><?= $administradores ?></h3>
                        <p class="mb-0">Administradores</p>
                    </div>
                </div>
            </div>
        </div>

       
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Lista de Clientes Registrados</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Usuario</th>
                                <th>Contraseña</th>
                                <th>Correo</th>
                                <th>Celular</th>
                                <th>Tipo</th>
                                <th>Fecha Registro</th>
                                <th>Último Login</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($usuarios as $usuario): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary"><?= $usuario['id'] ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($usuario['nombre_completo']) ?></strong>
                                </td>
                                <td>
                                    <code><?= htmlspecialchars($usuario['usuario']) ?></code>
                                </td>
                                <td>
                                    <span class="password-field">
                                        <?= htmlspecialchars($usuario['contraseña']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($usuario['correo']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($usuario['correo']) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:<?= htmlspecialchars($usuario['celular']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($usuario['celular']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if($usuario['tipo_usuario'] === 'Administrador'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Cliente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if($usuario['ultimo_login']): ?>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($usuario['ultimo_login'])) ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-warning">Nunca</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    
    <footer class="mt-5 py-4 bg-light">
        <div class="container text-center">
            <small class="text-muted">
                Panel de Administración - <?= htmlspecialchars($_SESSION['usuario']) ?> | 
                <a href="cerrar_sesion.php" class="text-danger">Cerrar Sesión</a>
            </small>
        </div>
    </footer>
</body>
</html>