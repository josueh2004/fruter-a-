<?php
require_once 'conexion.php';
include 'includes/header.php';

$alertas = $pdo->query("
    SELECT a.*, p.nombre AS producto 
    FROM alertas_stock a
    JOIN productos p ON a.producto_id = p.id
    WHERE a.atendida = 0
");

include 'includes/sidebar.php';
?>

            <h2 class="mb-4">Alertas de Stock</h2>
            
            <div class="row g-3">
                <?php while($alerta = $alertas->fetch()): ?>
                <div class="col-md-6">
                    <div class="alert alert-warning d-flex justify-content-between align-items-center">
                        <div>
                            <h5><?= $alerta['producto'] ?></h5>
                            <p class="mb-0"><?= $alerta['mensaje'] ?></p>
                            <small><?= $alerta['fecha'] ?></small>
                        </div>
                        <a href="marcar_alerta.php?id=<?= $alerta['id'] ?>" class="btn btn-sm btn-outline-dark">
                            Marcar como atendida
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>