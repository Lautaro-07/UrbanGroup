<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/TerrenoModel.php';
require_once __DIR__ . '/../includes/LocationModel.php';
require_once __DIR__ . '/../includes/PhotoModel.php';

if (!isset($_SESSION['portal_client'])) {
    header('Location: portal_login.php?section=terrenos');
    exit;
}

$terrenoModel = new TerrenoModel();
$locationModel = new LocationModel();
$photoModel = new PhotoModel();

$filters = [
    'region_id' => $_GET['region_id'] ?? '',
    'comuna_id' => $_GET['comuna_id'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'min_area' => $_GET['min_area'] ?? '',
    'max_area' => $_GET['max_area'] ?? '',
    'has_anteproyecto' => $_GET['has_anteproyecto'] ?? ''
];

$terrenos = $terrenoModel->getTerrenos($filters);
$regions = $locationModel->getRegions();

$pageTitle = 'Terrenos Inmobiliarios';
$currentPage = 'terrenos';
include __DIR__ . '/../templates/header.php';
?>

<div class="bg-gray-50 min-h-screen">
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Terrenos Inmobiliarios</h1>
                    <p class="text-blue-100">Bienvenido, <?= htmlspecialchars($_SESSION['portal_client']['nombre_completo']) ?></p>
                </div>
                <a href="portal_logout.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition">
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <form method="GET" class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros de Búsqueda</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Región</label>
                    <select name="region_id" id="regionSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?= $region['id'] ?>" <?= $filters['region_id'] == $region['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($region['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comuna</label>
                    <select name="comuna_id" id="comunaSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Mín (CLP)</label>
                    <input type="number" name="min_price" value="<?= htmlspecialchars($filters['min_price']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Máx (CLP)</label>
                    <input type="number" name="max_price" value="<?= htmlspecialchars($filters['max_price']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Sin límite">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Mín (m²)</label>
                    <input type="number" name="min_area" value="<?= htmlspecialchars($filters['min_area']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Máx (m²)</label>
                    <input type="number" name="max_area" value="<?= htmlspecialchars($filters['max_area']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Sin límite">
                </div>
                
                <div class="flex items-end">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="has_anteproyecto" value="1" <?= $filters['has_anteproyecto'] ? 'checked' : '' ?>
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Con Anteproyecto</span>
                    </label>
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                        Buscar
                    </button>
                    <a href="terrenos.php" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>

        <div class="mb-4">
            <p class="text-gray-600"><?= count($terrenos) ?> terreno(s) encontrado(s)</p>
        </div>

        <?php if (empty($terrenos)): ?>
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron terrenos</h3>
                <p class="text-gray-500">Intente ajustar los filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($terrenos as $terreno): ?>
                    <?php $photos = $photoModel->getByPropertyId($terreno['id']); ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                        <div class="relative h-48">
                            <?php if (!empty($photos)): ?>
                                <img src="<?= htmlspecialchars($photos[0]['photo_url']) ?>" 
                                     alt="<?= htmlspecialchars($terreno['title']) ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/50" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.14 2 5 5.14 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.86-3.14-7-7-7z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terreno['has_anteproyecto'])): ?>
                                <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                                    CON ANTEPROYECTO
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-5">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2"><?= htmlspecialchars($terreno['title']) ?></h3>
                            
                            <p class="text-gray-500 text-sm mb-3">
                                <?= htmlspecialchars($terreno['comuna_name'] ?? '') ?>, <?= htmlspecialchars($terreno['region_name'] ?? '') ?>
                            </p>
                            
                            <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                    </svg>
                                    <?= number_format($terreno['total_area'] ?? 0, 0, ',', '.') ?> m²
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold text-blue-600">
                                    $<?= number_format($terreno['price'], 0, ',', '.') ?>
                                </span>
                                <a href="propiedad.php?id=<?= $terreno['id'] ?>" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('regionSelect').addEventListener('change', function() {
    const regionId = this.value;
    const comunaSelect = document.getElementById('comunaSelect');
    
    comunaSelect.innerHTML = '<option value="">Cargando...</option>';
    
    if (regionId) {
        fetch('/api/get-comunas.php?region_id=' + regionId)
            .then(response => response.json())
            .then(data => {
                comunaSelect.innerHTML = '<option value="">Todas</option>';
                data.forEach(comuna => {
                    comunaSelect.innerHTML += `<option value="${comuna.id}">${comuna.name}</option>`;
                });
            });
    } else {
        comunaSelect.innerHTML = '<option value="">Todas</option>';
    }
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
