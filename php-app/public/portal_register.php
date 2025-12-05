<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/PortalClientModel.php';

$portalClientModel = new PortalClientModel();
$section = $_GET['section'] ?? 'terrenos';
$error = '';
$warning = '';
$success = '';

$sectionTitles = [
    'terrenos' => 'Terrenos Inmobiliarios',
    'activos' => 'Activos Inmobiliarios',
    'usa' => 'Propiedades USA'
];

$sectionTitle = $sectionTitles[$section] ?? 'Portal de Propiedades';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['razon_social', 'rut', 'representante_legal', 'nombre_completo', 
                 'cedula_identidad', 'celular', 'email', 'password', 'alias', 'consent'];
    
    $missing = [];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $missing[] = $field;
        }
    }
    
    if (!empty($missing)) {
        $error = 'Todos los campos son obligatorios';
    } else {
        $rutRaw = trim($_POST['rut'] ?? '');
        if (!PortalClientModel::validateRut($rutRaw)) {
            // Allow registration even if DV validation fails, but warn the user.
            $warning = 'El RUT ingresado no pasó la validación del dígito verificador. Se guardará tal como fue ingresado.';
        }
    }
    if (empty($error) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'El email ingresado no es válido';
    } elseif (strlen($_POST['password']) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } elseif ($_POST['password'] !== $_POST['password_confirm']) {
        $error = 'Las contraseñas no coinciden';
    } else {
        try {
            $result = $portalClientModel->create([
            'razon_social' => trim($_POST['razon_social']),
            'rut' => $rutRaw,
                'registered_sections' => $section,
            'representante_legal' => trim($_POST['representante_legal']),
            'nombre_completo' => trim($_POST['nombre_completo']),
            'cedula_identidad' => trim($_POST['cedula_identidad']),
            'celular' => trim($_POST['celular']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'alias' => trim($_POST['alias'])
        ]);
        } catch (Exception $e) {
            $result = ['error' => 'Error al crear el registro: ' . $e->getMessage()];
        }

        if (is_array($result) && isset($result['error'])) {
            $error = $result['error'];
        } else {
            $success = 'Registro exitoso. Ahora puede iniciar sesión.';
        }
    }
}

$pageTitle = 'Registro - ' . $sectionTitle;
$currentPage = 'portal-register';
include __DIR__ . '/../templates/header.php';
?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Registro de Cliente</h1>
                <p class="text-gray-600"><?= htmlspecialchars($sectionTitle) ?></p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($warning)): ?>
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-6">
                    <?= htmlspecialchars($warning) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <?= htmlspecialchars($success) ?>
                    <a href="portal_login.php?section=<?= $section ?>" class="underline font-medium">Iniciar Sesión</a>
                </div>
            <?php else: ?>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Razón Social *</label>
                        <input type="text" name="razon_social" value="<?= htmlspecialchars($_POST['razon_social'] ?? '') ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">RUT *</label>
                        <input type="text" name="rut" value="<?= htmlspecialchars($_POST['rut'] ?? '') ?>" 
                               placeholder="12.345.678-9"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Representante Legal *</label>
                        <input type="text" name="representante_legal" value="<?= htmlspecialchars($_POST['representante_legal'] ?? '') ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                        <input type="text" name="nombre_completo" value="<?= htmlspecialchars($_POST['nombre_completo'] ?? '') ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cédula de Identidad *</label>
                        <input type="text" name="cedula_identidad" value="<?= htmlspecialchars($_POST['cedula_identidad'] ?? '') ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Celular *</label>
                        <input type="tel" name="celular" value="<?= htmlspecialchars($_POST['celular'] ?? '') ?>" 
                               placeholder="+56 9 1234 5678"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alias *</label>
                        <input type="text" name="alias" value="<?= htmlspecialchars($_POST['alias'] ?? '') ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                        <input type="password" name="password" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <p class="text-xs text-gray-500 mt-1">Mínimo 6 caracteres</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña *</label>
                        <input type="password" name="password_confirm" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                    <div class="flex items-start">
                        <input type="checkbox" name="consent" id="consent" value="1" 
                               class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" required>
                        <label for="consent" class="ml-3 text-sm text-gray-700">
                            <span class="font-semibold">ACEPTO LOS TÉRMINOS Y CONDICIONES:</span>
                            <p class="mt-2 text-justify leading-relaxed">
                                AL REGISTRARME EN ESTE PORTAL DE PROPIEDADES, ACEPTO QUE EN CASO DE CONCRETARSE UNA O MÁS COMPRAVENTAS, 
                                PAGARÉ A URBAN GROUP SPA LA COMISIÓN ESTABLECIDA DEL <strong>2,0% MÁS IVA</strong> SOBRE EL VALOR FINAL 
                                DE COMPRAVENTA DE CADA PROPIEDAD. POR ELLO, SE INCLUIRÁ EN LA PROMESA DE COMPRAVENTA, UNA CLÁUSULA DE 
                                COMISIONES QUE REGULARÁ SU PAGO.
                            </p>
                            <p class="mt-3 text-justify leading-relaxed text-gray-600">
                                La información contenida en este portal de propiedades es confidencial, de carácter privado y es 
                                proporcionada únicamente al cliente registrado, por lo que se prohíbe absolutamente el uso no autorizado, 
                                la divulgación parcial o total de su contenido y/o de las imágenes.
                            </p>
                        </label>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition">
                        Registrarme
                    </button>
                    <a href="portal_login.php?section=<?= $section ?>" 
                       class="flex-1 text-center border border-gray-300 text-gray-700 py-3 px-6 rounded-lg font-medium hover:bg-gray-50 transition">
                        Ya tengo cuenta
                    </a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
