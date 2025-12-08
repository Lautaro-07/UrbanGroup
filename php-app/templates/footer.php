<!-- Footer -->
<footer class="bg-gray-900 text-gray-400 border-t border-gray-800 mt-16 lg:mt-20">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Brand -->
            <div>
                <a href="/" class="flex items-center gap-3 mb-4">
                    <img src="<?= BASE_URL ?>uploads/logo.png" alt="Urban Group" class="w-8 h-8 rounded-full shadow-sm object-cover">
                    <span class="sr-only">Urban Group</span>
                </a>
                <p class="text-sm leading-relaxed">
                    Portal inmobiliario profesional. Conectamos propietarios con compradores y arrendatarios en todo Chile.
                </p>
            </div>
            
            <!-- Navegación -->
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Navegación</h4>
                <ul class="space-y-2">
                    <li><a href="index.php" class="text-sm hover:text-white transition">Inicio</a></li>
                    <li><a href="propiedades.php" class="text-sm hover:text-white transition">Propiedades</a></li>
                    <li><a href="propiedades.php?operation_type=Venta" class="text-sm hover:text-white transition">En Venta</a></li>
                    <li><a href="propiedades.php?operation_type=Arriendo" class="text-sm hover:text-white transition">En Arriendo</a></li>
                </ul>
            </div>
            
            <!-- Tipos de Propiedad -->
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Tipos de Propiedad</h4>
                <ul class="space-y-2">
                    <li><a href="propiedades.php?property_type=Casa" class="text-sm hover:text-white transition">Casas</a></li>
                    <li><a href="propiedades.php?property_type=Departamento" class="text-sm hover:text-white transition">Departamentos</a></li>
                    <li><a href="propiedades.php?property_type=Oficina" class="text-sm hover:text-white transition">Oficinas</a></li>
                    <li><a href="propiedades.php?property_type=Terreno" class="text-sm hover:text-white transition">Terrenos</a></li>
                </ul>
            </div>
            
            <!-- Contacto -->
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Contacto</h4>
                <ul class="space-y-2">
                    <li><a href="mailto:contacto@urbangroup.cl" class="text-sm hover:text-white transition">contacto@urbangroup.cl</a></li>
                    <li><a href="tel:+56912345678" class="text-sm hover:text-white transition">+56 9 1234 5678</a></li>
                    <li><a href="nosotros.php" class="text-sm hover:text-white transition">Sobre Nosotros</a></li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-800 pt-8">
            <p class="text-center text-sm">&copy; <?= date('Y') ?> <img src="<?= BASE_URL ?>uploads/logo.png" alt="Urban Group" class="inline-block w-4 h-4 rounded-full align-middle mr-1"> Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<script src="/assets/js/main.js"></script>
</body>
</html>
