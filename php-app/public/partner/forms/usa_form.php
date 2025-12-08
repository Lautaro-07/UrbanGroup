<?php
$existingPhotos = $isEdit ? $photoModel->getByPropertyId($editProperty['id']) : [];
$u = $usaDetails ?? [];
?>

<div class="space-y-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-semibold text-blue-800 mb-4">Informacion Basica</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titulo *</label>
                <input type="text" name="title" required
                       value="<?= htmlspecialchars($editProperty['title'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Propiedad *</label>
                <select name="property_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($usaPropertyTypes as $type): ?>
                    <option value="<?= htmlspecialchars($type['name']) ?>" <?= ($editProperty['property_type'] ?? '') === $type['name'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($type['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio USD *</label>
                <input type="number" name="usa[price_usd]" required min="0" step="1"
                       value="<?= $u['price_usd'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <input type="hidden" name="price" value="<?= $editProperty['price'] ?? 0 ?>">
                <input type="hidden" name="currency" value="USD">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">MLS ID</label>
                <input type="text" name="usa[mls_id]"
                       value="<?= htmlspecialchars($u['mls_id'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: MLS123456">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                <input type="text" name="usa[whatsapp_number]"
                       value="<?= htmlspecialchars($u['whatsapp_number'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="+1 555 123 4567">
            </div>
        </div>
    </div>
    
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="font-semibold text-green-800 mb-4">Ubicacion</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado (State)</label>
                <input type="text" name="usa[state]"
                       value="<?= htmlspecialchars($u['state'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Florida">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad (City)</label>
                <input type="text" name="usa[city]"
                       value="<?= htmlspecialchars($u['city'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Miami">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Zip Code</label>
                <input type="text" name="usa[zip_code]"
                       value="<?= htmlspecialchars($u['zip_code'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: 33101">
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Direccion Completa</label>
            <input type="text" name="address"
                   value="<?= htmlspecialchars($editProperty['address'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Ej: 1234 Ocean Drive, Miami, FL 33101">
        </div>
    </div>
    
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
        <h3 class="font-semibold text-amber-800 mb-4">Caracteristicas</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Superficie (sqft)</label>
                <input type="number" name="usa[surface_sqft]" min="0" step="1"
                       value="<?= $u['surface_sqft'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lote (sqft)</label>
                <input type="number" name="usa[lot_size_sqft]" min="0" step="1"
                       value="<?= $u['lot_size_sqft'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dormitorios</label>
                <input type="number" name="bedrooms" min="0"
                       value="<?= $editProperty['bedrooms'] ?? 0 ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Banos</label>
                <input type="number" name="bathrooms" min="0" step="0.5"
                       value="<?= $editProperty['bathrooms'] ?? 0 ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ano Construccion</label>
                <input type="number" name="usa[year_built]" min="1800" max="2030"
                       value="<?= $u['year_built'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pisos (Stories)</label>
                <input type="number" name="usa[stories]" min="1"
                       value="<?= $u['stories'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Garage (espacios)</label>
                <input type="number" name="usa[garage_spaces]" min="0"
                       value="<?= $u['garage_spaces'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="usa[pool]" value="1" id="pool"
                       <?= !empty($u['pool']) ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="pool" class="text-sm font-medium text-gray-700">Piscina</label>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="usa[waterfront]" value="1" id="waterfront"
                       <?= !empty($u['waterfront']) ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="waterfront" class="text-sm font-medium text-gray-700">Frente al Agua</label>
            </div>
        </div>
    </div>
    
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
        <h3 class="font-semibold text-purple-800 mb-4">Detalles Adicionales</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vista</label>
                <input type="text" name="usa[view_type]"
                       value="<?= htmlspecialchars($u['view_type'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Ocean View, City View">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Calefaccion</label>
                <input type="text" name="usa[heating]"
                       value="<?= htmlspecialchars($u['heating'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Central, Electric">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Aire Acondicionado</label>
                <input type="text" name="usa[cooling]"
                       value="<?= htmlspecialchars($u['cooling'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Central AC">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pisos</label>
                <input type="text" name="usa[flooring]"
                       value="<?= htmlspecialchars($u['flooring'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Hardwood, Tile">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">HOA Fee (mensual USD)</label>
                <input type="number" name="usa[hoa_fee]" min="0" step="0.01"
                       value="<?= $u['hoa_fee'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Property Tax (anual USD)</label>
                <input type="number" name="usa[property_tax]" min="0" step="0.01"
                       value="<?= $u['property_tax'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Electrodomesticos</label>
                <input type="text" name="usa[appliances]"
                       value="<?= htmlspecialchars($u['appliances'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Refrigerator, Washer, Dryer">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Caracteristicas Exteriores</label>
                <input type="text" name="usa[exterior_features]"
                       value="<?= htmlspecialchars($u['exterior_features'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Patio, Deck, Fence">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Caracteristicas Interiores</label>
                <input type="text" name="usa[interior_features]"
                       value="<?= htmlspecialchars($u['interior_features'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Walk-in Closet, High Ceilings">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amenidades Comunidad</label>
                <input type="text" name="usa[community_features]"
                       value="<?= htmlspecialchars($u['community_features'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Gym, Pool, Security">
            </div>
        </div>
    </div>
    
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-indigo-800">Seccion Proyecto</h3>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="usa[is_project]" value="1" id="is_project"
                       <?= !empty($u['is_project']) ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                       onchange="document.getElementById('project_fields').classList.toggle('hidden', !this.checked)">
                <span class="text-sm text-gray-700">Es un Proyecto</span>
            </label>
        </div>
        <div id="project_fields" class="<?= empty($u['is_project']) ? 'hidden' : '' ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° de Unidades</label>
                    <input type="number" name="usa[project_units]" min="0"
                           value="<?= $u['project_units'] ?? '' ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desarrollador</label>
                    <input type="text" name="usa[project_developer]"
                           value="<?= htmlspecialchars($u['project_developer'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Entrega</label>
                    <input type="date" name="usa[project_completion_date]"
                           value="<?= htmlspecialchars($u['project_completion_date'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Amenidades del Proyecto</label>
                <textarea name="usa[project_amenities]" rows="2"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Ej: Rooftop Pool, Gym, Concierge, Valet Parking..."><?= htmlspecialchars($u['project_amenities'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
    
    <div class="border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Descripcion</h3>
        <textarea name="description" rows="5"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="Describe la propiedad en detalle..."><?= htmlspecialchars($editProperty['description'] ?? '') ?></textarea>
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
