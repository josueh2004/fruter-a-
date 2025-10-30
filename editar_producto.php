<?php
require_once 'conexion.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch();

    if (!$producto) {
        header("Location: productos.php?error=1");
        exit();
    }

    $categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();
    $proveedores = $pdo->query("SELECT * FROM proveedores WHERE activo = 1")->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id' => $_POST['id'],
        'nombre' => $_POST['nombre'],
        'categoria_id' => $_POST['categoria_id'],
        'proveedor_id' => $_POST['proveedor_id'],
        'precio_compra' => $_POST['precio_compra'],
        'precio_venta' => $_POST['precio_venta'],
        'stock_actual' => $_POST['stock_actual'],
        'stock_minimo' => $_POST['stock_minimo'],
        'unidad_medida' => $_POST['unidad_medida'],
        'activo' => $_POST['activo']
    ];

    $stmt = $pdo->prepare("UPDATE productos SET 
        nombre = :nombre,
        categoria_id = :categoria_id,
        proveedor_id = :proveedor_id,
        precio_compra = :precio_compra,
        precio_venta = :precio_venta,
        stock_actual = :stock_actual,
        stock_minimo = :stock_minimo,
        unidad_medida = :unidad_medida,
        activo = :activo
        WHERE id = :id
    ");

    if ($stmt->execute($datos)) {
        // --- ALERTA DE STOCK BAJO ---
        if ($datos['stock_actual'] <= $datos['stock_minimo']) {
            // Verifica si ya existe una alerta pendiente para este producto
            $stmtAlerta = $pdo->prepare("SELECT id FROM alertas_stock WHERE producto_id = ? AND atendida = 0");
            $stmtAlerta->execute([$datos['id']]);
            if (!$stmtAlerta->fetch()) {
                $mensaje = "¡Alerta! Stock bajo de {$datos['nombre']}: {$datos['stock_actual']} {$datos['unidad_medida']} (Mínimo requerido: {$datos['stock_minimo']} {$datos['unidad_medida']})";
                $stmtInsert = $pdo->prepare("INSERT INTO alertas_stock (producto_id, mensaje, cantidad_actual, atendida) VALUES (?, ?, ?, 0)");
                $stmtInsert->execute([$datos['id'], $mensaje, $datos['stock_actual']]);
            }
        }
        // --- FIN ALERTA DE STOCK BAJO ---

        header("Location: productos.php?success=1");
    } else {
        header("Location: productos.php?error=1");
    }
    exit();
}

include 'includes/sidebar.php';
?>

<div class="container mt-4">
    <h2>Editar Producto</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $producto['id'] ?>">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" required value="<?= htmlspecialchars($producto['nombre']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Categoría</label>
                <select class="form-select" name="categoria_id" required>
                    <?php foreach($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $producto['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Proveedor</label>
                <select class="form-select" name="proveedor_id" required>
                    <?php foreach($proveedores as $prov): ?>
                        <option value="<?= $prov['id'] ?>" <?= $producto['proveedor_id'] == $prov['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prov['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Unidad de Medida</label>
                <select class="form-select" name="unidad_medida" required>
                    <option value="kg" <?= $producto['unidad_medida'] == 'kg' ? 'selected' : '' ?>>Kilogramos</option>
                    <option value="pieza" <?= $producto['unidad_medida'] == 'pieza' ? 'selected' : '' ?>>Pieza</option>
                    <option value="caja" <?= $producto['unidad_medida'] == 'caja' ? 'selected' : '' ?>>Caja</option>
                    <option value="racimo" <?= $producto['unidad_medida'] == 'racimo' ? 'selected' : '' ?>>Racimo</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Precio Compra</label>
                <input type="number" step="0.01" class="form-control" name="precio_compra" required value="<?= $producto['precio_compra'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Precio Venta</label>
                <input type="number" step="0.01" class="form-control" name="precio_venta" required value="<?= $producto['precio_venta'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Stock Actual</label>
                <input type="number" step="0.01" class="form-control" name="stock_actual" required value="<?= $producto['stock_actual'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Stock Mínimo</label>
                <input type="number" step="0.01" class="form-control" name="stock_minimo" required value="<?= $producto['stock_minimo'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Activo</label>
                <select class="form-select" name="activo">
                    <option value="1" <?= $producto['activo'] ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= !$producto['activo'] ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="productos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>