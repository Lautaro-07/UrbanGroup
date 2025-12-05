<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/CarouselModel.php';
require_once __DIR__ . '/../../includes/base_url.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$carouselModel = new CarouselModel();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $result = $carouselModel->create([
                'title' => $_POST['title'] ?? '',
                'alt_text' => $_POST['alt_text'] ?? ''
            ], $_FILES['image'] ?? null);
            
            if (is_array($result) && isset($result['error'])) {
                $error = $result['error'];
            } else {
                $message = 'Imagen agregada correctamente';
            }
            break;
            
        case 'update':
            $id = (int)$_POST['id'];
            $result = $carouselModel->update($id, [
                'title' => $_POST['title'] ?? '',
                'alt_text' => $_POST['alt_text'] ?? '',
                'display_order' => (int)($_POST['display_order'] ?? 0)
            ], $_FILES['image'] ?? null);
            
            if (is_array($result) && isset($result['error'])) {
                $error = $result['error'];
            } else {
                $message = 'Imagen actualizada correctamente';
            }
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            $carouselModel->delete($id);
            $message = 'Imagen eliminada correctamente';
            break;
            
        case 'toggle':
            $id = (int)$_POST['id'];
            $carouselModel->toggleActive($id);
            $message = 'Estado de imagen actualizado';
            break;
            
        case 'reorder':
            $order = json_decode($_POST['order'] ?? '[]', true);
            if ($order) {
                $carouselModel->updateOrder($order);
                $message = 'Orden actualizado';
            }
            break;
    }
}

$images = $carouselModel->getAll();

$pageTitle = 'Gestión de Carousel';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Urban Group Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="index.php" class="text-blue-600 hover:text-blue-800">
                        ← Volver al Panel
                    </a>
                    <h1 class="text-xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                </div>
                <span class="text-sm text-gray-500"><?= count($images) ?> / 8 imágenes</span>
            </div>
        </div>
        <!-- Enlaces de navegación para escritorio -->
        <div class="max-w-7xl mx-auto px-4 py-2 border-t flex gap-2 overflow-x-auto scrollbar-hide">
            <a href="index.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200">Dashboard</a>
            <a href="index.php?action=properties" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200">Propiedades</a>
            <a href="index.php?action=partners" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200">Socios</a>
            <a href="property_types.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200">Tipos</a>
            <a href="carousel.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-blue-600 text-white">Carousel</a>
            <a href="portal_clients.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200">Clientes Portal</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php if ($message): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (count($images) < 8): ?>
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Agregar Nueva Imagen</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" value="create">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imagen *</label>
                        <input type="file" name="image" accept="image/*" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, WebP. Máx 5MB</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                        <input type="text" name="title" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="Título opcional">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Texto Alternativo</label>
                        <input type="text" name="alt_text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="Descripción de la imagen">
                    </div>
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Agregar Imagen
                </button>
            </form>
        </div>
        <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-6">
            Has alcanzado el límite de 8 imágenes. Elimina una para agregar más.
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Imágenes del Carousel</h2>
            
            <?php if (empty($images)): ?>
                <p class="text-gray-500 text-center py-8">No hay imágenes en el carousel</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="imageGrid">
                    <?php foreach ($images as $image): ?>
                        <div class="border rounded-lg overflow-hidden <?= $image['is_active'] ? 'border-green-300' : 'border-gray-300 opacity-60' ?>">
                            <div class="relative h-40">
                                <img src="<?= BASE_URL . htmlspecialchars($image['file_path']) ?>" 
                                     alt="<?= htmlspecialchars($image['alt_text'] ?? '') ?>"
                                     class="w-full h-full object-cover">
                                <span class="absolute top-2 left-2 bg-black/50 text-white text-xs px-2 py-1 rounded">
                                    #<?= $image['display_order'] ?>
                                </span>
                                <?php if (!$image['is_active']): ?>
                                    <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                                        Inactiva
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-3">
                                <p class="text-sm font-medium text-gray-900 truncate mb-2">
                                    <?= htmlspecialchars($image['title'] ?: 'Sin título') ?>
                                </p>
                                
                                <div class="flex gap-2">
                                    <button onclick="editImage(<?= htmlspecialchars(json_encode($image)) ?>)"
                                            class="flex-1 text-xs bg-gray-100 text-gray-700 py-1 px-2 rounded hover:bg-gray-200 transition">
                                        Editar
                                    </button>
                                    
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?= $image['id'] ?>">
                                        <button type="submit" class="w-full text-xs <?= $image['is_active'] ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' ?> py-1 px-2 rounded hover:opacity-80 transition">
                                            <?= $image['is_active'] ? 'Desactivar' : 'Activar' ?>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" class="flex-1" onsubmit="return confirm('¿Eliminar esta imagen?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $image['id'] ?>">
                                        <button type="submit" class="w-full text-xs bg-red-100 text-red-700 py-1 px-2 rounded hover:bg-red-200 transition">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Imagen</h3>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editId">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Imagen (opcional)</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="title" id="editTitle"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texto Alternativo</label>
                    <input type="text" name="alt_text" id="editAltText"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                    <input type="number" name="display_order" id="editOrder" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                        Guardar
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editImage(image) {
            document.getElementById('editId').value = image.id;
            document.getElementById('editTitle').value = image.title || '';
            document.getElementById('editAltText').value = image.alt_text || '';
            document.getElementById('editOrder').value = image.display_order || 1;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }
        
        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
        
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>
