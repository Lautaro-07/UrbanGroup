<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/PropertyModel.php';
require_once __DIR__ . '/../includes/LocationModel.php';

$propertyModel = new PropertyModel();
$locationModel = new LocationModel();

$filters = [
    'operation_type' => $_GET['operation_type'] ?? '',
    'property_type' => $_GET['property_type'] ?? '',
    'region_id' => $_GET['region_id'] ?? '',
    'comuna_id' => $_GET['comuna_id'] ?? '',
    'bedrooms' => $_GET['bedrooms'] ?? '',
    'search' => $_GET['search'] ?? ''
];

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

$properties = $propertyModel->getAll($filters, $limit, $offset);
$totalProperties = $propertyModel->count($filters);
$totalPages = ceil($totalProperties / $limit);

$regions = $locationModel->getRegions();
$comunas = $locationModel->getComunas(!empty($filters['region_id']) ? $filters['region_id'] : null);

$pageTitle = 'Propiedades';
$currentPage = 'properties';

include __DIR__ . '/../templates/header.php';
?>

<!-- Header -->
<div class="bg-gradient-to-b from-gray-50 to-white py-8 md:py-12 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Propiedades</h1>
        <p class="text-gray-600 text-sm md:text-base">Encuentra tu propiedad ideal entre <?= $totalProperties ?> opciones disponibles</p>
    </div>
</div>

<!-- Filters -->
<section class="bg-white border-b border-gray-200 py-4 md:py-6">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <form action="" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 md:gap-4">
            <select name="operation_type" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                <option value="">Tipo de operación</option>
                <option value="Venta" <?= $filters['operation_type'] === 'Venta' ? 'selected' : '' ?>>Venta</option>
                <option value="Arriendo" <?= $filters['operation_type'] === 'Arriendo' ? 'selected' : '' ?>>Arriendo</option>
            </select>
            
            <select name="property_type" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                <option value="">Tipo de propiedad</option>
                <?php foreach (PROPERTY_TYPES as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $filters['property_type'] === $key ? 'selected' : '' ?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
            
            <select name="region_id" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                <option value="">Región</option>
                <?php foreach ($regions as $region): ?>
                    <option value="<?= $region['id'] ?>" <?= $filters['region_id'] == $region['id'] ? 'selected' : '' ?>><?= htmlspecialchars($region['name']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <select name="comuna_id" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                <option value="">Comuna</option>
                <?php foreach ($comunas as $comuna): ?>
                    <option value="<?= $comuna['id'] ?>" <?= $filters['comuna_id'] == $comuna['id'] ? 'selected' : '' ?>><?= htmlspecialchars($comuna['name']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" name="search" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Buscar..." value="<?= htmlspecialchars($filters['search']) ?>">
            
            <button type="submit" class="px-3 md:px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-sm">Buscar</button>
        </form>
    </div>
</section>

<!-- Properties Grid -->
<section class="py-8 md:py-12 lg:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <?php if (empty($properties)): ?>
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="text-lg md:text-xl font-semibold mb-2 text-gray-900">No se encontraron propiedades</h3>
                <p class="text-gray-600 text-sm md:text-base mb-4">Intenta con otros filtros de búsqueda.</p>
                <a href="propiedades.php" class="inline-block px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-sm">Limpiar filtros</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 mb-8">
                <?php foreach ($properties as $property): ?>
                    <a href="propiedad.php?id=<?= $property['id'] ?>" class="group">
                        <div class="hover-elevate bg-white border border-gray-200/50 rounded-xl overflow-hidden h-full flex flex-col">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="<?= getFirstImage($property['images']) ?>" alt="<?= htmlspecialchars($property['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute top-3 left-3">
                                    <span class="bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-lg">
                                        <?= formatPrice($property['price']) ?>
                                    </span>
                                </div>
                                <div class="absolute top-3 right-3">
                                    <span class="<?= $property['operation_type'] === 'Venta' ? 'bg-green-600' : 'bg-amber-500' ?> text-white text-xs px-2 py-1 rounded-md font-semibold">
                                        <?= $property['operation_type'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 md:p-4 space-y-2 flex-1 flex flex-col">
                                <h3 class="font-semibold text-gray-900 line-clamp-1 group-hover:text-blue-600 transition-colors text-sm md:text-base">
                                    <?= htmlspecialchars(truncateText($property['title'], 40)) ?>
                                </h3>
                                <p class="text-xs text-gray-600"><?= htmlspecialchars($property['comuna_name'] ?? '') ?></p>
                                <div class="flex gap-3 text-xs text-gray-600 flex-wrap">
                                    <?php if ($property['bedrooms'] > 0): ?>
                                        <span>🛏️ <?= $property['bedrooms'] ?></span>
                                    <?php endif; ?>
                                    <?php if ($property['bathrooms'] > 0): ?>
                                        <span>🚿 <?= $property['bathrooms'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="flex flex-wrap justify-center gap-2">
                    <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1])) ?>" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm">← Anterior</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="px-3 md:px-4 py-2 bg-blue-600 text-white rounded-lg font-medium text-sm"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?= http_build_query(array_merge($filters, ['page' => $page + 1])) ?>" class="px-3 md:px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm">Siguiente →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
