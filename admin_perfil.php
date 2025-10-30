<?php

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header('Location: login.php');
    exit();
}

require_once 'conexion.php';


$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$user = $stmt->fetch();


$total_clientes = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Mi Perfil</title>
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
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .profile-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
                    <a href="admin_perfil.php" class="btn btn-primary me-2">
                        <i class="bi bi-person-badge"></i> Mi Perfil
                    </a>
                    <a href="admin_clientes_simple.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-people-fill"></i> Clientes
                    </a>
                </div>
            </div>
        </div>

       
        <div class="row mb-4">
            <div class="col-md-4 mx-auto">
                <div class="card stat-card text-bg-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-4"></i>
                        <h3><?= $total_clientes ?></h3>
                        <p class="mb-0">Total de Clientes</p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card profile-card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-person-badge"></i> Mi Perfil de Administrador</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">ID de Usuario:</th>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($user['id']) ?></span></td>
                                </tr>
                                <tr>
                                    <th>Nombre Completo:</th>
                                    <td><strong><?= htmlspecialchars($user['nombre_completo']) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Usuario:</th>
                                    <td><code><?= htmlspecialchars($user['usuario']) ?></code></td>
                                </tr>
                                <tr>
                                    <th>Correo Electrónico:</th>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($user['correo']) ?>">
                                            <?= htmlspecialchars($user['correo']) ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Celular:</th>
                                    <td>
                                        <a href="tel:<?= htmlspecialchars($user['celular']) ?>">
                                            <?= htmlspecialchars($user['celular']) ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha de Registro:</th>
                                    <td><?= date('d/m/Y H:i:s', strtotime($user['fecha_registro'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Último Login:</th>
                                    <td>
                                        <?php if($user['ultimo_login']): ?>
                                            <?= date('d/m/Y H:i:s', strtotime($user['ultimo_login'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Nunca</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Rol:</th>
                                    <td><span class="badge bg-danger">Administrador</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="card mt-3">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Panel Administrativo</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Función:</strong></p>
                                <p class="text-muted">Gestión de Clientes</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Acceso:</strong></p>
                                <p class="text-muted">Solo Administradores</p>
                            </div>
                        </div>
                    </div>
                </div>
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