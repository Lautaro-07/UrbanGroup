<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/FavoriteModel.php';
require_once __DIR__ . '/../../includes/PhotoModel.php';

if (!isset($_SESSION['portal_client_id'])) {
    header('Location: login.php?redirect=cliente/favoritos.php');
    exit;
}

$favoriteModel = new FavoriteModel();
$photoModel = new PhotoModel();

$favorites = $favoriteModel->getClientFavorites($_SESSION['portal_client_id']);

$pageTitle = 'Mis Favoritos';
$currentPage = 'favoritos';
include __DIR__ . '/../../templates/header.php';
?>

<div class="bg-gradient-to-b from-gray-50 to-white py-8 md:py-12 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Mis Favoritos</h1>
                <p class="text-gray-600">Hola, <?= htmlspecialchars($_SESSION['portal_client_name']) ?></p>
            </div>
            <a href="logout.php" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">Cerrar Sesión</a>
        </div>
    </div>
</div>

<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <?php if (empty($favorites)): ?>
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No tienes favoritos aún</h3>
                <p class="text-gray-600 mb-4">Explora nuestras propiedades y guarda tus favoritas</p>
                <a href="../propiedades.php" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Ver Propiedades
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($favorites as $p): ?>
                    <?php
                    $photos = $photoModel->getByPropertyId($p['id']);
                    $photo = null;
                    if (!empty($photos) && !empty($photos[0]['photo_url'])) {
                        $photo = getPropertyPhotoUrl($photos[0]['photo_url'], true);
                    } else {
                        $photo = getFirstImage($p['images'] ?? '[]');
                    }
                    ?>
                    <div class="bg-white border rounded-xl overflow-hidden hover:shadow-lg transition group relative">
                        <button onclick="removeFavorite(<?= $p['id'] ?>, this)" class="absolute top-3 right-3 z-10 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-50 transition">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        
                        <a href="../propiedad.php?id=<?= $p['id'] ?>">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="<?= $photo ?>" class="w-full h-full object-cover group-hover:scale-105 duration-300">
                                <span class="absolute top-3 left-3 text-xs px-3 py-1 bg-blue-600 text-white rounded-lg">
                                    <?= formatPrice($p['price']) ?>
                                </span>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600">
                                    <?= htmlspecialchars(truncateText($p['title'], 40)) ?>
                                </h3>
                                <p class="text-xs text-gray-600"><?= $p['comuna_name'] ?? '' ?></p>
                                <div class="flex gap-4 text-xs text-gray-600 mt-2">
                                    <?php if (($p['bedrooms'] ?? 0) > 0): ?><span>🛏 <?= $p['bedrooms'] ?></span><?php endif; ?>
                                    <?php if (($p['bathrooms'] ?? 0) > 0): ?><span>🚿 <?= $p['bathrooms'] ?></span><?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function removeFavorite(propertyId, button) {
    if (!confirm('¿Eliminar de favoritos?')) return;
    
    fetch('api/favorites.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=remove&property_id=' + propertyId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            button.closest('.group').remove();
            if (document.querySelectorAll('.group').length === 0) {
                location.reload();
            }
        }
    });
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
