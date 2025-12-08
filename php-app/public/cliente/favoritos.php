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
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-gray-600"><?= count($favorites) ?> propiedad(es) en favoritos</p>
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
                            Solicitar Más Información (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                </div>
                <p class="text-sm text-gray-500">Selecciona las propiedades sobre las que deseas recibir más información</p>
            </div>
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
                    <div class="bg-white border rounded-xl overflow-hidden hover:shadow-lg transition group relative property-card" data-property-id="<?= $p['id'] ?>" data-property-title="<?= htmlspecialchars($p['title']) ?>">
                        <div class="absolute top-3 left-3 z-10">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="property-checkbox w-5 h-5 text-blue-600 border-2 border-white rounded shadow-lg" 
                                       value="<?= $p['id'] ?>" onchange="updateSelectedCount()">
                            </label>
                        </div>
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

<div id="ordenVisitaModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Orden de Visita</h2>
                <button onclick="closeOrdenVisitaModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">Propiedades Seleccionadas:</h4>
                <ul id="selectedPropertiesList" class="space-y-1 max-h-32 overflow-y-auto"></ul>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm text-gray-700 leading-relaxed max-h-60 overflow-y-auto">
                <h3 class="font-bold text-center mb-4">ORDEN DE VISITA INMOBILIARIA</h3>
                <p class="mb-3">
                    El suscrito, en adelante "El Cliente", mediante la presente Orden de Visita, autoriza a 
                    <strong>URBAN GROUP SpA, RUT 76.192.802-3</strong>, en adelante "El Corredor", para que realice 
                    las gestiones de intermediación conducentes a la compraventa de los inmuebles seleccionados.
                </p>
                <p class="mb-3">
                    <strong>PRIMERO:</strong> El Cliente declara que ha tomado conocimiento de las propiedades listadas 
                    en este documento a través del Portal de Terrenos Inmobiliarios de Urban Group.
                </p>
                <p class="mb-3">
                    <strong>SEGUNDO:</strong> El Cliente se compromete a pagar a El Corredor una comisión equivalente al 
                    <strong>2,0% + IVA</strong> sobre el precio de venta final de cada propiedad que efectivamente adquiera.
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
                        Urban Group SpA a enviarme información detallada de las propiedades seleccionadas.
                    </span>
                </label>
            </div>
            
            <div id="submitError" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4"></div>
            <div id="submitSuccess" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4"></div>
            
            <div class="flex gap-4">
                <button onclick="closeOrdenVisitaModal()" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button onclick="submitOrdenVisita()" id="submitBtn" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Enviar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let allSelected = false;

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
            button.closest('.property-card').remove();
            updateSelectedCount();
            if (document.querySelectorAll('.property-card').length === 0) {
                location.reload();
            }
        }
    });
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.property-checkbox:checked');
    document.getElementById('selectedCount').textContent = checkboxes.length;
    
    const allCheckboxes = document.querySelectorAll('.property-checkbox');
    allSelected = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
    document.getElementById('selectAllBtn').querySelector('span').textContent = allSelected ? 'Deseleccionar Todos' : 'Seleccionar Todos';
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.property-checkbox');
    allSelected = !allSelected;
    checkboxes.forEach(cb => cb.checked = allSelected);
    updateSelectedCount();
}

function getSelectedPropertyIds() {
    const checkboxes = document.querySelectorAll('.property-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function openOrdenVisitaModal() {
    const selected = getSelectedPropertyIds();
    if (selected.length === 0) {
        alert('Por favor, selecciona al menos una propiedad para solicitar información.');
        return;
    }
    
    const listContainer = document.getElementById('selectedPropertiesList');
    if (listContainer) {
        listContainer.innerHTML = '';
        document.querySelectorAll('.property-checkbox:checked').forEach(cb => {
            const card = cb.closest('.property-card');
            const title = card.dataset.propertyTitle;
            listContainer.innerHTML += `<li class="text-sm text-gray-700">• ${title}</li>`;
        });
    }
    
    document.getElementById('ordenVisitaModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeOrdenVisitaModal() {
    document.getElementById('ordenVisitaModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('acceptTerms').checked = false;
    document.getElementById('submitError').classList.add('hidden');
    document.getElementById('submitSuccess').classList.add('hidden');
}

function submitOrdenVisita() {
    const acceptTerms = document.getElementById('acceptTerms').checked;
    const submitBtn = document.getElementById('submitBtn');
    const errorDiv = document.getElementById('submitError');
    const successDiv = document.getElementById('submitSuccess');
    const selectedIds = getSelectedPropertyIds();
    
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    
    if (!acceptTerms) {
        errorDiv.textContent = 'Debe aceptar los términos y condiciones para continuar.';
        errorDiv.classList.remove('hidden');
        return;
    }
    
    if (selectedIds.length === 0) {
        errorDiv.textContent = 'Debe seleccionar al menos una propiedad.';
        errorDiv.classList.remove('hidden');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Enviando...';
    
    fetch('api/orden_visita.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=send_orden_visita&property_ids=' + selectedIds.join(',')
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            successDiv.textContent = 'Su solicitud ha sido enviada exitosamente. Recibirá un correo con la información.';
            successDiv.classList.remove('hidden');
            setTimeout(() => {
                closeOrdenVisitaModal();
            }, 3000);
        } else {
            errorDiv.textContent = data.error || 'Error al enviar la solicitud. Intente nuevamente.';
            errorDiv.classList.remove('hidden');
        }
    })
    .catch(err => {
        errorDiv.textContent = 'Error de conexión. Intente nuevamente.';
        errorDiv.classList.remove('hidden');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Enviar Solicitud';
    });
}

document.addEventListener('DOMContentLoaded', updateSelectedCount);
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
