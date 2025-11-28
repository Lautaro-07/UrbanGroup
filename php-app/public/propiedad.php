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
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
