<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/TerrenoModel.php';
require_once __DIR__ . '/../includes/LocationModel.php';
require_once __DIR__ . '/../includes/PropertyTypeModel.php';
require_once __DIR__ . '/../includes/PhotoModel.php';

if (!isset($_SESSION['portal_client'])) {
    header('Location: portal_login.php?section=activos');
    exit;
}

$terrenoModel = new TerrenoModel();
$locationModel = new LocationModel();
$propertyTypeModel = new PropertyTypeModel();
$photoModel = new PhotoModel();

$filters = [
    'operation_type' => $_GET['operation_type'] ?? '',
    'property_type' => $_GET['property_type'] ?? '',
    'region_id' => $_GET['region_id'] ?? '',
    'comuna_id' => $_GET['comuna_id'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'min_area' => $_GET['min_area'] ?? '',
    'max_area' => $_GET['max_area'] ?? '',
    'bedrooms' => $_GET['bedrooms'] ?? '',
    'bathrooms' => $_GET['bathrooms'] ?? ''
];

$activos = $terrenoModel->getActivosInmobiliarios($filters);
$regions = $locationModel->getRegions();
$propertyTypes = $propertyTypeModel->getAll();

$pageTitle = 'Activos Inmobiliarios';
$currentPage = 'activos';
include __DIR__ . '/../templates/header.php';
?>

<div class="bg-gray-50 min-h-screen">
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Activos Inmobiliarios</h1>
                    <p class="text-purple-100">Bienvenido, <?= htmlspecialchars($_SESSION['portal_client']['nombre_completo']) ?></p>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Operación</label>
                    <select name="operation_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Todos</option>
                        <option value="Venta" <?= $filters['operation_type'] === 'Venta' ? 'selected' : '' ?>>Venta</option>
                        <option value="Arriendo" <?= $filters['operation_type'] === 'Arriendo' ? 'selected' : '' ?>>Arriendo</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Propiedad</label>
                    <select name="property_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Todos</option>
                        <?php foreach ($propertyTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type['name']) ?>" <?= $filters['property_type'] === $type['name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Región</label>
                    <select name="region_id" id="regionSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
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
                    <select name="comuna_id" id="comunaSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Todas</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Mín (CLP)</label>
                    <input type="number" name="min_price" value="<?= htmlspecialchars($filters['min_price']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="0">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Máx (CLP)</label>
                    <input type="number" name="max_price" value="<?= htmlspecialchars($filters['max_price']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Sin límite">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Mín (m²)</label>
                    <input type="number" name="min_area" value="<?= htmlspecialchars($filters['min_area']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="0">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Máx (m²)</label>
                    <input type="number" name="max_area" value="<?= htmlspecialchars($filters['max_area']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Sin límite">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dormitorios (mín)</label>
                    <select name="bedrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Todos</option>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?= $i ?>" <?= $filters['bedrooms'] == $i ? 'selected' : '' ?>><?= $i ?>+</option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Baños (mín)</label>
                    <select name="bathrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Todos</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= $filters['bathrooms'] == $i ? 'selected' : '' ?>><?= $i ?>+</option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition">
                        Buscar
                    </button>
                    <a href="activos.php" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>

        <div class="mb-4">
            <p class="text-gray-600"><?= count($activos) ?> activo(s) encontrado(s)</p>
        </div>

        <?php if (empty($activos)): ?>
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron activos</h3>
                <p class="text-gray-500">Intente ajustar los filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($activos as $activo): ?>
                    <?php $photos = $photoModel->getByPropertyId($activo['id']); ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                        <div class="relative h-48">
                            <?php if (!empty($photos)): ?>
                                <img src="<?= htmlspecialchars($photos[0]['photo_url']) ?>" 
                                     alt="<?= htmlspecialchars($activo['title']) ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/50" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <span class="absolute top-3 left-3 bg-purple-600 text-white text-xs font-bold px-2 py-1 rounded">
                                <?= htmlspecialchars($activo['operation_type']) ?>
                            </span>
                        </div>
                        
                        <div class="p-5">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2"><?= htmlspecialchars($activo['title']) ?></h3>
                            
                            <p class="text-gray-500 text-sm mb-3">
                                <?= htmlspecialchars($activo['comuna_name'] ?? '') ?>, <?= htmlspecialchars($activo['region_name'] ?? '') ?>
                            </p>
                            
                            <p class="text-sm text-gray-600 mb-4"><?= htmlspecialchars($activo['property_type']) ?></p>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold text-purple-600">
                                    $<?= number_format($activo['price'], 0, ',', '.') ?>
                                </span>
                                <a href="propiedad.php?id=<?= $activo['id'] ?>" 
                                   class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition">
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
        fetch('<?= BASE_URL ?>api/comunas.php?region_id=' + regionId)
            .then(response => response.json())
            .then(data => {
                comunaSelect.innerHTML = '<option value="">Todas</option>';
                data.forEach(comuna => {
                    const selected = '<?= $filters['comuna_id'] ?? '' ?>' == comuna.id ? 'selected' : '';
                    comunaSelect.innerHTML += `<option value="${comuna.id}" ${selected}>${comuna.name}</option>`;
                });
            });
    } else {
        comunaSelect.innerHTML = '<option value="">Todas</option>';
    }
});

<?php if (!empty($filters['region_id'])): ?>
document.getElementById('regionSelect').dispatchEvent(new Event('change'));
<?php endif; ?>
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
