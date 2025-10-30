<?php
require_once 'conexion.php';
include 'includes/header.php';


$estadisticas = [
    'productos' => $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
    'alertas' => $pdo->query("SELECT COUNT(*) FROM alertas_stock WHERE atendida = 0")->fetchColumn(),
    'proveedores' => $pdo->query("SELECT COUNT(*) FROM proveedores WHERE activo = 1")->fetchColumn(),
    'movimientos' => $pdo->query("SELECT COUNT(*) FROM movimientos")->fetchColumn()
];

include 'includes/sidebar.php';
?>

            <h2 class="mb-4">Panel Principal</h2>
            
            <div class="row g-4 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card text-bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Productos</h5>
                            <p class="card-text display-4"><?= $estadisticas['productos'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-6 col-md-3">
                    <div class="card text-bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Alertas Activas</h5>
                            <p class="card-text display-4"><?= $estadisticas['alertas'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-6 col-md-3">
                    <div class="card text-bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Proveedores</h5>
                            <p class="card-text display-4"><?= $estadisticas['proveedores'] ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-6 col-md-3">
                    <div class="card text-bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Movimientos</h5>
                            <p class="card-text display-4"><?= $estadisticas['movimientos'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>