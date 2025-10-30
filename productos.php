<?php
require_once 'conexion.php';
include 'includes/header.php';

$productos = $pdo->query("
    SELECT p.*, c.nombre AS categoria, pr.nombre AS proveedor 
    FROM productos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
");

$categorias = $pdo->query("SELECT * FROM categorias");
$proveedores = $pdo->query("SELECT * FROM proveedores WHERE activo = 1");

include 'includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Productos</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalProducto">
        <i class="bi bi-plus-circle"></i> Nuevo Producto
    </button>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    Operación realizada correctamente
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show">
    Error al realizar la operación
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Proveedor</th>
            <th>Stock</th>
            <th>Precio Venta</th>
            <th class="table-actions">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while($producto = $productos->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($producto['nombre']) ?></td>
            <td><?= htmlspecialchars($producto['categoria']) ?></td>
            <td><?= htmlspecialchars($producto['proveedor']) ?></td>
            <td class="<?= $producto['stock_actual'] <= $producto['stock_minimo'] ? 'text-danger fw-bold' : '' ?>">
                <?= $producto['stock_actual'] ?> <?= $producto['unidad_medida'] ?>
            </td>
            <td>$<?= number_format($producto['precio_venta'], 2) ?></td>
            <td>
                <a href="editar_producto.php?id=<?= $producto['id'] ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i>
                </a>
                <button onclick="confirmarEliminacion(<?= $producto['id'] ?>)" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
function confirmarEliminacion(id) {
    if (confirm("¿Estás seguro de eliminar este producto?")) {
        window.location.href = `eliminar_producto.php?id=${id}`;
    }
}
</script>


<?php include 'includes/footer.php'; ?>

<div class="modal fade" id="modalProducto">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="guardar_producto.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" name="categoria_id" required>
                                <?php foreach($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Proveedor</label>
                            <select class="form-select" name="proveedor_id" required>
                                <?php foreach($proveedores as $prov): ?>
                                <option value="<?= $prov['id'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unidad de Medida</label>
                            <select class="form-select" name="unidad_medida" required>
                                <option value="kg">Kilogramos</option>
                                <option value="pieza">Pieza</option>
                                <option value="caja">Caja</option>
                                <option value="racimo">Racimo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Precio Compra</label>
                            <input type="number" step="0.01" class="form-control" name="precio_compra" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Precio Venta</label>
                            <input type="number" step="0.01" class="form-control" name="precio_venta" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Actual</label>
                            <input type="number" step="0.01" class="form-control" name="stock_actual" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Mínimo</label>
                            <input type="number" step="0.01" class="form-control" name="stock_minimo" required>
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