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
    'zona_prc_edificacion' => $_GET['zona_prc_edificacion'] ?? '',
    'usos_suelo' => $_GET['usos_suelo'] ?? '',
    'min_densidad_bruta_hab' => $_GET['min_densidad_bruta_hab'] ?? '',
    'max_densidad_bruta_hab' => $_GET['max_densidad_bruta_hab'] ?? '',
    'min_densidad_neta_hab' => $_GET['min_densidad_neta_hab'] ?? '',
    'max_densidad_neta_hab' => $_GET['max_densidad_neta_hab'] ?? '',
    'min_superficie_util' => $_GET['min_superficie_util'] ?? '',
    'max_superficie_util' => $_GET['max_superficie_util'] ?? '',
    'min_precio_uf_m2' => $_GET['min_precio_uf_m2'] ?? '',
    'max_precio_uf_m2' => $_GET['max_precio_uf_m2'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'anteproyecto_filter' => $_GET['anteproyecto_filter'] ?? ''
];

$terrenos = $terrenoModel->getTerrenos($filters);
$regions = $locationModel->getRegions();
$zonasPRC = $terrenoModel->getDistinctZonasPRC();
$usosSuelo = $terrenoModel->getDistinctUsosSuelo();

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
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zona PRC Edificación</label>
                    <select name="zona_prc_edificacion" id="zonaPrcSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas</option>
                        <?php foreach ($zonasPRC as $zona): ?>
                            <option value="<?= htmlspecialchars($zona) ?>" <?= $filters['zona_prc_edificacion'] == $zona ? 'selected' : '' ?>>
                                <?= htmlspecialchars($zona) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zona PRC Usos de Suelo</label>
                    <select name="usos_suelo" id="usosSueloSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos</option>
                        <?php foreach ($usosSuelo as $uso): ?>
                            <option value="<?= htmlspecialchars($uso) ?>" <?= $filters['usos_suelo'] == $uso ? 'selected' : '' ?>>
                                <?= htmlspecialchars($uso) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-4 mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Filtros de Densidad</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Densidad Bruta Mín (Hab/Ha)</label>
                        <input type="number" step="0.01" name="min_densidad_bruta_hab" value="<?= htmlspecialchars($filters['min_densidad_bruta_hab']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Densidad Bruta Máx (Hab/Ha)</label>
                        <input type="number" step="0.01" name="max_densidad_bruta_hab" value="<?= htmlspecialchars($filters['max_densidad_bruta_hab']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Sin límite">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Densidad Neta Mín (Hab/Ha)</label>
                        <input type="number" step="0.01" name="min_densidad_neta_hab" value="<?= htmlspecialchars($filters['min_densidad_neta_hab']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Densidad Neta Máx (Hab/Ha)</label>
                        <input type="number" step="0.01" name="max_densidad_neta_hab" value="<?= htmlspecialchars($filters['max_densidad_neta_hab']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Sin límite">
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-4 mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Superficie y Precio</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Neta Útil Mín (m²)</label>
                        <input type="number" step="0.01" name="min_superficie_util" value="<?= htmlspecialchars($filters['min_superficie_util']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Neta Útil Máx (m²)</label>
                        <input type="number" step="0.01" name="max_superficie_util" value="<?= htmlspecialchars($filters['max_superficie_util']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Sin límite">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio Mín (UF/m²)</label>
                        <input type="number" step="0.01" name="min_precio_uf_m2" value="<?= htmlspecialchars($filters['min_precio_uf_m2']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio Máx (UF/m²)</label>
                        <input type="number" step="0.01" name="max_precio_uf_m2" value="<?= htmlspecialchars($filters['max_precio_uf_m2']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Sin límite">
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    
                    <div class="flex items-end">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Anteproyecto</label>
                        <select name="anteproyecto_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="con" <?= ($filters['anteproyecto_filter'] ?? '') === 'con' ? 'selected' : '' ?>>Con Anteproyecto</option>
                            <option value="sin" <?= ($filters['anteproyecto_filter'] ?? '') === 'sin' ? 'selected' : '' ?>>Sin Anteproyecto</option>
                        </select>
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
            <div class="mb-6 flex justify-between items-center">
                <div class="flex gap-3">
                    <button onclick="toggleSelectAll()" id="selectAllBtn"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Seleccionar Todos</span>
                    </button>
                    <button onclick="openOrdenVisitaModal()" id="solicitarBtn"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Solicitar Información (<span id="selectedCount">0</span>)
                    </button>
                </div>
                <p class="text-sm text-gray-500">Selecciona los terrenos sobre los que deseas recibir más información</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($terrenos as $terreno): ?>
                    <?php 
                    $photos = $photoModel->getByPropertyId($terreno['id']); 
                    $photoUrl = '';
                    if (!empty($photos) && !empty($photos[0]['photo_url'])) {
                        $photoUrl = getPropertyPhotoUrl($photos[0]['photo_url'], true);
                    }
                    ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition property-card" 
                         data-property-id="<?= $terreno['id'] ?>" 
                         data-property-title="<?= htmlspecialchars($terreno['title']) ?>">
                        <div class="relative h-48">
                            <div class="absolute top-3 left-3 z-10">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="property-checkbox w-5 h-5 text-blue-600 border-2 border-white rounded shadow-lg" 
                                           value="<?= $terreno['id'] ?>" onchange="updateSelectedCount()">
                                </label>
                            </div>
                            <?php if (!empty($photoUrl)): ?>
                                <img src="<?= htmlspecialchars($photoUrl) ?>" 
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
                                <span class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
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

