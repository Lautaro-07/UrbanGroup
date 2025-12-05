<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/UserModel.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Por favor, ingrese un nombre de usuario y una nueva contraseña.';
    } else {
        try {
            $userModel = new UserModel();
            $user = $userModel->getByUsername($username);

            if ($user) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $db = Database::getInstance()->getConnection();
                
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                
                if ($stmt->execute([$hashedPassword, $user['id']])) {
                    $message = "La contraseña para el usuario '<strong>" . htmlspecialchars($username) . "</strong>' se ha actualizado correctamente.";
                } else {
                    $error = "Error al actualizar la contraseña.";
                }
            } else {
                $error = "No se encontró un usuario con el nombre '<strong>" . htmlspecialchars($username) . "</strong>'.";
            }
        } catch (Exception $e) {
            $error = 'Ocurrió un error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herramienta para Hashear Contraseñas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-lg bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Actualizar Contraseña de Usuario</h1>
            <p class="text-gray-500 mt-2 text-sm">
                Esta herramienta actualiza la contraseña de un usuario (admin o socio) y la guarda en el formato seguro (hash) que la aplicación necesita.
            </p>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg relative mb-6 text-sm" role="alert">
                <span><?= $message ?></span>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg relative mb-6 text-sm" role="alert">
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="hash_password.php" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Nombre de Usuario</label>
                <input type="text" name="username" id="username" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                       placeholder="Ej: admin">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                       placeholder="Ingrese la nueva contraseña segura">
            </div>

            <div>
                <button type="submit"
                        class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    Actualizar Contraseña
                </button>
            </div>
        </form>
    </div>

</body>
</html>
