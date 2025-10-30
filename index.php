<?php
require_once 'conexion.php';
include 'includes/header.php';

// Estadísticas
$estadisticas = [
    'productos' => $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
    'alertas' => $pdo->query("SELECT COUNT(*) FROM alertas_stock WHERE atendida = 0")->fetchColumn(),
    'proveedores' => $pdo->query("SELECT COUNT(*) FROM proveedores WHERE activo = 1")->fetchColumn(),
    'movimientos' => $pdo->query("SELECT COUNT(*) FROM movimientos")->fetchColumn()
];

// Productos con stock bajo (top 5)
$productos_bajo_stock = $pdo->query("
    SELECT nombre, stock_actual, stock_minimo, unidad_medida 
    FROM productos 
    WHERE stock_actual <= stock_minimo 
    ORDER BY stock_actual ASC 
    LIMIT 5
")->fetchAll();

// Últimos movimientos
$ultimos_movimientos = $pdo->query("
    SELECT m.*, p.nombre as producto 
    FROM movimientos m
    JOIN productos p ON m.producto_id = p.id
    ORDER BY m.fecha DESC
    LIMIT 5
")->fetchAll();

include 'includes/sidebar.php';
?>

<style>
/* ===================================
   SISTEMA FRUTERÍA - DASHBOARD
   =================================== */

.dashboard-container {
    padding: 30px;
    background: #f5f7fa;
    min-height: 100vh;
}

/* ==================== HEADER ==================== */
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.header-text h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-text .subtitle {
    font-size: 1.1rem;
    opacity: 0.95;
    margin: 0;
}

.header-date {
    background: rgba(255, 255, 255, 0.2);
    padding: 12px 20px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
}

/* ==================== STATS CARDS ==================== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.stat-card {
    position: relative;
    height: 200px;
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    text-decoration: none;
    color: white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.stat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
}

/* Imagen de fondo de las cards */
.card-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.card-background img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.stat-card:hover .card-background img {
    transform: scale(1.1);
}

/* Overlay oscuro */
.card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4));
    z-index: 2;
}

/* Contenido de las cards */
.card-content {
    position: relative;
    z-index: 3;
    padding: 25px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card-icon {
    font-size: 3rem;
    opacity: 0.9;
}

.card-info {
    display: flex;
    flex-direction: column;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 8px;
    opacity: 0.95;
}

.card-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 5px 0;
    line-height: 1;
}

.card-subtitle {
    font-size: 0.9rem;
    opacity: 0.85;
}

/* Efecto hover en las cards */
.card-hover-effect {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 15px 25px;
    background: rgba(255, 255, 255, 0.95);
    color: #333;
    font-weight: 600;
    transform: translateY(100%);
    transition: transform 0.3s ease;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stat-card:hover .card-hover-effect {
    transform: translateY(0);
}

/* Colores específicos por tipo de card */
.card-productos .card-overlay {
    background: linear-gradient(135deg, rgba(33, 150, 243, 0.7), rgba(21, 101, 192, 0.7));
}

.card-alertas .card-overlay {
    background: linear-gradient(135deg, rgba(255, 152, 0, 0.7), rgba(245, 124, 0, 0.7));
}

.card-proveedores .card-overlay {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.7), rgba(56, 142, 60, 0.7));
}

.card-movimientos .card-overlay {
    background: linear-gradient(135deg, rgba(0, 188, 212, 0.7), rgba(0, 151, 167, 0.7));
}

/* ==================== RESUMEN ==================== */
.dashboard-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.summary-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.summary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.summary-header h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.badge-alert {
    background: #ff9800;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.summary-content {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

/* Items de Stock Bajo */
.stock-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
    transition: background 0.3s ease;
}

.stock-item:hover {
    background: #e9ecef;
}

.stock-icon {
    font-size: 2rem;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.stock-info {
    flex: 1;
}

.stock-name {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.stock-progress {
    display: flex;
    align-items: center;
    gap: 10px;
}

.progress-bar {
    flex: 1;
    height: 8px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #ff5252, #ff9800);
    border-radius: 10px;
    transition: width 0.3s ease;
}

.stock-text {
    font-size: 0.85rem;
    color: #666;
    white-space: nowrap;
}

/* Items de Movimientos */
.movement-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
    transition: background 0.3s ease;
}

.movement-item:hover {
    background: #e9ecef;
}

.movement-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.5rem;
}