<div id="ordenVisitaModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Solicitud de Información - Terrenos</h2>
                <button onclick="closeOrdenVisitaModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">Terrenos Seleccionados:</h4>
                <ul id="selectedPropertiesList" class="space-y-1 max-h-32 overflow-y-auto"></ul>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm text-gray-700 leading-relaxed max-h-60 overflow-y-auto">
                <h3 class="font-bold text-center mb-4">ORDEN DE VISITA INMOBILIARIA</h3>
                <p class="mb-3">
                    El suscrito, en adelante "El Cliente", mediante la presente Orden de Visita, autoriza a 
                    <strong>URBAN GROUP SpA, RUT 76.192.802-3</strong>, en adelante "El Corredor", para que realice 
                    las gestiones de intermediación conducentes a la compraventa de los terrenos seleccionados.
                </p>
                <p class="mb-3">
                    <strong>PRIMERO:</strong> El Cliente declara que ha tomado conocimiento de los terrenos listados 
                    en este documento a través del Portal de Terrenos Inmobiliarios de Urban Group.
                </p>
                <p class="mb-3">
                    <strong>SEGUNDO:</strong> El Cliente se compromete a pagar a El Corredor una comisión equivalente al 
                    <strong>2,0% + IVA</strong> sobre el precio de venta final de cada terreno que efectivamente adquiera.
                </p>
                <p class="mb-3">
                    <strong>TERCERO:</strong> El Cliente declara que la información contenida en este portal es confidencial 
                    y de carácter privado, prohibiéndose la divulgación parcial o total de su contenido.
                </p>
                <p class="mb-3">
                    <strong>CUARTO:</strong> Esta orden tendrá una vigencia de 12 meses desde la fecha de aceptación.
                </p>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" id="acceptTerms" class="mt-1 h-5 w-5 text-blue-600 border-gray-300 rounded">
                    <span class="ml-3 text-sm text-gray-700">
                        <strong>ACEPTO</strong> los términos y condiciones de esta Orden de Visita y autorizo a 
                        Urban Group SpA a enviarme información detallada de los terrenos seleccionados.
                    </span>
                </label>
            </div>
            
            <div id="submitError" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4"></div>
            <div id="submitSuccess" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4"></div>
            
            <div class="flex justify-end gap-4">
                <button onclick="closeOrdenVisitaModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button onclick="submitOrdenVisita()" id="submitOrderBtn" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                    Enviar Solicitud
                </button>
            </div>
        </div>
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

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.property-checkbox:checked');
    const countEl = document.getElementById('selectedCount');
    if (countEl) countEl.textContent = checkboxes.length;
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.property-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => cb.checked = !allChecked);
    updateSelectedCount();
    
    const btn = document.getElementById('selectAllBtn');
    if (btn) {
        const span = btn.querySelector('span');
        if (span) span.textContent = allChecked ? 'Seleccionar Todos' : 'Deseleccionar Todos';
    }
}

function openOrdenVisitaModal() {
    const checkboxes = document.querySelectorAll('.property-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor selecciona al menos un terreno.');
        return;
    }
    
    const list = document.getElementById('selectedPropertiesList');
    list.innerHTML = '';
    
    checkboxes.forEach(cb => {
        const card = cb.closest('.property-card');
        const title = card.dataset.propertyTitle;
        list.innerHTML += `<li class="text-sm text-gray-700">• ${title}</li>`;
    });
    
    document.getElementById('ordenVisitaModal').classList.remove('hidden');
    document.getElementById('acceptTerms').checked = false;
    document.getElementById('submitError').classList.add('hidden');
    document.getElementById('submitSuccess').classList.add('hidden');
}

function closeOrdenVisitaModal() {
    document.getElementById('ordenVisitaModal').classList.add('hidden');
}

function submitOrdenVisita() {
    const acceptTerms = document.getElementById('acceptTerms');
    if (!acceptTerms.checked) {
        document.getElementById('submitError').textContent = 'Debe aceptar los términos y condiciones.';
        document.getElementById('submitError').classList.remove('hidden');
        return;
    }
    
    const checkboxes = document.querySelectorAll('.property-checkbox:checked');
    const propertyIds = Array.from(checkboxes).map(cb => cb.value);
    
    const submitBtn = document.getElementById('submitOrderBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Enviando...';
    
    fetch('cliente/api/orden_visita.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            property_ids: propertyIds,
            section_type: 'terrenos'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('submitSuccess').textContent = 'Solicitud enviada correctamente. Nos pondremos en contacto con usted pronto.';
            document.getElementById('submitSuccess').classList.remove('hidden');
            document.getElementById('submitError').classList.add('hidden');
            
            setTimeout(() => {
                closeOrdenVisitaModal();
                checkboxes.forEach(cb => cb.checked = false);
                updateSelectedCount();
            }, 2000);
        } else {
            document.getElementById('submitError').textContent = data.error || 'Error al enviar la solicitud.';
            document.getElementById('submitError').classList.remove('hidden');
        }
    })
    .catch(error => {
        document.getElementById('submitError').textContent = 'Error de conexión. Intente nuevamente.';
        document.getElementById('submitError').classList.remove('hidden');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Enviar Solicitud';
    });
}
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
