<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/PropertyModel.php';

$propertyModel = new PropertyModel();

$id = (int)($_GET['id'] ?? 0);
$property = $propertyModel->getById($id);

if (!$property) {
    header('Location: /propiedades.php');
    exit;
}

$images = getImages($property['images']);
$features = getFeatures($property['features'] ?? '[]');

$pageTitle = $property['title'];
$currentPage = 'properties';

include __DIR__ . '/../templates/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4">
        <div class="flex items-center gap-2 text-sm">
            <a href="/" class="text-blue-600 hover:text-blue-700">Inicio</a>
            <span class="text-gray-400">/</span>
            <a href="/propiedades.php" class="text-blue-600 hover:text-blue-700">Propiedades</a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-600"><?= htmlspecialchars(truncateText($property['title'], 50)) ?></span>
        </div>
    </div>
</div>

<section class="py-8 lg:py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Gallery -->
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

                <!-- Description -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Descripción</h2>
                    <p class="text-gray-600 leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($property['description']) ?></p>
                </div>

                <!-- Features -->
                <?php if (!empty($features)): ?>
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Características</h2>
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

                <!-- Google Maps -->
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

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Property Info Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 sticky top-24">
                    <!-- Badges -->
                    <div class="flex gap-2 mb-4">
                        <span class="inline-block px-3 py-1 <?= $property['operation_type'] === 'Venta' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?> text-xs font-semibold rounded-lg">
                            <?= $property['operation_type'] ?>
                        </span>
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg">
                            <?= ucfirst($property['property_type']) ?>
                        </span>
                    </div>

                    <!-- Title & Location -->
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

                    <!-- Price -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-gray-600 text-sm mb-1">Precio</p>
                        <p class="text-3xl font-bold text-gray-900">
                            <?= formatPrice($property['price'], $property['currency'] ?? 'CLP') ?>
                            <?php if ($property['operation_type'] === 'Arriendo'): ?>
                                <span class="text-lg text-gray-600">/mes</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-gray-200">
                        <?php if (!empty($property['bedrooms']) && $property['bedrooms'] > 0): ?>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900"><?= $property['bedrooms'] ?></p>
                                <p class="text-xs text-gray-600">Dormitorios</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['bathrooms']) && $property['bathrooms'] > 0): ?>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900"><?= $property['bathrooms'] ?></p>
                                <p class="text-xs text-gray-600">Baños</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['built_area']) && $property['built_area'] > 0): ?>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900"><?= round($property['built_area']) ?></p>
                                <p class="text-xs text-gray-600">m² Construidos</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['parking_spots']) && $property['parking_spots'] > 0): ?>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900"><?= $property['parking_spots'] ?></p>
                                <p class="text-xs text-gray-600">Estacionamientos</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Download PDF Button -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <a href="/api/download-property-pdf.php?id=<?= $property['id'] ?>" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8m0 8l-6-4m6 4l6-4"/>
                            </svg>
                            Descargar PDF
                        </a>
                    </div>

                    <!-- Contact Form -->
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
function changeImage(src, thumb) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('[onclick*="changeImage"]').forEach(el => {
        el.classList.remove('border-blue-600');
        el.classList.add('border-gray-200');
    });
    thumb.classList.add('border-blue-600');
    thumb.classList.remove('border-gray-200');
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
    const message = `Hola, me interesa la propiedad "${propertyTitle}". Mi nombre es ${name}, mi email es ${email}${phone ? ', y mi teléfono es ' + phone : ''}. Me gustaría obtener más información.`;
    
    const whatsappNumber = '<?= WHATSAPP_NUMBER ?>';
    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/${whatsappNumber.replace(/\D/g, '')}?text=${encodedMessage}`;
    
    window.open(whatsappUrl, '_blank');
    return false;
}

// Leaflet Map - Ubicación Aproximada
function initPropertyMap() {
    const comuna = '<?= htmlspecialchars($property['comuna_name'] ?? '') ?>';
    const region = '<?= htmlspecialchars($property['region_name'] ?? '') ?>';
    const address = '<?= htmlspecialchars($property['address'] ?? '') ?>';
    
    try {
        // Geocodificar usando Nominatim (OpenStreetMap)
        const searchQuery = address + ', ' + comuna + ', ' + region + ', Chile';
        
        fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(searchQuery) + '&format=json&limit=1')
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    
                    // Crear mapa
                    const map = L.map('propertyMap', {
                        center: [lat, lon],
                        zoom: 15,
                        scrollWheelZoom: true
                    });
                    
                    // Tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                        maxZoom: 19,
                        minZoom: 1
                    }).addTo(map);
                    
                    // Icono azul
                    const blueIcon = L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                    
                    // Marcador
                    L.marker([lat, lon], { icon: blueIcon })
                        .bindPopup('<b>Ubicación Aproximada</b><br/>Radio de 500 metros')
                        .addTo(map)
                        .openPopup();
                    
                    // Círculo de 500 metros
                    L.circle([lat, lon], {
                        color: '#0078D7',
                        fillColor: '#0078D7',
                        fillOpacity: 0.15,
                        weight: 2,
                        radius: 500
                    }).addTo(map);
                    
                    // Centrar vista
                    map.setView([lat, lon], 15);
                } else {
                    console.log('No se encontraron coordenadas para:', searchQuery);
                    document.getElementById('propertyMap').innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">No se pudo ubicar en el mapa</div>';
                }
            })
            .catch(error => {
                console.error('Error geocodificando:', error);
                document.getElementById('propertyMap').innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">Error al cargar el mapa</div>';
            });
    } catch(error) {
        console.error('Error al cargar el mapa:', error);
    }
}

// Inicializar mapa cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('propertyMap')) {
        initPropertyMap();
    }
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
