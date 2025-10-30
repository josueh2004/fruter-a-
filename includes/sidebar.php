<?php
// Inicializar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="d-flex flex-column gap-2">
                <a href="index.php" class="btn btn-success">
                    <i class="bi bi-speedometer2"></i> Panel Principal
                </a>
                <a href="productos.php" class="btn btn-outline-success">
                    <i class="bi bi-apple"></i> Productos
                </a>
                <a href="alertas.php" class="btn btn-outline-success">
                    <i class="bi bi-bell"></i> Alertas de Stock
                </a>
                <a href="proveedores.php" class="btn btn-outline-success">
                    <i class="bi bi-truck"></i> Proveedores
                </a>
                
                <!-- Solo mostrar usuarios si es administrador -->
                <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true): ?>
                <a href="usuarios.php" class="btn btn-outline-success">
                    <i class="bi bi-people"></i> Usuarios
                </a>
                <a href="admin_perfil.php" class="btn btn-outline-success">
                    <i class="bi bi-person-badge"></i> Mi Perfil Admin
                </a>
                <?php endif; ?>
                
                <hr class="my-3">
                
                <!-- Información del usuario -->
                <div class="text-center mb-2">
                    <small class="text-muted">
                        <i class="bi bi-person-circle"></i><br>
                        <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?>
                        <br>
                        <span class="badge <?= isset($_SESSION['es_admin']) && $_SESSION['es_admin'] ? 'bg-danger' : 'bg-primary' ?>">
                            <?= isset($_SESSION['es_admin']) && $_SESSION['es_admin'] ? 'Administrador' : 'Usuario' ?>
                        </span>
                    </small>
                </div>
                
                <a href="cerrar_sesion.php" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </a>
            </div>
        </div>
        <div class="col-md-9 col-lg-10 main-content">