<?php
$existingPhotos = $isEdit ? $photoModel->getByPropertyId($editProperty['id']) : [];
$details = $editPropertyDetails['details'] ?? [];
?>

<div class="space-y-6">
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
        <h3 class="font-semibold text-amber-800 mb-4">Informacion del Activo</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titulo *</label>
                <input type="text" name="title" required
                       value="<?= htmlspecialchars($editProperty['title'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Excavadora CAT 320">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Activo *</label>
                <select name="property_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    <option value="Maquinaria" <?= ($editProperty['property_type'] ?? '') === 'Maquinaria' ? 'selected' : '' ?>>Maquinaria</option>
                    <option value="Vehiculo" <?= ($editProperty['property_type'] ?? '') === 'Vehiculo' ? 'selected' : '' ?>>Vehiculo</option>
                    <option value="Equipo Industrial" <?= ($editProperty['property_type'] ?? '') === 'Equipo Industrial' ? 'selected' : '' ?>>Equipo Industrial</option>
                    <option value="Mobiliario" <?= ($editProperty['property_type'] ?? '') === 'Mobiliario' ? 'selected' : '' ?>>Mobiliario</option>
                    <option value="Equipo Tecnologico" <?= ($editProperty['property_type'] ?? '') === 'Equipo Tecnologico' ? 'selected' : '' ?>>Equipo Tecnologico</option>
                    <option value="Otros" <?= ($editProperty['property_type'] ?? '') === 'Otros' ? 'selected' : '' ?>>Otros</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                <select name="property_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Sin categoria</option>
                    <option value="construccion" <?= ($editProperty['property_category'] ?? '') === 'construccion' ? 'selected' : '' ?>>Construccion</option>
                    <option value="mineria" <?= ($editProperty['property_category'] ?? '') === 'mineria' ? 'selected' : '' ?>>Mineria</option>
                    <option value="agricola" <?= ($editProperty['property_category'] ?? '') === 'agricola' ? 'selected' : '' ?>>Agricola</option>
                    <option value="transporte" <?= ($editProperty['property_category'] ?? '') === 'transporte' ? 'selected' : '' ?>>Transporte</option>
                    <option value="industrial" <?= ($editProperty['property_category'] ?? '') === 'industrial' ? 'selected' : '' ?>>Industrial</option>
                    <option value="oficina" <?= ($editProperty['property_category'] ?? '') === 'oficina' ? 'selected' : '' ?>>Oficina</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                <input type="text" name="brand"
                       value="<?= htmlspecialchars($details['brand'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Caterpillar, Toyota, etc.">
            </div>
        </div>
    </div>
    
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="font-semibold text-green-800 mb-4">Precio y Condicion</h3>
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Condicion</label>
                <select name="asset_condition" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    <option value="Nuevo" <?= ($details['asset_condition'] ?? '') === 'Nuevo' ? 'selected' : '' ?>>Nuevo</option>
                    <option value="Excelente" <?= ($details['asset_condition'] ?? '') === 'Excelente' ? 'selected' : '' ?>>Excelente</option>
                    <option value="Bueno" <?= ($details['asset_condition'] ?? '') === 'Bueno' ? 'selected' : '' ?>>Bueno</option>
                    <option value="Regular" <?= ($details['asset_condition'] ?? '') === 'Regular' ? 'selected' : '' ?>>Regular</option>
                    <option value="Para reparar" <?= ($details['asset_condition'] ?? '') === 'Para reparar' ? 'selected' : '' ?>>Para Reparar</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Operacion</label>
            <select name="operation_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="Venta" <?= ($editProperty['operation_type'] ?? 'Venta') === 'Venta' ? 'selected' : '' ?>>Venta</option>
                <option value="Arriendo" <?= ($editProperty['operation_type'] ?? '') === 'Arriendo' ? 'selected' : '' ?>>Arriendo</option>
            </select>
        </div>
    </div>
    
    <div class="border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Descripcion</h3>
        <textarea name="description" rows="5"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="Describe el activo en detalle: modelo, ano, horas de uso, especificaciones tecnicas, etc."><?= htmlspecialchars($editProperty['description'] ?? '') ?></textarea>
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
            <p class="text-xs text-gray-500 mt-2">Marca las fotos que deseas eliminar.</p>
        </div>
        <?php endif; ?>
        
        <input type="file" name="property_photos[]" multiple accept="image/jpeg,image/png"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">JPG o PNG. La primera foto sera la principal.</p>
    </div>
</div>