.icon-entrada {
    background: #e8f5e9;
    color: #4caf50;
}

.icon-salida {
    background: #ffebee;
    color: #f44336;
}

.movement-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.movement-product {
    font-weight: 600;
    color: #333;
}

.movement-details {
    font-size: 0.85rem;
    color: #666;
    display: flex;
    align-items: center;
    gap: 8px;
}

.movement-type {
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.type-entrada {
    background: #e8f5e9;
    color: #4caf50;
}

.type-salida {
    background: #ffebee;
    color: #f44336;
}

/* Estado vacío */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 0.95rem;
}

/* ==================== ACCIONES RÁPIDAS ==================== */
.quick-actions {
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px;
    background: white;
    border-radius: 15px;
    text-decoration: none;
    color: #333;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.action-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.action-btn i {
    font-size: 1.8rem;
}

.btn-add-product {
    background: linear-gradient(135deg, #2196f3, #1976d2);
    color: white;
}

.btn-add-supplier {
    background: linear-gradient(135deg, #4caf50, #388e3c);
    color: white;
}

.btn-view-alerts {
    background: linear-gradient(135deg, #ff9800, #f57c00);
    color: white;
}

.btn-inventory {
    background: linear-gradient(135deg, #00bcd4, #0097a7);
    color: white;
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 992px) {
    .dashboard-summary {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 20px;
    }

    .dashboard-header {
        padding: 20px;
    }

    .header-text h1 {
        font-size: 1.5rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .actions-grid {
        grid-template-columns: 1fr;
    }

    .stock-item,
    .movement-item {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<div class="dashboard-container">
    <!-- Header del Dashboard -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="header-text">
                <h1>
                    <i class="bi bi-speedometer2"></i>
                    Panel Principal
                </h1>
                <p class="subtitle">Bienvenido de vuelta, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?> 👋</p>
            </div>
            <div class="header-date">
                <i class="bi bi-calendar3"></i>
                <span><?= date('d/m/Y') ?></span>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="stats-grid">
        <!-- Card Productos -->
        <a href="productos.php" class="stat-card card-productos">
            <div class="card-background">
                <img src="https://images.unsplash.com/photo-1619566636858-adf3ef46400b?w=400&q=80" alt="Productos">
            </div>
            <div class="card-overlay"></div>
            <div class="card-content">
                <div class="card-icon">
                    <i class="bi bi-apple"></i>
                </div>
                <div class="card-info">
                    <h3 class="card-title">Productos</h3>
                    <p class="card-number"><?= $estadisticas['productos'] ?></p>
                    <span class="card-subtitle">Total registrados</span>
                </div>
            </div>
            <div class="card-hover-effect">
                <span>Ver productos <i class="bi bi-arrow-right"></i></span>
            </div>
        </a>

        <!-- Card Alertas -->
        <a href="alertas.php" class="stat-card card-alertas">
            <div class="card-background">
                <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400&q=80" alt="Alertas">
            </div>
            <div class="card-overlay"></div>
            <div class="card-content">
                <div class="card-icon">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <div class="card-info">
                    <h3 class="card-title">Alertas Activas</h3>
                    <p class="card-number"><?= $estadisticas['alertas'] ?></p>
                    <span class="card-subtitle">Requieren atención</span>
                </div>
            </div>
            <div class="card-hover-effect">
                <span>Ver alertas <i class="bi bi-arrow-right"></i></span>
            </div>
        </a>

        <!-- Card Proveedores -->
        <a href="proveedores.php" class="stat-card card-proveedores">
            <div class="card-background">
                <img src="https://images.unsplash.com/photo-1566576721346-d4a3b4eaeb55?w=400&q=80" alt="Proveedores">
            </div>
            <div class="card-overlay"></div>
            <div class="card-content">
                <div class="card-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="card-info">
                    <h3 class="card-title">Proveedores</h3>
                    <p class="card-number"><?= $estadisticas['proveedores'] ?></p>
                    <span class="card-subtitle">Activos</span>
                </div>
            </div>
            <div class="card-hover-effect">
                <span>Ver proveedores <i class="bi bi-arrow-right"></i></span>
            </div>
        </a>

        <!-- Card Movimientos -->
        <div class="stat-card card-movimientos">
            <div class="card-background">
                <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&q=80" alt="Movimientos">
            </div>
            <div class="card-overlay"></div>
            <div class="card-content">
                <div class="card-icon">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div class="card-info">
                    <h3 class="card-title">Movimientos</h3>
                    <p class="card-number"><?= $estadisticas['movimientos'] ?></p>
                    <span class="card-subtitle">Total registrados</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Resumen -->
    <div class="dashboard-summary">
        <!-- Productos con Stock Bajo -->
        <div class="summary-card">
            <div class="summary-header">
                <h3>
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Stock Bajo
                </h3>
                <span class="badge-alert"><?= count($productos_bajo_stock) ?></span>
            </div>
            <div class="summary-content">
                <?php if(count($productos_bajo_stock) > 0): ?>
                    <?php foreach($productos_bajo_stock as $producto): ?>
                    <div class="stock-item">
                        <div class="stock-icon">
                            🍎
                        </div>
                        <div class="stock-info">
                            <span class="stock-name"><?= htmlspecialchars($producto['nombre']) ?></span>
                            <div class="stock-progress">
                                <?php 
                                $porcentaje = ($producto['stock_actual'] / $producto['stock_minimo']) * 100;
                                $porcentaje = min($porcentaje, 100);
                                ?>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $porcentaje ?>%"></div>
                                </div>
                                <span class="stock-text">
                                    <?= $producto['stock_actual'] ?> / <?= $producto['stock_minimo'] ?> 
                                    <?= $producto['unidad_medida'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>Todo el inventario está en niveles óptimos</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Últimos Movimientos -->
        <div class="summary-card">
            <div class="summary-header">
                <h3>
                    <i class="bi bi-clock-history"></i>
                    Últimos Movimientos
                </h3>
            </div>
            <div class="summary-content">
                <?php if(count($ultimos_movimientos) > 0): ?>
                    <?php foreach($ultimos_movimientos as $mov): ?>
                    <div class="movement-item">
                        <div class="movement-icon <?= $mov['tipo'] === 'entrada' ? 'icon-entrada' : 'icon-salida' ?>">
                            <i class="bi bi-<?= $mov['tipo'] === 'entrada' ? 'arrow-down-circle' : 'arrow-up-circle' ?>"></i>
                        </div>
                        <div class="movement-info">
                            <span class="movement-product"><?= htmlspecialchars($mov['producto']) ?></span>
                            <span class="movement-details">
                                <span class="movement-type <?= $mov['tipo'] === 'entrada' ? 'type-entrada' : 'type-salida' ?>">
                                    <?= ucfirst($mov['tipo']) ?>
                                </span>
                                • <?= $mov['cantidad'] ?> unidades
                                • <?= date('d/m/Y', strtotime($mov['fecha'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No hay movimientos registrados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sección de Acciones Rápidas -->
    <div class="quick-actions">
        <h3 class="section-title">
            <i class="bi bi-lightning-fill"></i>
            Acciones Rápidas
        </h3>
        <div class="actions-grid">
            <a href="productos.php" class="action-btn btn-add-product">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Agregar Producto</span>
            </a>
            <a href="proveedores.php" class="action-btn btn-add-supplier">
                <i class="bi bi-truck"></i>
                <span>Nuevo Proveedor</span>
            </a>
            <a href="alertas.php" class="action-btn btn-view-alerts">
                <i class="bi bi-bell-fill"></i>
                <span>Ver Alertas</span>
            </a>
            <a href="productos.php" class="action-btn btn-inventory">
                <i class="bi bi-box-seam"></i>
                <span>Gestionar Inventario</span>
            </a>
        </div>
    </div>
</div>

</div>
</div>
</div>

<?php include 'includes/footer.php'; ?>
