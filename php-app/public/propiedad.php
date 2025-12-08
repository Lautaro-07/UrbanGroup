<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/PropertyModel.php';
require_once __DIR__ . '/../includes/PhotoModel.php';
require_once __DIR__ . '/../includes/PropertyDetailsModel.php';
require_once __DIR__ . '/../includes/FavoriteModel.php';
require_once __DIR__ . '/../includes/ClientActivityModel.php';
require_once __DIR__ . '/../includes/TerrenoModel.php';

$propertyModel = new PropertyModel();
$photoModel = new PhotoModel();
$detailsModel = new PropertyDetailsModel();
$favoriteModel = new FavoriteModel();
$activityModel = new ClientActivityModel();
$terrenoModel = new TerrenoModel();

$id = (int)($_GET['id'] ?? 0);
$property = $propertyModel->getById($id);

if (!$property) {
    header('Location: /propiedades.php');
    exit;
}

$isTerreno = ($property['section_type'] ?? '') === 'terrenos';
$terrenoDetails = null;
if ($isTerreno) {
    $terrenoDetails = $terrenoModel->getDetailsByPropertyId($id);
}

if (isset($_SESSION['portal_client_id'])) {
    $activityModel->trackView($_SESSION['portal_client_id'], $id);
}

$propertyPhotos = $photoModel->getByPropertyId($id);
$images = !empty($propertyPhotos) ? array_map(function($p) { 
    return getPropertyPhotoUrl($p['photo_url'], true);
}, $propertyPhotos) : [];
if (empty($images)) {
    $images = getImages($property['images']);
}
$features = getFeatures($property['features'] ?? '[]');

$propertyDetails = $detailsModel->getByPropertyId($id);
$detailsData = $propertyDetails ? json_decode($propertyDetails['details_json'] ?? '{}', true) : [];
$featuresData = $propertyDetails ? json_decode($propertyDetails['features_json'] ?? '[]', true) : [];
$costsData = $propertyDetails ? json_decode($propertyDetails['costs_json'] ?? '{}', true) : [];

$clientLoggedIn = isset($_SESSION['portal_client_id']);
$isFavorite = $clientLoggedIn ? $favoriteModel->isFavorite($_SESSION['portal_client_id'], $id) : false;

$sectionType = $property['section_type'] ?? 'propiedades';
$similarProperties = $propertyModel->getSimilar($id, $sectionType, $property['property_type'], $property['price'], 4);

$propertyUrl = SITE_URL . '/propiedad.php?id=' . $id;
$whatsappNumber = defined('WHATSAPP_NUMBER') ? preg_replace('/[^0-9]/', '', WHATSAPP_NUMBER) : '542914125043';

$pageTitle = $property['title'];
$currentPage = 'properties';

include __DIR__ . '/../templates/header.php';
?>

