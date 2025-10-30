<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'conexion.php';
    $usuario = $_POST['usuario'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';
    $tipo_usuario = $_POST['tipo_usuario'] ?? 'usuario';

    // Verificar si el usuario existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();

    if ($user && $user['contraseña'] === $contraseña) {
        // Verificar si es administrador en la tabla administradores
        $stmtAdmin = $pdo->prepare("SELECT * FROM administradores WHERE usuario_id = ?");
        $stmtAdmin->execute([$user['id']]);
        $admin = $stmtAdmin->fetch();
        $es_administrador = (bool)$admin;

        // VALIDACIÓN ESTRICTA DE ROLES
        if ($tipo_usuario === 'admin') {
            // Si selecciona administrador, DEBE ser administrador
            if ($es_administrador) {
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['nombre'] = $user['nombre_completo'];
                $_SESSION['es_admin'] = true;
                $_SESSION['usuario_id'] = $user['id'];
                
                // Actualizar último login
                $stmtUpdate = $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
                $stmtUpdate->execute([$user['id']]);
                
                header('Location: admin_perfil.php');
                exit();
            } else {
                $error = 'No tienes permisos de administrador.';
            }
        } else {
            // Si selecciona usuario normal, NO debe ser administrador
            if (!$es_administrador) {
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['nombre'] = $user['nombre_completo'];
                $_SESSION['es_admin'] = false;
                $_SESSION['usuario_id'] = $user['id'];
                
                // Actualizar último login
                $stmtUpdate = $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
                $stmtUpdate->execute([$user['id']]);
                
                header('Location: index.php');
                exit();
            } else {
                $error = 'Eres administrador. Debes seleccionar "Administrador" en el tipo de usuario.';
            }
        }
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema Frutería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #a8e063 0%, #56ab2f 100%);
            min-height: 100vh;
        }
        .login-container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px;
        }
        .login-logo {
            font-size: 2.5rem;
            color: #198754;
            margin-bottom: 16px;
            text-align: center;
        }
        .alert {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <i class="bi bi-apple"></i> Frutería
        </div>
        <h4 class="mb-4 text-center">Iniciar Sesión</h4>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" class="form-control" name="usuario" required autofocus 
                       value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="contraseña" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de usuario</label>
                <select class="form-select" name="tipo_usuario" required>
                    <option value="usuario" <?= ($_POST['tipo_usuario'] ?? '') === 'usuario' ? 'selected' : '' ?>>
                        Usuario Normal
                    </option>
                    <option value="admin" <?= ($_POST['tipo_usuario'] ?? '') === 'admin' ? 'selected' : '' ?>>
                        Administrador
                    </option>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-100">Entrar</button>
        </form>
        
        <div class="mt-3 text-center">
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> 
                Selecciona correctamente tu tipo de usuario
            </small>
        </div>
    </div>
</body>
</html>