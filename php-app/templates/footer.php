<!-- Footer -->
<footer class="bg-gray-900 text-gray-400 border-t border-gray-800 mt-16 lg:mt-20">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Brand -->
            <div>
                <a href="/" class="flex items-center gap-2 text-white font-bold text-lg mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"/>
                        <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"/>
                    </svg>
                    Urban Group
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
            <p class="text-center text-sm">&copy; <?= date('Y') ?> Urban Group. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<script src="/assets/js/main.js"></script>
</body>
</html>