<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm">
                <a href="/" class="text-blue-600 hover:text-blue-700">Inicio</a>
                <span class="text-gray-400">/</span>
                <?php if ($isTerreno): ?>
                    <a href="/terrenos.php" class="text-blue-600 hover:text-blue-700">Terrenos</a>
                <?php else: ?>
                    <a href="/propiedades.php" class="text-blue-600 hover:text-blue-700">Propiedades</a>
                <?php endif; ?>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600"><?= htmlspecialchars(truncateText($property['title'], 50)) ?></span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="toggleFavorite(<?= $id ?>)" id="favBtn" class="p-2 rounded-full border <?= $isFavorite ? 'bg-red-50 border-red-200' : 'bg-white border-gray-200' ?> hover:bg-red-50 transition" title="<?= $clientLoggedIn ? 'Agregar a favoritos' : 'Inicia sesión para guardar favoritos' ?>">
                    <svg class="w-5 h-5 <?= $isFavorite ? 'text-red-500' : 'text-gray-400' ?>" id="favIcon" fill="<?= $isFavorite ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
                <button onclick="openShareModal()" class="p-2 rounded-full bg-white border border-gray-200 hover:bg-blue-50 transition" title="Compartir">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<section class="py-8 lg:py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="mb-8">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-gray-100 mb-4">
                        <img src="<?= $images[0] ?? 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800' ?>" alt="<?= htmlspecialchars($property['title']) ?>" id="mainImage" class="w-full h-full object-cover">
                    </div>
                    
                    <?php if (count($images) > 1): ?>
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                            <?php foreach ($images as $index => $image): ?>
                                <div class="aspect-square rounded-lg overflow-hidden cursor-pointer border-2 <?= $index === 0 ? 'border-blue-600' : 'border-gray-200' ?> hover:border-blue-600 transition" onclick="changeImage('<?= htmlspecialchars($image) ?>', this)">
                                    <img src="<?= $image ?>" alt="Imagen <?= $index + 1 ?>" class="w-full h-full object-cover">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Descripción</h2>
                    <p class="text-gray-600 leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($property['description']) ?></p>
                </div>

                <div class="mb-8 bg-gray-50 rounded-xl p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Características Básicas</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php if (!empty($property['bedrooms']) && $property['bedrooms'] > 0): ?>
                            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                <div class="text-2xl mb-1">🛏️</div>
                                <p class="text-xl font-bold text-gray-900"><?= $property['bedrooms'] ?></p>
                                <p class="text-xs text-gray-600">Dormitorios</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['bathrooms']) && $property['bathrooms'] > 0): ?>
                            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                <div class="text-2xl mb-1">🚿</div>
                                <p class="text-xl font-bold text-gray-900"><?= $property['bathrooms'] ?></p>
                                <p class="text-xs text-gray-600">Baños</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['built_area']) && $property['built_area'] > 0): ?>
                            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                <div class="text-2xl mb-1">📐</div>
                                <p class="text-xl font-bold text-gray-900"><?= round($property['built_area']) ?></p>
                                <p class="text-xs text-gray-600">m² Construidos</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['total_area']) && $property['total_area'] > 0): ?>
                            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                <div class="text-2xl mb-1">🏞️</div>
                                <p class="text-xl font-bold text-gray-900"><?= round($property['total_area']) ?></p>
                                <p class="text-xs text-gray-600">m² Terreno</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['parking_spots']) && $property['parking_spots'] > 0): ?>
                            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                <div class="text-2xl mb-1">🚗</div>
                                <p class="text-xl font-bold text-gray-900"><?= $property['parking_spots'] ?></p>
                                <p class="text-xs text-gray-600">Estacionamientos</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($isTerreno && $terrenoDetails): ?>
                <div class="mb-8 bg-green-50 rounded-xl p-6 border border-green-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Información del Terreno</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <?php if (!empty($terrenoDetails['zona_prc_edificacion'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Zona PRC Edificación</p>
                                <p class="font-semibold text-gray-900"><?= htmlspecialchars($terrenoDetails['zona_prc_edificacion']) ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['usos_suelo'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Usos de Suelo</p>
                                <p class="font-semibold text-gray-900"><?= htmlspecialchars($terrenoDetails['usos_suelo']) ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['superficie_util'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Superficie Útil</p>
                                <p class="font-semibold text-gray-900"><?= number_format($terrenoDetails['superficie_util'], 0, ',', '.') ?> m²</p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['superficie_bruta'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Superficie Bruta</p>
                                <p class="font-semibold text-gray-900"><?= number_format($terrenoDetails['superficie_bruta'], 0, ',', '.') ?> m²</p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['densidad_bruta_max_hab_ha'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Densidad Bruta Máx</p>
                                <p class="font-semibold text-gray-900"><?= number_format($terrenoDetails['densidad_bruta_max_hab_ha'], 0, ',', '.') ?> Hab/Ha</p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['densidad_neta_max_hab_ha'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Densidad Neta Máx</p>
                                <p class="font-semibold text-gray-900"><?= number_format($terrenoDetails['densidad_neta_max_hab_ha'], 0, ',', '.') ?> Hab/Ha</p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['coef_constructibilidad_max'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Coef. Constructibilidad</p>
                                <p class="font-semibold text-gray-900"><?= $terrenoDetails['coef_constructibilidad_max'] ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['coef_ocupacion_suelo_max'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Coef. Ocupación Suelo</p>
                                <p class="font-semibold text-gray-900"><?= $terrenoDetails['coef_ocupacion_suelo_max'] ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['altura_maxima'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Altura Máxima</p>
                                <p class="font-semibold text-gray-900"><?= $terrenoDetails['altura_maxima'] ?> m</p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['precio_uf_m2'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Precio UF/m²</p>
                                <p class="font-semibold text-gray-900"><?= number_format($terrenoDetails['precio_uf_m2'], 2, ',', '.') ?> UF</p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['frente'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Frente</p>
                                <p class="font-semibold text-gray-900"><?= $terrenoDetails['frente'] ?> m</p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($terrenoDetails['fondo'])): ?>
                            <div class="bg-white rounded-lg p-3 shadow-sm">
                                <p class="text-xs text-gray-500">Fondo</p>
                                <p class="font-semibold text-gray-900"><?= $terrenoDetails['fondo'] ?> m</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($terrenoDetails['has_anteproyecto'])): ?>
                    <div class="mt-6 p-4 bg-green-100 rounded-lg border border-green-300">
                        <h3 class="font-bold text-green-800 mb-2">✓ Con Anteproyecto Aprobado</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <?php if (!empty($terrenoDetails['num_viviendas'])): ?>
                                <div><span class="text-green-700">Viviendas:</span> <strong><?= $terrenoDetails['num_viviendas'] ?></strong></div>
                            <?php endif; ?>
                            <?php if (!empty($terrenoDetails['num_estacionamientos'])): ?>
                                <div><span class="text-green-700">Estacionamientos:</span> <strong><?= $terrenoDetails['num_estacionamientos'] ?></strong></div>
                            <?php endif; ?>
                            <?php if (!empty($terrenoDetails['superficie_edificada'])): ?>
                                <div><span class="text-green-700">Sup. Edificada:</span> <strong><?= number_format($terrenoDetails['superficie_edificada'], 0, ',', '.') ?> m²</strong></div>
                            <?php endif; ?>
                            <?php if (!empty($terrenoDetails['num_bodegas'])): ?>
                                <div><span class="text-green-700">Bodegas:</span> <strong><?= $terrenoDetails['num_bodegas'] ?></strong></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($terrenoDetails['observaciones'])): ?>
                    <div class="mt-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Observaciones</h4>
                        <p class="text-gray-600"><?= nl2br(htmlspecialchars($terrenoDetails['observaciones'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php 
                $hasDetailedInfo = !empty($features) || !empty($detailsData) || !empty($featuresData) || !empty($costsData);
                if ($hasDetailedInfo): 
                ?>
                <div class="mb-8">
                    <button onclick="toggleAllInfo()" id="toggleInfoBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" id="toggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span id="toggleText">Mostrar toda la información</span>
                    </button>
                    
                    <div id="allInfoSection" class="hidden mt-6 space-y-6">
                        <?php if (!empty($features)): ?>
                            <div class="bg-white border border-gray-200 rounded-xl p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Características</h3>
                                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <?php foreach ($features as $feature): ?>
                                        <li class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-gray-700"><?= htmlspecialchars($feature) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($detailsData)): ?>
                            <div class="bg-white border border-gray-200 rounded-xl p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Datos Físicos</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <?php foreach ($detailsData as $key => $value): ?>
                                        <?php if (!empty($value)): ?>
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 uppercase"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) ?></p>
                                                <p class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($value) ?></p>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($costsData)): ?>
                            <div class="bg-white border border-gray-200 rounded-xl p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Costos</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <?php foreach ($costsData as $key => $value): ?>
                                        <?php if (!empty($value)): ?>
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 uppercase"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) ?></p>
                                                <p class="text-lg font-semibold text-gray-900">$<?= number_format($value, 0, ',', '.') ?></p>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($featuresData)): ?>
                            <div class="bg-white border border-gray-200 rounded-xl p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Características Adicionales</h3>
                                <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    <?php foreach ($featuresData as $feat): ?>
                                        <li class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-gray-700"><?= htmlspecialchars(PropertyDetailsModel::getFeatureLabel($feat)) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php 
                $latitude = floatval($property['latitude'] ?? -33.8688);
                $longitude = floatval($property['longitude'] ?? -151.2093);
                ?>
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Ubicación Aproximada</h2>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
                    <div id="propertyMap" style="width: 100%; height: 320px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;"></div>
                    <p class="text-sm text-gray-500 mt-2">📍 Ubicación aproximada en <?= htmlspecialchars($property['comuna_name'] ?? '') ?>, <?= htmlspecialchars($property['region_name'] ?? '') ?> (radio de 500m)</p>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 sticky top-24">
                    <div class="flex gap-2 mb-4">
                        <span class="inline-block px-3 py-1 <?= $property['operation_type'] === 'Venta' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?> text-xs font-semibold rounded-lg">
                            <?= $property['operation_type'] ?>
                        </span>
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg">
                            <?= ucfirst($property['property_type']) ?>
                        </span>
                    </div>

                    <h1 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($property['title']) ?></h1>
                    <div class="flex items-start gap-2 text-gray-600 mb-6 pb-6 border-b border-gray-200">
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <div>
                            <p><?= htmlspecialchars($property['address'] ?? '') ?></p>
                            <p class="text-sm"><?= htmlspecialchars($property['comuna_name'] ?? '') ?>, <?= htmlspecialchars($property['region_name'] ?? '') ?></p>
                        </div>
                    </div>

                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-gray-600 text-sm mb-1">Precio</p>
                        <p class="text-3xl font-bold text-gray-900">
                            <?= formatPrice($property['price'], $property['currency'] ?? 'CLP') ?>
                            <?php if ($property['operation_type'] === 'Arriendo'): ?>
                                <span class="text-lg text-gray-600">/mes</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="mb-6 pb-6 border-b border-gray-200 space-y-3">
                        <a href="https://wa.me/<?= $whatsappNumber ?>?text=<?= urlencode('Hola, me interesa la propiedad "' . $property['title'] . '" (' . formatPrice($property['price'], $property['currency'] ?? 'CLP') . ') que vi en UrbanPropiedades: ' . $propertyUrl) ?>" target="_blank" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Contactar por WhatsApp
                        </a>
                        
                        <a href="api/download-property-pdf.php?id=<?= $property['id'] ?>" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Descargar PDF
                        </a>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">¿Interesado en esta propiedad?</h3>
                        <form method="POST" action="/api/contact.php" class="space-y-3" onsubmit="return sendWhatsApp(event)">
                            <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                            <input type="hidden" name="property_title" value="<?= htmlspecialchars($property['title']) ?>">
                            
                            <input type="text" name="name" placeholder="Tu nombre" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            
                            <input type="email" name="email" placeholder="Tu email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            
                            <input type="tel" name="phone" placeholder="Tu teléfono" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition">
                                Enviar Consulta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="shareModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-6 max-w-md w-full">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Compartir Propiedad</h3>
            <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-2 gap-3 mb-4">
            <a href="https://wa.me/?text=<?= urlencode('Mira esta propiedad: ' . $property['title'] . ' - ' . $propertyUrl) ?>" target="_blank" class="flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-medium py-3 rounded-lg transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($propertyUrl) ?>" target="_blank" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?text=<?= urlencode('Mira esta propiedad: ' . $property['title']) ?>&url=<?= urlencode($propertyUrl) ?>" target="_blank" class="flex items-center justify-center gap-2 bg-black hover:bg-gray-800 text-white font-medium py-3 rounded-lg transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                X (Twitter)
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($propertyUrl) ?>&title=<?= urlencode($property['title']) ?>" target="_blank" class="flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-medium py-3 rounded-lg transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                LinkedIn
            </a>
        </div>
        
        <div class="border-t border-gray-200 pt-4">
            <p class="text-sm text-gray-600 mb-2">Copiar enlace</p>
            <div class="flex gap-2">
                <input type="text" value="<?= $propertyUrl ?>" id="shareUrl" readonly class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50">
                <button onclick="copyLink()" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-lg transition text-sm">Copiar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
const clientLoggedIn = <?= $clientLoggedIn ? 'true' : 'false' ?>;
let isFavorite = <?= $isFavorite ? 'true' : 'false' ?>;

function changeImage(src, thumb) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('[onclick*="changeImage"]').forEach(el => {
        el.classList.remove('border-blue-600');
        el.classList.add('border-gray-200');
    });
    thumb.classList.add('border-blue-600');
    thumb.classList.remove('border-gray-200');
}

function toggleAllInfo() {
    const section = document.getElementById('allInfoSection');
    const btn = document.getElementById('toggleInfoBtn');
    const icon = document.getElementById('toggleIcon');
    const text = document.getElementById('toggleText');
    
    if (section.classList.contains('hidden')) {
        section.classList.remove('hidden');
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>';
        text.textContent = 'Ocultar información';
        btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        btn.classList.add('bg-gray-600', 'hover:bg-gray-700');
    } else {
        section.classList.add('hidden');
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>';
        text.textContent = 'Mostrar toda la información';
        btn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
        btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
    }
}

function toggleFavorite(propertyId) {
    if (!clientLoggedIn) {
        if (confirm('Debes iniciar sesión para guardar favoritos. ¿Deseas iniciar sesión ahora?')) {
            window.location.href = 'cliente/login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
        }
        return;
    }
    
    fetch('api/favorites.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=toggle&property_id=' + propertyId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            isFavorite = data.is_favorite;
            updateFavoriteButton();
            } else if (data.error === 'not_authenticated') {
            window.location.href = 'cliente/login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
        }
    });
}

function updateFavoriteButton() {
    const btn = document.getElementById('favBtn');
    const icon = document.getElementById('favIcon');
    
    if (isFavorite) {
        btn.classList.add('bg-red-50', 'border-red-200');
        btn.classList.remove('bg-white', 'border-gray-200');
        icon.classList.add('text-red-500');
        icon.classList.remove('text-gray-400');
        icon.setAttribute('fill', 'currentColor');
    } else {
        btn.classList.remove('bg-red-50', 'border-red-200');
        btn.classList.add('bg-white', 'border-gray-200');
        icon.classList.remove('text-red-500');
        icon.classList.add('text-gray-400');
        icon.setAttribute('fill', 'none');
    }
}

function openShareModal() {
    document.getElementById('shareModal').classList.remove('hidden');
}

function closeShareModal() {
    document.getElementById('shareModal').classList.add('hidden');
}

function copyLink() {
    const input = document.getElementById('shareUrl');
    input.select();
    document.execCommand('copy');
    alert('Enlace copiado al portapapeles');
}

function sendWhatsApp(event) {
    event.preventDefault();
    
    const form = event.target;
    const name = form.querySelector('input[name="name"]').value;
    const email = form.querySelector('input[name="email"]').value;
    const phone = form.querySelector('input[name="phone"]').value;
    
    if (!name || !email) {
        alert('Por favor completa nombre y email');
        return false;
    }
    
    const propertyTitle = '<?= htmlspecialchars($property['title']) ?>';
    const propertyPrice = '<?= formatPrice($property['price'], $property['currency'] ?? 'CLP') ?>';
    const message = `Hola, me interesa la propiedad "${propertyTitle}" (${propertyPrice}). Mi nombre es ${name}, mi email es ${email}${phone ? ', y mi teléfono es ' + phone : ''}. Me gustaría obtener más información.`;
    
    const whatsappNumber = '<?= $whatsappNumber ?>';
    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;
    
    window.open(whatsappUrl, '_blank');
    return false;
}

function initPropertyMap() {
    const comuna = '<?= htmlspecialchars($property['comuna_name'] ?? '') ?>';
    const region = '<?= htmlspecialchars($property['region_name'] ?? '') ?>';
    const address = '<?= htmlspecialchars($property['address'] ?? '') ?>';
    
    try {
        const searchQuery = address + ', ' + comuna + ', ' + region + ', Chile';
        
        fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(searchQuery) + '&format=json&limit=1')
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    
                    const map = L.map('propertyMap', {
                        center: [lat, lon],
                        zoom: 15,
                        scrollWheelZoom: true
                    });
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                        maxZoom: 19,
                        minZoom: 1
                    }).addTo(map);
                    
                    const blueIcon = L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                    
                    L.marker([lat, lon], { icon: blueIcon })
                        .bindPopup('<b>Ubicación Aproximada</b><br/>Radio de 500 metros')
                        .addTo(map)
                        .openPopup();
                    
                    L.circle([lat, lon], {
                        color: '#0078D7',
                        fillColor: '#0078D7',
                        fillOpacity: 0.15,
                        weight: 2,
                        radius: 500
                    }).addTo(map);
                    
                    map.setView([lat, lon], 15);
                } else {
                    document.getElementById('propertyMap').innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">No se pudo ubicar en el mapa</div>';
                }
            })
            .catch(error => {
                document.getElementById('propertyMap').innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">Error al cargar el mapa</div>';
            });
    } catch(error) {
        console.error('Error al cargar el mapa:', error);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('propertyMap')) {
        initPropertyMap();
    }
});

