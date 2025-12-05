<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/PropertyModel.php';
require_once __DIR__ . '/../includes/PhotoModel.php';
require_once __DIR__ . '/../includes/FavoriteModel.php';

if (!isset($_SESSION['portal_client'])) {
    header('Location: portal_login.php?section=terrenos');
    exit;
}

$db = Database::getInstance()->getConnection();
$propertyModel = new PropertyModel();
$photoModel = new PhotoModel();
$favoriteModel = new FavoriteModel();

$id = (int)($_GET['id'] ?? 0);
$property = $propertyModel->getById($id);

if (!$property || $property['section_type'] !== 'terrenos') {
    header('Location: terrenos.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM property_terreno_details WHERE property_id = ?");
$stmt->execute([$id]);
$terrenoDetails = $stmt->fetch();

$propertyPhotos = $photoModel->getByPropertyId($id);
$images = !empty($propertyPhotos) ? array_map(function($p) { 
    return getPropertyPhotoUrl($p['photo_url'], true);
}, $propertyPhotos) : [];

if (empty($images)) {
    $images = ['https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800'];
}

$clientLoggedIn = isset($_SESSION['portal_client_id']);
$isFavorite = $clientLoggedIn ? $favoriteModel->isFavorite($_SESSION['portal_client_id'], $id) : false;

$whatsappNumber = '542914125043';
$propertyUrl = SITE_URL . '/propiedad_terreno.php?id=' . $id;

$hasAnteproyecto = !empty($terrenoDetails['has_anteproyecto']);

$pageTitle = $property['title'] . ' | Terrenos Inmobiliarios';
$currentPage = 'terrenos';

include __DIR__ . '/../templates/header.php';
?>

<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm">
                <a href="/" class="text-blue-600 hover:text-blue-700">Inicio</a>
                <span class="text-gray-400">/</span>
                <a href="/terrenos.php" class="text-blue-600 hover:text-blue-700">Terrenos Inmobiliarios</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600"><?= htmlspecialchars(truncateText($property['title'], 50)) ?></span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="toggleFavorite(<?= $id ?>)" id="favBtn" class="p-2 rounded-full border <?= $isFavorite ? 'bg-red-50 border-red-200' : 'bg-white border-gray-200' ?> hover:bg-red-50 transition">
                    <svg class="w-5 h-5 <?= $isFavorite ? 'text-red-500' : 'text-gray-400' ?>" id="favIcon" fill="<?= $isFavorite ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
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
                        <img src="<?= $images[0] ?>" alt="<?= htmlspecialchars($property['title']) ?>" id="mainImage" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 flex gap-2">
                            <?php if ($hasAnteproyecto): ?>
                                <span class="bg-green-500 text-white text-sm font-bold px-3 py-1 rounded-lg">CON ANTEPROYECTO</span>
                            <?php else: ?>
                                <span class="bg-blue-500 text-white text-sm font-bold px-3 py-1 rounded-lg">SIN ANTEPROYECTO</span>
                            <?php endif; ?>
                        </div>
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

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Datos del Terreno</h2>
                    <div class="bg-green-50 rounded-xl p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php if (!empty($terrenoDetails['superficie_terreno']) || !empty($property['total_area'])): ?>
                                <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                    <div class="text-2xl mb-1">📐</div>
                                    <p class="text-xl font-bold text-gray-900"><?= number_format($terrenoDetails['superficie_terreno'] ?? $property['total_area'], 0, ',', '.') ?></p>
                                    <p class="text-xs text-gray-600">m² Superficie</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['roles'])): ?>
                                <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                    <div class="text-2xl mb-1">📋</div>
                                    <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($terrenoDetails['roles']) ?></p>
                                    <p class="text-xs text-gray-600">Rol(es)</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['zonificacion'])): ?>
                                <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                    <div class="text-2xl mb-1">🗺️</div>
                                    <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($terrenoDetails['zonificacion']) ?></p>
                                    <p class="text-xs text-gray-600">Zonificación</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['altura_maxima'])): ?>
                                <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                    <div class="text-2xl mb-1">🏢</div>
                                    <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($terrenoDetails['altura_maxima']) ?></p>
                                    <p class="text-xs text-gray-600">Altura Máx</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($terrenoDetails)): ?>
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Normativa Urbanística</h2>
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php if (!empty($terrenoDetails['usos_permitidos'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Usos Permitidos</p>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($terrenoDetails['usos_permitidos']) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['densidad_maxima'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Densidad Máxima</p>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($terrenoDetails['densidad_maxima']) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['coef_constructibilidad_max'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Coef. Constructibilidad Máx</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['coef_constructibilidad_max'], 2) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['coef_ocupacion_suelo_max'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Coef. Ocupación Suelo Máx</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['coef_ocupacion_suelo_max'], 2) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['frente_minimo'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Frente Mínimo</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['frente_minimo'], 1) ?> m</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['fondo_minimo'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Fondo Mínimo</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['fondo_minimo'], 1) ?> m</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['antejardin_min'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Antejardín Mínimo</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['antejardin_min'], 1) ?> m</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['distanciamientos'])): ?>
                            <div class="p-3 bg-white rounded-lg md:col-span-2">
                                <p class="text-sm text-gray-500">Distanciamientos</p>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($terrenoDetails['distanciamientos']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Factibilidades</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="p-4 rounded-lg <?= !empty($terrenoDetails['factibilidad_electrica']) ? 'bg-green-100' : 'bg-gray-100' ?> text-center">
                            <div class="text-2xl mb-2"><?= !empty($terrenoDetails['factibilidad_electrica']) ? '✅' : '❌' ?></div>
                            <p class="text-sm font-medium">Factibilidad Eléctrica</p>
                        </div>
                        
                        <div class="p-4 rounded-lg <?= !empty($terrenoDetails['factibilidad_agua']) ? 'bg-green-100' : 'bg-gray-100' ?> text-center">
                            <div class="text-2xl mb-2"><?= !empty($terrenoDetails['factibilidad_agua']) ? '✅' : '❌' ?></div>
                            <p class="text-sm font-medium">Factibilidad Agua</p>
                        </div>
                        
                        <div class="p-4 rounded-lg <?= !empty($terrenoDetails['factibilidad_alcantarillado']) ? 'bg-green-100' : 'bg-gray-100' ?> text-center">
                            <div class="text-2xl mb-2"><?= !empty($terrenoDetails['factibilidad_alcantarillado']) ? '✅' : '❌' ?></div>
                            <p class="text-sm font-medium">Factibilidad Alcantarillado</p>
                        </div>
                        
                        <div class="p-4 rounded-lg <?= !empty($terrenoDetails['factibilidad_gas']) ? 'bg-green-100' : 'bg-gray-100' ?> text-center">
                            <div class="text-2xl mb-2"><?= !empty($terrenoDetails['factibilidad_gas']) ? '✅' : '❌' ?></div>
                            <p class="text-sm font-medium">Factibilidad Gas</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Características del Terreno</h2>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="p-4 rounded-lg <?= !empty($terrenoDetails['es_esquinero']) ? 'bg-blue-100' : 'bg-gray-100' ?> text-center">
                            <div class="text-2xl mb-2"><?= !empty($terrenoDetails['es_esquinero']) ? '✅' : '❌' ?></div>
                            <p class="text-sm font-medium">Es Esquinero</p>
                        </div>
                        
                        <div class="p-4 rounded-lg <?= !empty($terrenoDetails['topografia_regular']) ? 'bg-blue-100' : 'bg-gray-100' ?> text-center">
                            <div class="text-2xl mb-2"><?= !empty($terrenoDetails['topografia_regular']) ? '✅' : '❌' ?></div>
                            <p class="text-sm font-medium">Topografía Regular</p>
                        </div>
                        
                        <div class="p-4 rounded-lg <?= !empty($terrenoDetails['urbanizado']) ? 'bg-blue-100' : 'bg-gray-100' ?> text-center">
                            <div class="text-2xl mb-2"><?= !empty($terrenoDetails['urbanizado']) ? '✅' : '❌' ?></div>
                            <p class="text-sm font-medium">Urbanizado</p>
                        </div>
                        
                        <div class="p-4 rounded-lg <?= !empty($terrenoDetails['cerrado_perimetralmente']) ? 'bg-blue-100' : 'bg-gray-100' ?> text-center">
                            <div class="text-2xl mb-2"><?= !empty($terrenoDetails['cerrado_perimetralmente']) ? '✅' : '❌' ?></div>
                            <p class="text-sm font-medium">Cerrado Perimetralmente</p>
                        </div>
                        
                        <div class="p-4 rounded-lg <?= !empty($terrenoDetails['con_proyecto_preliminar']) ? 'bg-blue-100' : 'bg-gray-100' ?> text-center">
                            <div class="text-2xl mb-2"><?= !empty($terrenoDetails['con_proyecto_preliminar']) ? '✅' : '❌' ?></div>
                            <p class="text-sm font-medium">Con Proyecto Preliminar</p>
                        </div>
                    </div>
                </div>
                
                <?php if ($hasAnteproyecto): ?>
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="bg-green-500 text-white px-3 py-1 rounded-lg text-sm">CON ANTEPROYECTO</span>
                        Datos del Anteproyecto
                    </h2>
                    <div class="bg-green-50 rounded-xl p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php if (!empty($terrenoDetails['fecha_permiso_edificacion'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Fecha Permiso Edificación</p>
                                <p class="font-medium text-gray-900"><?= date('d/m/Y', strtotime($terrenoDetails['fecha_permiso_edificacion'])) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['zona_prc_edificacion'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Zona PRC Edificación</p>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($terrenoDetails['zona_prc_edificacion']) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['sistema_agrupamiento'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Sistema Agrupamiento</p>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($terrenoDetails['sistema_agrupamiento']) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['rasante'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Rasante</p>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($terrenoDetails['rasante']) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['num_viviendas'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">N° Viviendas</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['num_viviendas'], 0) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['num_estacionamientos'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">N° Estacionamientos</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['num_estacionamientos'], 0) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['num_est_bicicletas'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Est. Bicicletas</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['num_est_bicicletas'], 0) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['num_locales_comerciales'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Locales Comerciales</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['num_locales_comerciales'], 0) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['num_bodegas'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">N° Bodegas</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['num_bodegas'], 0) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="font-semibold text-gray-900 mt-6 mb-3">Superficies del Proyecto</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php if (!empty($terrenoDetails['superficie_util'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Superficie Útil</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['superficie_util'], 2) ?> m²</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['superficie_comun'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Superficie Común</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['superficie_comun'], 2) ?> m²</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['superficie_total'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Superficie Total</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['superficie_total'], 2) ?> m²</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['edificada_sobre_terreno'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Edificada Sobre Terreno</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['edificada_sobre_terreno'], 2) ?> m²</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['edificada_bajo_terreno'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Edificada Bajo Terreno</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['edificada_bajo_terreno'], 2) ?> m²</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['edificada_total'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Edificada Total</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['edificada_total'], 2) ?> m²</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($terrenoDetails['precio_uf_m2']) || !empty($terrenoDetails['precio_total_uf'])): ?>
                        <h3 class="font-semibold text-gray-900 mt-6 mb-3">Valorización</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php if (!empty($terrenoDetails['precio_uf_m2'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Precio UF/m²</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['precio_uf_m2'], 2) ?> UF/m²</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['precio_total_uf'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Precio Total UF</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['precio_total_uf'], 2) ?> UF</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($terrenoDetails['comision_porcentaje'])): ?>
                            <div class="p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">Comisión</p>
                                <p class="font-medium text-gray-900"><?= number_format($terrenoDetails['comision_porcentaje'], 1) ?>%</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 sticky top-24">
                    <div class="flex gap-2 mb-4">
                        <?php if ($hasAnteproyecto): ?>
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-lg">CON ANTEPROYECTO</span>
                        <?php else: ?>
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg">SIN ANTEPROYECTO</span>
                        <?php endif; ?>
                    </div>

                    <h1 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($property['title']) ?></h1>
                    
                    <div class="text-sm text-gray-600 mb-4">
                        <?= htmlspecialchars($property['property_type']) ?>
                    </div>
                    
                    <?php if (!empty($property['address'])): ?>
                    <div class="flex items-start gap-2 text-gray-600 mb-6 pb-6 border-b border-gray-200">
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <div>
                            <p><?= htmlspecialchars($property['address']) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-gray-600 text-sm mb-1">Precio</p>
                        <p class="text-3xl font-bold text-green-600">
                            $<?= number_format($property['price'], 0, ',', '.') ?>
                            <span class="text-lg text-gray-600"><?= $property['currency'] ?? 'CLP' ?></span>
                        </p>
                    </div>
                    
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-gray-600 text-sm mb-1">Superficie</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= number_format($terrenoDetails['superficie_terreno'] ?? $property['total_area'] ?? 0, 0, ',', '.') ?> m²
                        </p>
                    </div>

                    <div class="mb-6 pb-6 border-b border-gray-200 space-y-3">
                        <a href="https://wa.me/<?= $whatsappNumber ?>?text=<?= urlencode('Hola, me interesa el terreno "' . $property['title'] . '" ($' . number_format($property['price'], 0, ',', '.') . ' ' . ($property['currency'] ?? 'CLP') . ') que vi en UrbanPropiedades Terrenos: ' . $propertyUrl) ?>" target="_blank" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Contactar por WhatsApp
                        </a>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">¿Interesado en este terreno?</h3>
                        <form method="POST" action="api/contact.php" class="space-y-3" onsubmit="return sendWhatsApp(event)">
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

<script>
function changeImage(src, element) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.aspect-square').forEach(el => {
        el.classList.remove('border-blue-600');
        el.classList.add('border-gray-200');
    });
    element.classList.remove('border-gray-200');
    element.classList.add('border-blue-600');
}

function toggleFavorite(propertyId) {
    fetch('api/favorites.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=toggle&property_id=' + propertyId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById('favBtn');
            const icon = document.getElementById('favIcon');
            if (data.is_favorite) {
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
    });
}

function sendWhatsApp(event) {
    event.preventDefault();
    const form = event.target;
    const name = form.querySelector('[name="name"]').value;
    const email = form.querySelector('[name="email"]').value;
    const phone = form.querySelector('[name="phone"]').value;
    const title = form.querySelector('[name="property_title"]').value;
    
    const message = `Hola, soy ${name}.\n\nMe interesa el terreno "${title}" que vi en UrbanPropiedades.\n\nEmail: ${email}${phone ? '\nTeléfono: ' + phone : ''}\n\n¿Podrían darme más información?`;
    
    window.open(`https://wa.me/<?= $whatsappNumber ?>?text=${encodeURIComponent(message)}`, '_blank');
    return false;
}
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
