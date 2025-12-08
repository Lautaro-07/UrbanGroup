<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/PropertyModel.php';
require_once __DIR__ . '/../includes/LocationModel.php';
require_once __DIR__ . '/../includes/PhotoModel.php';
require_once __DIR__ . '/../includes/PropertyTypeModel.php';
require_once __DIR__ . '/../includes/CarouselModel.php';

$propertyModel = new PropertyModel();
$locationModel = new LocationModel();
$propertyTypeModel = new PropertyTypeModel();
$photoModel = new PhotoModel();
$carouselModel = new CarouselModel();

$featuredProperties = $propertyModel->getFeatured(8);
$regions = $locationModel->getRegions();
$propertyTypes = $propertyTypeModel->getAll();
$carouselImages = $carouselModel->getActive();

$pageTitle = 'Inicio';
$currentPage = 'home';

include __DIR__ . '/../templates/header.php';
?>

<!-- Hero Section with Carousel -->
<section class="relative h-[500px] md:h-[600px] lg:h-[700px] flex items-center justify-center overflow-hidden">
    <?php if (!empty($carouselImages)): ?>
    <div id="heroCarousel" class="absolute inset-0">
        <?php foreach ($carouselImages as $index => $image): ?>
            <div class="carousel-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 <?= $index === 0 ? 'opacity-100' : 'opacity-0' ?>" 
                 style="background-image: url('<?= BASE_URL . htmlspecialchars($image['file_path']) ?>');">
                <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70"></div>
            </div>
        <?php endforeach; ?>
        
        <?php if (count($carouselImages) > 1): ?>
        <div class="absolute bottom-24 left-1/2 transform -translate-x-1/2 flex gap-2 z-20">
            <?php foreach ($carouselImages as $index => $image): ?>
                <button onclick="goToSlide(<?= $index ?>)" 
                        class="carousel-dot w-3 h-3 rounded-full transition <?= $index === 0 ? 'bg-white' : 'bg-white/50' ?>"></button>
            <?php endforeach; ?>
        </div>
        
        <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white p-2 rounded-full z-20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white p-2 rounded-full z-20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1920&h=1080&fit=crop');">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70"></div>
    </div>
    <?php endif; ?>
    
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 lg:px-8 text-center">
        <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight">
            Encuentra tu propiedad ideal<br/>
            <span class="text-blue-400">en Chile</span>
        </h1>
        <p class="text-base md:text-lg lg:text-xl text-white/80 mb-8 max-w-2xl mx-auto px-4">
            Más de 15 años de experiencia transformando el corretaje de propiedades en un servicio profesional.
        </p>
        
        <!-- Tab Buttons -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-2xl max-w-6xl mx-auto overflow-hidden">
            <div class="flex border-b border-gray-200">
                <button type="button" id="tabBuscar" onclick="switchTab('buscar')" class="flex-1 px-6 py-4 text-sm md:text-base font-semibold transition flex items-center justify-center gap-2 bg-blue-600 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35"></path>
                    </svg>
                    Buscar Propiedad
                </button>
                <button type="button" id="tabVender" onclick="switchTab('vender')" class="flex-1 px-6 py-4 text-sm md:text-base font-semibold transition flex items-center justify-center gap-2 bg-gray-100 text-gray-700 hover:bg-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Vender / Arrendar mi Propiedad
                </button>
            </div>

            <!-- Search Form Panel -->
            <div id="panelBuscar" class="p-4 md:p-6 lg:p-8">
                <form action="<?= BASE_URL ?>propiedades.php" method="GET">
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
                                <?php foreach ($propertyTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
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

            <!-- Sell/Rent Form Panel -->
            <div id="panelVender" class="p-4 md:p-6 lg:p-8 hidden">
                <div class="text-center mb-6">
                    <h3 class="text-lg md:text-xl font-bold text-gray-900">¿Quieres vender o arrendar tu propiedad?</h3>
                    <p class="text-sm text-gray-600 mt-1">Completa los datos y te contactaremos por WhatsApp</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="space-y-2 text-left">
                        <label class="text-xs md:text-sm font-medium text-slate-700">¿Qué deseas hacer?</label>
                        <select id="sellOperationType" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                            <option value="">Seleccionar</option>
                            <option value="Vender">Vender</option>
                            <option value="Arrendar">Arrendar</option>
                        </select>
                    </div>
                    
                    <div class="space-y-2 text-left">
                        <label class="text-xs md:text-sm font-medium text-slate-700">Tipo de Propiedad</label>
                        <select id="sellPropertyType" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($propertyTypes as $type): ?>
                                <option value="<?= htmlspecialchars($type['name']) ?>"><?= htmlspecialchars($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="space-y-2 text-left">
                        <label class="text-xs md:text-sm font-medium text-slate-700">Dirección / Ubicación</label>
                        <input type="text" id="sellAddress" placeholder="Ej: Av. Providencia 1234, Santiago" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    </div>
                    
                    <div class="space-y-2 text-left">
                        <label class="text-xs md:text-sm font-medium text-slate-700">Metros Cuadrados</label>
                        <input type="number" id="sellSquareMeters" placeholder="Ej: 120" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    </div>
                </div>
                
                <div class="mt-6 text-center">
                    <button type="button" onclick="contactWhatsApp()" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition shadow-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Contactar por WhatsApp
                    </button>
                    <p class="text-xs text-gray-500 mt-2">Te responderemos a la brevedad</p>
                </div>
            </div>
        </div>
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
            <a href="<?= BASE_URL ?>propiedades.php" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition whitespace-nowrap">Ver todas</a>
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
                    <?php
                        $photos = $photoModel->getByPropertyId($property['id']);
                        $photo  = !empty($photos)
                            ? str_replace('../','',$photos[0]['photo_url'])
                            : getFirstImage($property['images']);
                    ?>
                    <a href="<?= BASE_URL ?>propiedad.php?id=<?= $property['id'] ?>" class="group">
                        <div class="hover-elevate bg-white border border-gray-200/50 rounded-xl overflow-hidden">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="<?= BASE_URL . $photo ?>" alt="<?= htmlspecialchars($property['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
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
const WHATSAPP_NUMBER = '<?= defined("WHATSAPP_NUMBER") ? preg_replace("/[^0-9]/", "", WHATSAPP_NUMBER) : "2914125043" ?>';

function switchTab(tab) {
    const tabBuscar = document.getElementById('tabBuscar');
    const tabVender = document.getElementById('tabVender');
    const panelBuscar = document.getElementById('panelBuscar');
    const panelVender = document.getElementById('panelVender');
    
    if (tab === 'buscar') {
        tabBuscar.className = 'flex-1 px-6 py-4 text-sm md:text-base font-semibold transition flex items-center justify-center gap-2 bg-blue-600 text-white';
        tabVender.className = 'flex-1 px-6 py-4 text-sm md:text-base font-semibold transition flex items-center justify-center gap-2 bg-gray-100 text-gray-700 hover:bg-gray-200';
        panelBuscar.classList.remove('hidden');
        panelVender.classList.add('hidden');
    } else {
        tabVender.className = 'flex-1 px-6 py-4 text-sm md:text-base font-semibold transition flex items-center justify-center gap-2 bg-green-600 text-white';
        tabBuscar.className = 'flex-1 px-6 py-4 text-sm md:text-base font-semibold transition flex items-center justify-center gap-2 bg-gray-100 text-gray-700 hover:bg-gray-200';
        panelVender.classList.remove('hidden');
        panelBuscar.classList.add('hidden');
    }
}

function contactWhatsApp() {
    const operationType = document.getElementById('sellOperationType').value;
    const propertyType = document.getElementById('sellPropertyType').value;
    const address = document.getElementById('sellAddress').value;
    const squareMeters = document.getElementById('sellSquareMeters').value;
    
    if (!operationType || !propertyType || !address) {
        alert('Por favor completa al menos: tipo de operación, tipo de propiedad y dirección.');
        return;
    }
    
    let message = `Hola, me interesa *${operationType}* mi propiedad.\n\n`;
    message += `*Tipo de Propiedad:* ${propertyType}\n`;
    message += `*Dirección:* ${address}\n`;
    if (squareMeters) {
        message += `*Metros Cuadrados:* ${squareMeters} m²\n`;
    }
    message += `\nQuedo atento a su respuesta.`;
    
    const whatsappUrl = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
}

document.getElementById('regionSelect').addEventListener('change', function() {
    const regionId = this.value;
    const comunaSelect = document.getElementById('comunaSelect');
    
    if (!regionId) {
        comunaSelect.innerHTML = '<option value="">Seleccionar</option>';
        return;
    }
    
    fetch('<?= BASE_URL ?>api/comunas.php?region_id=' + regionId)
        .then(r => r.json())
        .then(comunas => {
            comunaSelect.innerHTML = '<option value="">Seleccionar</option>';
            comunas.forEach(c => {
                comunaSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
            });
        });
});

<?php if (!empty($carouselImages) && count($carouselImages) > 1): ?>
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const dots = document.querySelectorAll('.carousel-dot');
const totalSlides = slides.length;
let slideInterval;

function goToSlide(index) {
    slides[currentSlide].classList.remove('opacity-100');
    slides[currentSlide].classList.add('opacity-0');
    dots[currentSlide].classList.remove('bg-white');
    dots[currentSlide].classList.add('bg-white/50');
    
    currentSlide = index;
    
    slides[currentSlide].classList.remove('opacity-0');
    slides[currentSlide].classList.add('opacity-100');
    dots[currentSlide].classList.remove('bg-white/50');
    dots[currentSlide].classList.add('bg-white');
    
    resetInterval();
}

function nextSlide() {
    goToSlide((currentSlide + 1) % totalSlides);
}

function prevSlide() {
    goToSlide((currentSlide - 1 + totalSlides) % totalSlides);
}

function resetInterval() {
    clearInterval(slideInterval);
    slideInterval = setInterval(nextSlide, 5000);
}

slideInterval = setInterval(nextSlide, 5000);
<?php endif; ?>
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
