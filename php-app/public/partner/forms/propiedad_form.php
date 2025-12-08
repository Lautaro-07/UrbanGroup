<?php
$existingPhotos = $isEdit ? $photoModel->getByPropertyId($editProperty['id']) : [];
$details = $editPropertyDetails['details'] ?? [];
$features = $editPropertyDetails['features'] ?? [];
?>

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Titulo *</label>
            <input type="text" name="title" required
                   value="<?= htmlspecialchars($editProperty['title'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Propiedad *</label>
            <select name="property_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Seleccionar...</option>
                <?php foreach ($propertyTypes as $type): ?>
                    <?php if (empty($type['is_usa'])): ?>
                    <option value="<?= htmlspecialchars($type['name']) ?>" <?= ($editProperty['property_type'] ?? '') === $type['name'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($type['name']) ?>
                    </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Operacion</label>
            <select name="operation_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="Venta" <?= ($editProperty['operation_type'] ?? 'Venta') === 'Venta' ? 'selected' : '' ?>>Venta</option>
                <option value="Arriendo" <?= ($editProperty['operation_type'] ?? '') === 'Arriendo' ? 'selected' : '' ?>>Arriendo</option>
                <option value="Arriendo Temporal" <?= ($editProperty['operation_type'] ?? '') === 'Arriendo Temporal' ? 'selected' : '' ?>>Arriendo Temporal</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
            <select name="property_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Sin categoria</option>
                <option value="residencial" <?= ($editProperty['property_category'] ?? '') === 'residencial' ? 'selected' : '' ?>>Residencial</option>
                <option value="comercial" <?= ($editProperty['property_category'] ?? '') === 'comercial' ? 'selected' : '' ?>>Comercial</option>
                <option value="industrial" <?= ($editProperty['property_category'] ?? '') === 'industrial' ? 'selected' : '' ?>>Industrial</option>
            </select>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
            <input type="number" name="price" required min="0" step="1"
                   value="<?= $editProperty['price'] ?? '' ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Moneda</label>
            <select name="currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="CLP" <?= ($editProperty['currency'] ?? 'CLP') === 'CLP' ? 'selected' : '' ?>>CLP (Pesos)</option>
                <option value="UF" <?= ($editProperty['currency'] ?? '') === 'UF' ? 'selected' : '' ?>>UF</option>
                <option value="USD" <?= ($editProperty['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD</option>
            </select>
        </div>
    </div>
    
    <div class="border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ubicacion</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                <select name="region_id" id="region_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar Region...</option>
                    <?php foreach ($regions as $region): ?>
                    <option value="<?= $region['id'] ?>" <?= ($editProperty['region_id'] ?? '') == $region['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($region['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comuna</label>
                <select name="comuna_id" id="comuna_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Primero seleccione Region...</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Direccion</label>
            <input type="text" name="address"
                   value="<?= htmlspecialchars($editProperty['address'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Ej: Av. Providencia 1234, Depto 501">
        </div>
    </div>
    
    <div class="border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Caracteristicas</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dormitorios</label>
                <input type="number" name="bedrooms" min="0"
                       value="<?= $editProperty['bedrooms'] ?? 0 ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Banos</label>
                <input type="number" name="bathrooms" min="0"
                       value="<?= $editProperty['bathrooms'] ?? 0 ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Construida (m²)</label>
                <input type="number" name="built_area" min="0" step="0.01"
                       value="<?= $editProperty['built_area'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Total (m²)</label>
                <input type="number" name="total_area" min="0" step="0.01"
                       value="<?= $editProperty['total_area'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estacionamientos</label>
                <input type="number" name="parking_spots" min="0"
                       value="<?= $editProperty['parking_spots'] ?? 0 ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>
    
    <div class="border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Descripcion</h3>
        <textarea name="description" rows="5"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="Describe la propiedad..."><?= htmlspecialchars($editProperty['description'] ?? '') ?></textarea>
    </div>
    
    <div class="border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Fotos (max. 12)</h3>
        
        <?php if (!empty($existingPhotos)): ?>
        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-2">Fotos actuales (<?= count($existingPhotos) ?>):</p>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                <?php foreach ($existingPhotos as $idx => $photo): ?>
                <div class="relative group">
                    <img src="<?= htmlspecialchars($photo['photo_url']) ?>" alt="Foto <?= $idx + 1 ?>" 
                         class="w-full h-20 object-cover rounded border">
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-1">
                        <?php if ($idx > 0): ?>
                        <a href="?action=edit&id=<?= $editProperty['id'] ?>&move_photo=<?= $photo['id'] ?>&move_to=<?= $idx - 1 ?>" 
                           class="text-white text-xs px-1 bg-blue-600 rounded">&larr;</a>
                        <?php endif; ?>
                        <?php if ($idx < count($existingPhotos) - 1): ?>
                        <a href="?action=edit&id=<?= $editProperty['id'] ?>&move_photo=<?= $photo['id'] ?>&move_to=<?= $idx + 1 ?>" 
                           class="text-white text-xs px-1 bg-blue-600 rounded">&rarr;</a>
                        <?php endif; ?>
                    </div>
                    <label class="absolute bottom-0 right-0 bg-red-600 text-white text-xs px-1 cursor-pointer">
                        <input type="checkbox" name="delete_photos[]" value="<?= $photo['id'] ?>" class="hidden">
                        X
                    </label>
                    <?php if ($idx === 0): ?>
                    <span class="absolute top-0 left-0 bg-blue-600 text-white text-xs px-1">Principal</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="text-xs text-gray-500 mt-2">Marca las fotos que deseas eliminar. Arrastra para reordenar.</p>
        </div>
        <?php endif; ?>
        
        <input type="file" name="property_photos[]" multiple accept="image/jpeg,image/png"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">JPG o PNG. La primera foto sera la principal.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region_id');
    const comunaSelect = document.getElementById('comuna_id');
    const currentComunaId = '<?= $editProperty['comuna_id'] ?? '' ?>';
    
    function loadComunas(regionId) {
        if (!regionId) {
            comunaSelect.innerHTML = '<option value="">Primero seleccione Region...</option>';
            return;
        }
        
        fetch('/api/comunas.php?region_id=' + regionId)
            .then(response => response.json())
            .then(data => {
                comunaSelect.innerHTML = '<option value="">Seleccionar Comuna...</option>';
                data.forEach(comuna => {
                    const option = document.createElement('option');
                    option.value = comuna.id;
                    option.textContent = comuna.name;
                    if (comuna.id == currentComunaId) option.selected = true;
                    comunaSelect.appendChild(option);
                });
            })
            .catch(() => {
                comunaSelect.innerHTML = '<option value="">Error al cargar comunas</option>';
            });
    }
    
    regionSelect.addEventListener('change', function() {
        loadComunas(this.value);
    });
    
    if (regionSelect.value) {
        loadComunas(regionSelect.value);
    }
});
</script>
