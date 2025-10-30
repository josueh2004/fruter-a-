<?php
require_once 'conexion.php';
include 'includes/header.php';

$proveedores = $pdo->query("SELECT * FROM proveedores WHERE activo = 1");

include 'includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Proveedores</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalProveedor">
        <i class="bi bi-plus-circle"></i> Nuevo Proveedor
    </button>
</div>

<?php if (isset($_GET['exito'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    Operación realizada correctamente
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?php
    switch ($_GET['error']) {
        case 'campos_vacios': echo 'Todos los campos son obligatorios'; break;
        case 'bd': echo 'Error en la base de datos'; break;
        default: echo 'Error al realizar la operación';
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Nombre</th>
            <th>Contacto</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th class="acciones-tabla">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while($proveedor = $proveedores->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($proveedor['nombre']) ?></td>
            <td><?= htmlspecialchars($proveedor['contacto']) ?></td>
            <td><?= htmlspecialchars($proveedor['telefono']) ?></td>
            <td><?= htmlspecialchars($proveedor['direccion']) ?></td>
            <td>
                <a href="editar_proveedor.php?id=<?= $proveedor['id'] ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i>
                </a>
                <button onclick="confirmarEliminacion(<?= $proveedor['id'] ?>)" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
function confirmarEliminacion(id) {
    if (confirm("¿Estás seguro de eliminar este proveedor?")) {
        window.location.href = `eliminar_proveedor.php?id=${id}`;
    }
}
</script>


<div class="modal fade" id="modalProveedor">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="guardar_proveedor.php" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contacto</label>
                            <input type="text" class="form-control" name="contacto" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <textarea class="form-control" name="direccion" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>