document.getElementById('shareModal').addEventListener('click', function(e) {
    if (e.target === this) closeShareModal();
});
</script>

<?php if (!empty($similarProperties)): ?>
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Propiedades Similares</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($similarProperties as $similar): ?>
                <?php $similarPhotos = $photoModel->getByPropertyId($similar['id']); ?>
                <a href="propiedad.php?id=<?= $similar['id'] ?>" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition group">
                    <div class="relative h-48">
                        <?php if (!empty($similarPhotos)): ?>
                            <img src="<?= htmlspecialchars(getPropertyPhotoUrl($similarPhotos[0]['photo_url'], true)) ?>" 
                                 alt="<?= htmlspecialchars($similar['title']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                <svg class="w-12 h-12 text-white/50" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <span class="absolute top-3 left-3 <?= $similar['operation_type'] === 'Venta' ? 'bg-green-600' : 'bg-amber-600' ?> text-white text-xs font-bold px-2 py-1 rounded">
                            <?= htmlspecialchars($similar['operation_type']) ?>
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1 line-clamp-1"><?= htmlspecialchars($similar['title']) ?></h3>
                        <p class="text-gray-500 text-sm mb-2"><?= htmlspecialchars($similar['comuna_name'] ?? '') ?></p>
                        <div class="flex items-center gap-3 text-xs text-gray-600 mb-3">
                            <?php if (!empty($similar['bedrooms'])): ?>
                                <span><?= $similar['bedrooms'] ?> dorm.</span>
                            <?php endif; ?>
                            <?php if (!empty($similar['bathrooms'])): ?>
                                <span><?= $similar['bathrooms'] ?> baños</span>
                            <?php endif; ?>
                            <?php if (!empty($similar['built_area'])): ?>
                                <span><?= round($similar['built_area']) ?> m²</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-lg font-bold text-blue-600"><?= formatPrice($similar['price'], $similar['currency'] ?? 'CLP') ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>
