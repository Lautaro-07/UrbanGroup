<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/PropertyModel.php';
require_once __DIR__ . '/../includes/LocationModel.php';

$propertyModel = new PropertyModel();
$locationModel = new LocationModel();

$featuredProperties = $propertyModel->getFeatured(8);
$regions = $locationModel->getRegions();


$pageTitle = 'Inicio';
$currentPage = 'home';

include __DIR__ . '/../templates/header.php';
?>

<!-- Hero Section -->
<section class="relative h-[500px] md:h-[600px] lg:h-[700px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1920&h=1080&fit=crop');">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70"></div>
    </div>
    
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 lg:px-8 text-center">
        <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight">
            Encuentra tu propiedad ideal<br/>
            <span class="text-blue-400">en Chile</span>
        </h1>
        <p class="text-base md:text-lg lg:text-xl text-white/80 mb-8 max-w-2xl mx-auto px-4">
            Más de 15 años de experiencia transformando el corretaje de propiedades en un servicio profesional.
        </p>
        
        <!-- Search Form -->
        <form action="propiedades.php" method="GET" class="bg-white/95 backdrop-blur-sm rounded-xl shadow-2xl p-4 md:p-6 lg:p-8 max-w-6xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-4">
                <div class="space-y-2 text-left">
                    <label class="text-xs md:text-sm font-medium text-slate-700">Tipo de Operación</label>
                    <select name="operation_type" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Seleccionar</option>
                        <option value="Venta">Venta</option>
                        <option value="Arriendo">Arriendo</option>
                    </select>
                </div>
                
                <div class="space-y-2 text-left">
                    <label class="text-xs md:text-sm font-medium text-slate-700">Tipo de Propiedad</label>
                    <select name="property_type" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Seleccionar</option>
                        <?php foreach (PROPERTY_TYPES as $key => $value): ?>
                            <option value="<?= $key ?>"><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="space-y-2 text-left">
                    <label class="text-xs md:text-sm font-medium text-slate-700">Región</label>
                    <select name="region_id" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" id="regionSelect">
                        <option value="">Seleccionar</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?= $region['id'] ?>"><?= htmlspecialchars($region['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2 text-left">
                    <label class="text-xs md:text-sm font-medium text-slate-700">Comuna</label>
                    <select name="comuna_id" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" id="comunaSelect">
                        <option value="">Seleccionar</option>
                    </select>
                </div>
                
                <div class="space-y-2 text-left lg:col-span-1 col-span-1 sm:col-span-2 lg:col-span-1">
                    <button type="submit" style="height: 50px; position: relative; top: 20px; left: 8px;" class="w-full h-full bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <span class="hidden md:inline">Buscar</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Featured Section -->
<section class="py-12 md:py-16 lg:py-20 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-10">
            <div>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Propiedades Destacadas</h2>
                <p class="text-gray-600 text-sm md:text-base">Descubre las mejores oportunidades inmobiliarias seleccionadas para ti</p>
            </div>
            <a href="propiedades.php" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition whitespace-nowrap">Ver todas</a>
        </div>
        
        <?php if (empty($featuredProperties)): ?>
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="text-xl font-semibold mb-2">No hay propiedades destacadas</h3>
                <p class="text-gray-600 mb-4">Pronto agregaremos propiedades destacadas para ti</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <?php foreach ($featuredProperties as $property): ?>
                    <a href="propiedad.php?id=<?= $property['id'] ?>" class="group">
                        <div class="hover-elevate bg-white border border-gray-200/50 rounded-xl overflow-hidden">
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
                            <div class="p-4 space-y-3">
                                <h3 class="font-semibold text-gray-900 line-clamp-1 group-hover:text-blue-600 transition-colors text-sm md:text-base">
                                    <?= htmlspecialchars(truncateText($property['title'], 50)) ?>
                                </h3>
                                <p class="text-xs text-gray-600"><?= htmlspecialchars($property['comuna_name'] ?? '') ?></p>
                                <div class="flex gap-4 text-xs text-gray-600">
                                    <?php if ($property['bedrooms'] > 0): ?>
                                        <span>🛏️ <?= $property['bedrooms'] ?>hab</span>
                                    <?php endif; ?>
                                    <?php if ($property['bathrooms'] > 0): ?>
                                        <span>🚿 <?= $property['bathrooms'] ?>ba</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Stats Section -->
<section class="py-12 md:py-16 lg:py-20 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8 lg:gap-12">
            <div class="text-center">
                <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 md:w-8 h-6 md:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="text-2xl md:text-3xl lg:text-4xl font-bold mb-2">500+</div>
                <div class="text-white/80 text-sm md:text-base">Propiedades</div>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 md:w-8 h-6 md:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.684" />
                    </svg>
                </div>
                <div class="text-2xl md:text-3xl lg:text-4xl font-bold mb-2">15+</div>
                <div class="text-white/80 text-sm md:text-base">Años Experiencia</div>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 md:w-8 h-6 md:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-2xl md:text-3xl lg:text-4xl font-bold mb-2">1000+</div>
                <div class="text-white/80 text-sm md:text-base">Clientes</div>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 md:w-8 h-6 md:h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div class="text-2xl md:text-3xl lg:text-4xl font-bold mb-2">98%</div>
                <div class="text-white/80 text-sm md:text-base">Éxito</div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-12 md:py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-6">¿Quiénes Somos?</h2>
                <div class="space-y-4 text-gray-600 leading-relaxed text-sm md:text-base">
                    <p>
                        <strong class="text-gray-900">Urban Group</strong> es un equipo multidisciplinario formado por Arquitectos, Abogados y una extensa Red de Corredores de Propiedades con años de experiencia.
                    </p>
                    <p>
                        Con más de <strong class="text-gray-900">15 años</strong> en el mercado, hemos transformado el corretaje de propiedades en un servicio profesional, logrando el éxito en cada operación inmobiliaria.
                    </p>
                    <p>
                        En <strong class="text-gray-900">Urban Group</strong> nos enfocamos en el resultado final. Mediante un exhaustivo seguimiento del proceso de compraventa, atendemos los detalles de forma proactiva.
                    </p>
                </div>
            </div>
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop" alt="Sobre nosotros" class="rounded-xl shadow-lg w-full h-auto">
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('regionSelect').addEventListener('change', function() {
    const regionId = this.value;
    const comunaSelect = document.getElementById('comunaSelect');
    
    if (!regionId) {
        comunaSelect.innerHTML = '<option value="">Seleccionar</option>';
        return;
    }
    
    fetch('api/get-comunas.php?region_id=' + regionId)
        .then(r => r.json())
        .then(comunas => {
            comunaSelect.innerHTML = '<option value="">Seleccionar</option>';
            comunas.forEach(c => {
                comunaSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
            });
        });
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
