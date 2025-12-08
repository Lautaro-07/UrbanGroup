<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/USAModel.php';
require_once __DIR__ . '/../includes/PhotoModel.php';

if (!isset($_SESSION['portal_client'])) {
    header('Location: portal_login.php?section=usa');
    exit;
}

$usaModel = new USAModel();
$photoModel = new PhotoModel();

$usStates = [
    'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas',
    'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware',
    'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho',
    'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas',
    'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland',
    'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi',
    'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada',
    'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York',
    'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma',
    'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina',
    'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah',
    'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia',
    'WI' => 'Wisconsin', 'WY' => 'Wyoming', 'DC' => 'District of Columbia'
];

$filters = [
    'operation_type' => $_GET['operation_type'] ?? '',
    'property_type' => $_GET['property_type'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'bedrooms' => $_GET['bedrooms'] ?? '',
    'bathrooms' => $_GET['bathrooms'] ?? '',
    'state' => $_GET['state'] ?? '',
    'pool' => isset($_GET['pool']) ? 1 : '',
    'waterfront' => isset($_GET['waterfront']) ? 1 : '',
    'is_project' => $_GET['show_projects'] ?? ''
];

$usaPropertyTypes = $usaModel->getUSAPropertyTypes();
$usaProperties = $usaModel->getUSAProperties($filters);
$featuredProjects = $usaModel->getProjects(6);

$pageTitle = 'Propiedades USA';
$currentPage = 'usa';
include __DIR__ . '/../templates/header.php';
?>

<div class="bg-gray-50 min-h-screen">
    <div class="bg-gradient-to-r from-red-600 via-blue-700 to-red-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Propiedades USA</h1>
                    <p class="text-white/80">Bienvenido, <?= htmlspecialchars($_SESSION['portal_client']['nombre_completo']) ?></p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="proyectos_usa.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Ver Proyectos
                    </a>
                    <a href="portal_logout.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition">
                        Cerrar Sesion
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($featuredProjects)): ?>
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Proyectos Destacados</h2>
            <a href="proyectos_usa.php" class="text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                Ver todos
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($featuredProjects as $project): ?>
                <?php $photos = $photoModel->getByPropertyId($project['id']); ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition group">
                    <div class="relative h-48">
                        <?php if (!empty($photos)): ?>
                            <img src="<?= htmlspecialchars($photos[0]['photo_url']) ?>" 
                                 alt="<?= htmlspecialchars($project['title']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-amber-400 to-red-600 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/50" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <span class="absolute top-3 left-3 bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            PROYECTO
                        </span>
                        <?php if (!empty($project['project_units'])): ?>
                        <span class="absolute top-3 right-3 bg-black/60 text-white text-xs font-bold px-2 py-1 rounded">
                            <?= $project['project_units'] ?> unidades
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-5">
                        <h3 class="font-bold text-gray-900 mb-2 line-clamp-2"><?= htmlspecialchars($project['title']) ?></h3>
                        
                        <?php if (!empty($project['project_developer'])): ?>
                        <p class="text-sm text-gray-500 mb-2">Por: <?= htmlspecialchars($project['project_developer']) ?></p>
                        <?php endif; ?>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold text-red-600">
                                <?= !empty($project['price_usd']) ? USAModel::formatUSD($project['price_usd']) : formatPrice($project['price']) ?>
                            </span>
                            <a href="propiedad_usa.php?id=<?= $project['id'] ?>" 
                               class="bg-amber-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-600 transition">
                                Ver Proyecto
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <form method="GET" class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros de Busqueda</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Operacion</label>
                    <select name="operation_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        <option value="">Todos</option>
                        <option value="Venta" <?= $filters['operation_type'] === 'Venta' ? 'selected' : '' ?>>Venta</option>
                        <option value="Arriendo" <?= $filters['operation_type'] === 'Arriendo' ? 'selected' : '' ?>>Arriendo (Rent)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Propiedad</label>
                    <select name="property_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        <option value="">Todos</option>
                        <?php foreach ($usaPropertyTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type['name']) ?>" <?= $filters['property_type'] === $type['name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado (State)</label>
                    <select name="state" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        <option value="">Todos</option>
                        <?php foreach ($usStates as $code => $name): ?>
                            <option value="<?= $code ?>" <?= $filters['state'] === $code ? 'selected' : '' ?>>
                                <?= htmlspecialchars($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Minimo (USD)</label>
                    <input type="number" name="min_price" value="<?= htmlspecialchars($filters['min_price']) ?>" 
                           placeholder="Desde" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Maximo (USD)</label>
                    <input type="number" name="max_price" value="<?= htmlspecialchars($filters['max_price']) ?>" 
                           placeholder="Hasta" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dormitorios</label>
                    <select name="bedrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        <option value="">Cualquiera</option>
                        <option value="1" <?= $filters['bedrooms'] === '1' ? 'selected' : '' ?>>1+</option>
                        <option value="2" <?= $filters['bedrooms'] === '2' ? 'selected' : '' ?>>2+</option>
                        <option value="3" <?= $filters['bedrooms'] === '3' ? 'selected' : '' ?>>3+</option>
                        <option value="4" <?= $filters['bedrooms'] === '4' ? 'selected' : '' ?>>4+</option>
                        <option value="5" <?= $filters['bedrooms'] === '5' ? 'selected' : '' ?>>5+</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Banos</label>
                    <select name="bathrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        <option value="">Cualquiera</option>
                        <option value="1" <?= $filters['bathrooms'] === '1' ? 'selected' : '' ?>>1+</option>
                        <option value="2" <?= $filters['bathrooms'] === '2' ? 'selected' : '' ?>>2+</option>
                        <option value="3" <?= $filters['bathrooms'] === '3' ? 'selected' : '' ?>>3+</option>
                        <option value="4" <?= $filters['bathrooms'] === '4' ? 'selected' : '' ?>>4+</option>
                    </select>
                </div>
                
                <div class="flex items-end gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="pool" value="1" <?= $filters['pool'] ? 'checked' : '' ?> 
                               class="w-4 h-4 text-red-600 rounded focus:ring-red-500">
                        <span class="text-sm font-medium text-gray-700">Piscina</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="waterfront" value="1" <?= $filters['waterfront'] ? 'checked' : '' ?> 
                               class="w-4 h-4 text-red-600 rounded focus:ring-red-500">
                        <span class="text-sm font-medium text-gray-700">Waterfront</span>
                    </label>
                </div>
                
                <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-1">
                    <button type="submit" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition">
                        Buscar
                    </button>
                    <a href="usa.php" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>

        <div class="mb-4 flex items-center justify-between">
            <p class="text-gray-600"><?= count($usaProperties) ?> propiedad(es) encontrada(s)</p>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="checkbox" name="show_projects" value="1" 
                           onchange="this.form.submit()" form="filter-form"
                           <?= $filters['is_project'] ? 'checked' : '' ?> 
                           class="w-4 h-4 text-amber-600 rounded focus:ring-amber-500">
                    <span class="font-medium text-gray-700">Solo Proyectos</span>
                </label>
            </div>
        </div>

        <?php if (empty($usaProperties)): ?>
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron propiedades USA</h3>
                <p class="text-gray-500">Intente ajustar los filtros de busqueda</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($usaProperties as $property): ?>
                    <?php $photos = $photoModel->getByPropertyId($property['id']); ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition group">
                        <div class="relative h-48">
                            <?php if (!empty($photos)): ?>
                                <img src="<?= htmlspecialchars($photos[0]['photo_url']) ?>" 
                                     alt="<?= htmlspecialchars($property['title']) ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-red-400 to-blue-600 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/50" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <span class="absolute top-3 left-3 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                                USA
                            </span>
                            
                            <?php if (!empty($property['usa_is_project'])): ?>
                            <span class="absolute top-3 right-3 bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded">
                                PROYECTO
                            </span>
                            <?php endif; ?>
                            
                            <div class="absolute bottom-3 left-3 flex gap-2">
                                <?php if (!empty($property['pool'])): ?>
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded">Piscina</span>
                                <?php endif; ?>
                                <?php if (!empty($property['waterfront'])): ?>
                                <span class="bg-teal-500 text-white text-xs px-2 py-1 rounded">Waterfront</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="p-5">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2"><?= htmlspecialchars($property['title']) ?></h3>
                            
                            <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($property['property_type']) ?></p>
                            
                            <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                                <?php if (!empty($property['bedrooms'])): ?>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <?= $property['bedrooms'] ?> Beds
                                </span>
                                <?php endif; ?>
                                <?php if (!empty($property['bathrooms'])): ?>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                    <?= $property['bathrooms'] ?> Baths
                                </span>
                                <?php endif; ?>
                                <?php if (!empty($property['surface_sqft'])): ?>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                    </svg>
                                    <?= number_format($property['surface_sqft'], 0, '', ',') ?> sqft
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold text-red-600">
                                    <?= !empty($property['price_usd']) ? USAModel::formatUSD($property['price_usd']) : formatPrice($property['price']) ?>
                                </span>
                                <a href="propiedad_usa.php?id=<?= $property['id'] ?>" 
                                   class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition">
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

<?php include __DIR__ . '/../templates/footer.php'; ?>
